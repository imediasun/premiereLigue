<?php

require_once '../src/Model/Team.php';
require_once '../src/Model/League.php';

class LeagueController {
    private $league;

    public function __construct() {
        $chelsea = new Team("Chelsea", 5);
        $arsenal = new Team("Arsenal", 4);
        $manCity = new Team("Manchester City", 5);
        $liverpool = new Team("Liverpool", 3);

        $this->league = new League([$chelsea, $arsenal, $manCity, $liverpool]);
    }

    public function getTable() {
        header('Content-Type: application/json');
        echo json_encode([
            'leagueTable' => $this->league->getLeagueTable(),
            'predictions' => $this->league->getChampionshipPredictions(),
            'isFinished' => $this->league->isFinished(),
            'champion' => $this->league->getChampion(),
        ]);
    }

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

    public function resetLeague() {
        $this->league->resetLeague();

        header('Content-Type: application/json');
        echo json_encode([
            'message' => 'League reset successfully.',
        ]);
    }
}
