<?php

class League {
    private $teams;
    private $matches = [];
    private $round = 0; // Отслеживаем текущий круг
    private $isFinished = false; // Флаг завершения чемпионата
    private $totalRounds; // Общее количество раундов (игр дома и на выезде)
    private $redis;

    public function __construct($teams) {
        $this->redis = new Redis();
        $this->redis->connect('redis-league', 6379);
        $this->teams = $teams;
        $this->totalRounds = (count($teams) - 1) * 2; // Два круга: дома и в гостях
        $this->loadState();
    }

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

    // Симуляция одного круга матчей (каждая команда играет один матч за круг)
    public function simulateNextRound() {
        if ($this->isFinished) {
            return; // Чемпионат завершен, больше матчей не проводим
        }

        $teamsCount = count($this->teams);

        // Если все команды уже сыграли все матчи
        if ($this->round >= $this->totalRounds) {
            $this->isFinished = true; // Завершаем чемпионат
            $this->saveState();
            return;
        }

        // Получаем пары команд для текущего круга
        for ($i = 0; $i < $teamsCount; $i += 2) {
            if ($i + 1 < $teamsCount) {
                $team1 = $this->teams[$i];
                $team2 = $this->teams[$i + 1];

                // В первом круге команда 1 играет дома, во втором — команда 2
                if ($this->round % 2 == 0) {
                    $this->simulateMatch($team1, $team2);
                } else {
                    $this->simulateMatch($team2, $team1);
                }
            }
        }

        // Увеличиваем счетчик круга
        $this->round++;
        $this->saveState();
    }

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

    public function getMatchResults() {
        return $this->matches;
    }

    public function isFinished() {
        return $this->isFinished;
    }

    public function getChampion() {
        if (!$this->isFinished) {
            return null; // Чемпионат еще не завершен
        }

        // Возвращаем команду с наибольшим количеством очков
        usort($this->teams, function ($teamA, $teamB) {
            if ($teamA->getPoints() == $teamB->getPoints()) {
                return $teamB->getGoalDifference() - $teamA->getGoalDifference();
            }
            return $teamB->getPoints() - $teamA->getPoints();
        });

        return $this->teams[0]->getName(); // Команда на первом месте
    }

    public function getChampionshipPredictions() {
        $totalPoints = array_sum(array_map(function ($team) {
            return $team->getPoints();
        }, $this->teams));

        if ($totalPoints == 0) {
            return array_map(function ($team) {
                return [
                    'team' => $team->getName(),
                    'prediction' => '0%',
                ];
            }, $this->teams);
        }

        return array_map(function ($team) use ($totalPoints) {
            return [
                'team' => $team->getName(),
                'prediction' => round(($team->getPoints() / $totalPoints) * 100, 2) . '%',
            ];
        }, $this->teams);
    }

    public function resetLeague() {
        // Очищаем Redis
        foreach ($this->teams as $team) {
            $this->redis->del('team:' . $team->getName());
        }
        $this->redis->del('matches');
        $this->redis->del('round');
        $this->redis->del('isFinished');

        // Сбрасываем локальные данные
        $this->matches = [];
        $this->round = 0;
        $this->isFinished = false;

        // Сбрасываем данные команд
        foreach ($this->teams as $team) {
            $team->resetStatistics();
        }

        $this->saveState();
    }
}
