<?php
// This class represents the league and handles all logic related to simulating matches,
// tracking results, managing the state, and generating championship predictions.

namespace App\Model;

require_once __DIR__ .'/../Service/Generated/Team.php';
require_once __DIR__ .'/../Service/Generated/TeamsRequest.php';
require_once __DIR__ . '/../Service/grpc_client.php';

use App\Service\PredictionServiceClient;
use Redis;
use Generated\TeamsRequest;
use Generated\Team;


class League {
    private $teams;
    private $matches = [];
    private $round = 0;
    private $isFinished = false;
    private $totalRounds;
    private $redis;

    public function __construct($teams) {
        $this->redis = new Redis();
        $this->redis->connect('redis-league', 6379);
        $this->teams = $teams;
        $this->totalRounds = (count($teams) - 1) * 2; // Two rounds for home and away matches.
        $this->loadState();
    }

    // Loads the league's state from Redis.
    public function loadState() {
        foreach ($this->teams as $team) {
            $teamData = $this->redis->hGetAll('team:' . $team->getName());
            if (!empty($teamData)) {
                $team->setPoints($teamData['points']);
                $team->setPlayed($teamData['played']);
                $team->setWins($teamData['wins']);
                $team->setDraws($teamData['draws']);
                $team->setLosses($teamData['losses']);
                $team->setGoalDifference($teamData['goalDifference']);
            }
        }
        $this->matches = json_decode($this->redis->get('matches'), true) ?? [];
        $this->round = (int) $this->redis->get('round') ?? 0;
        $this->isFinished = (bool) $this->redis->get('isFinished') ?? false;
    }

    // Saves the league's state to Redis.
    public function saveState() {
        foreach ($this->teams as $team) {
            $this->redis->hMSet('team:' . $team->getName(), [
                'points' => $team->getPoints(),
                'played' => $team->getPlayed(),
                'wins' => $team->getWins(),
                'draws' => $team->getDraws(),
                'losses' => $team->getLosses(),
                'goalDifference' => $team->getGoalDifference(),
            ]);
        }
        $this->redis->set('matches', json_encode($this->matches));
        $this->redis->set('round', $this->round);
        $this->redis->set('isFinished', $this->isFinished);
    }

    // Resets the league to its initial state.
    public function resetLeague() {
        foreach ($this->teams as $team) {
            $this->redis->del('team:' . $team->getName());
        }
        $this->redis->del('matches');
        $this->redis->del('round');
        $this->redis->del('isFinished');

        $this->matches = [];
        $this->round = 0;
        $this->isFinished = false;

        foreach ($this->teams as $team) {
            $team->resetStatistics();
        }

        $this->saveState();
    }

    // Simulates the next round of matches in the league.
    public function simulateNextRound() {
        if ($this->isFinished) {
            return;
        }

        $teamsCount = count($this->teams);

        if ($this->round >= $this->totalRounds) {
            $this->isFinished = true;
            $this->saveState();
            return;
        }

        for ($i = 0; $i < $teamsCount; $i += 2) {
            if ($i + 1 < $teamsCount) {
                $team1 = $this->teams[$i];
                $team2 = $this->teams[$i + 1];

                if ($this->round % 2 == 0) {
                    $this->simulateMatch($team1, $team2);
                } else {
                    $this->simulateMatch($team2, $team1);
                }
            }
        }

        $this->round++;
        $this->saveState();
    }

    // Simulates a match between two teams.
    public function simulateMatch($team1, $team2) {
        $strengthDifference = $team1->getStrength() - $team2->getStrength();
        $goalsTeam1 = max(0, rand(0, 5) + $strengthDifference);
        $goalsTeam2 = max(0, rand(0, 5) - $strengthDifference);

        $team1->addMatchResult($goalsTeam1, $goalsTeam2);
        $team2->addMatchResult($goalsTeam2, $goalsTeam1);

        $this->matches[] = [
            'team1' => $team1->getName(),
            'team2' => $team2->getName(),
            'score' => "$goalsTeam1 - $goalsTeam2",
        ];

        $this->saveState();
    }

    // Generates the league table sorted by points and goal difference.
    public function getLeagueTable() {
        usort($this->teams, function ($teamA, $teamB) {
            if ($teamA->getPoints() == $teamB->getPoints()) {
                return $teamB->getGoalDifference() - $teamA->getGoalDifference();
            }
            return $teamB->getPoints() - $teamA->getPoints();
        });

        return array_map(function ($team) {
            return [
                'name' => $team->getName(),
                'points' => $team->getPoints(),
                'played' => $team->getPlayed(),
                'wins' => $team->getWins(),
                'draws' => $team->getDraws(),
                'losses' => $team->getLosses(),
                'goalDifference' => $team->getGoalDifference(),
            ];
        }, $this->teams);
    }

    // Returns the list of match results.
    public function getMatchResults() {
        return $this->matches;
    }

    // Checks if the league is finished.
    public function isFinished() {
        return $this->isFinished;
    }

    // Returns the champion if the league is finished.
    public function getChampion() {
        if (!$this->isFinished) {
            return null;
        }

        usort($this->teams, function ($teamA, $teamB) {
            if ($teamA->getPoints() == $teamB->getPoints()) {
                return $teamB->getGoalDifference() - $teamA->getGoalDifference();
            }
            return $teamB->getPoints() - $teamA->getPoints();
        });

        return $this->teams[0]->getName();
    }

    // Generates championship predictions based on current points.
    public function getChampionshipPredictions() {
        $client = new PredictionServiceClient("grpc-ai:50051", [
            'credentials' => \Grpc\ChannelCredentials::createInsecure(),
        ]);

        $teams = [
            new Team(['name' => 'Chelsea', 'points' => 10]),
            new Team(['name' => 'Arsenal', 'points' => 8]),
            new Team(['name' => 'Manchester City', 'points' => 12]),
            new Team(['name' => 'Liverpool', 'points' => 6]),
        ];

        $request = new TeamsRequest();
        $request->setTeams($teams);

        // send a request and receive a response
        list($response, $status) = $client->GetChampionshipPredictions($request)->wait();

        if ($status->code === \Grpc\STATUS_OK) {
            $predictions = [];
            foreach ($response->getPredictions() as $prediction) {
                $predictions[] = [
                    'team' => $prediction->getTeam(),
                    'prediction' => $prediction->getPrediction(),
                ];
            }
            return $predictions;
        } else {
            return ['error' => 'gRPC Error: ' . $status->details];
        }
    }


    // Adjust team strength by calling AI microservice after a championship is over
    public function adjustTeamStrength() {
        // Prepare teams data
        $teams = [];
        foreach ($this->teams as $team) {
            $teams[] = [
                'name' => $team->getName(),
                'strength' => $team->getStrength(),
            ];
        }

        // Call the gRPC AI microservice
        $adjustedTeams = adjustTeamStrength($teams);

        // Update team strengths based on AI service response
        foreach ($adjustedTeams as $adjustedTeam) {
            foreach ($this->teams as $team) {
                if ($team->getName() == $adjustedTeam->getName()) {
                    $team->setStrength($adjustedTeam->getStrength());
                }
            }
        }

        $this->saveState();
    }
}
