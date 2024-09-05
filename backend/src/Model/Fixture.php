<?php

class Fixture {
    private $team1;
    private $team2;
    private $score1;
    private $score2;

    public function __construct(Team $team1, Team $team2, $score1, $score2) {
        $this->team1 = $team1;
        $this->team2 = $team2;
        $this->score1 = $score1;
        $this->score2 = $score2;
    }

    // Возвращает результат матча
    public function getResult() {
        return "{$this->team1->getName()} {$this->score1} - {$this->score2} {$this->team2->getName()}";
    }

    // Обновляет статистику команд после матча
    public function updateTeamStatistics() {
        $this->team1->addMatchResult($this->score1, $this->score2);
        $this->team2->addMatchResult($this->score2, $this->score1);
    }
}
