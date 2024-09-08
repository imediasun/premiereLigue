<?php
// This controller handles league-related API requests such as fetching the table,
// simulating rounds, and resetting the league.

namespace App\Controller;

use App\Model\Team;
use App\Model\League;

class LeagueController {
    private $league;

    public function __construct() {
        // Initialize teams and set up the league.
        $chelsea = new Team("Chelsea", 5);
        $arsenal = new Team("Arsenal", 4);
        $manCity = new Team("Manchester City", 5);
        $liverpool = new Team("Liverpool", 3);

        $this->league = new League([$chelsea, $arsenal, $manCity, $liverpool]);
    }

    // Fetches the current state of the league table and predictions.
    public function getTable() {
        header('Content-Type: application/json');
        echo json_encode([
            'leagueTable' => $this->league->getLeagueTable(),
            'predictions' => $this->league->getChampionshipPredictions(),
            'isFinished' => $this->league->isFinished(),
            'champion' => $this->league->getChampion(),
        ]);
    }

    // Simulates the next round of matches in the league.
    public function playNextRound() {
        $this->league->simulateNextRound();

        header('Content-Type: application/json');
        echo json_encode([
            'leagueTable' => $this->league->getLeagueTable(),
            'matchResults' => $this->league->getMatchResults(),
            'predictions' => $this->league->getChampionshipPredictions(),
            'isFinished' => $this->league->isFinished(),
            'champion' => $this->league->getChampion(),
        ]);
    }

    // Resets the league by clearing all data and starting fresh.
    public function resetLeague() {
        $this->league->resetLeague();

        header('Content-Type: application/json');
        echo json_encode([
            'message' => 'League reset successfully.',
        ]);
    }
}
