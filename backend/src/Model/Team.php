<?php
// src/Model/Team.php

class Team {
    private $name;
    private $strength;
    private $points = 0;
    private $played = 0;
    private $wins = 0;
    private $draws = 0;
    private $losses = 0;
    private $goalDifference = 0;

    public function __construct($name, $strength) {
        $this->name = $name;
        $this->strength = $strength;
    }

    public function getName() {
        return $this->name;
    }

    public function getPoints() {
        return $this->points;
    }

    public function setPoints($points) {
        $this->points = $points;
    }

    public function getPlayed() {
        return $this->played;
    }

    public function setPlayed($played) {
        $this->played = $played;
    }

    public function getWins() {
        return $this->wins;
    }

    public function setWins($wins) {
        $this->wins = $wins;
    }

    public function getDraws() {
        return $this->draws;
    }

    public function setDraws($draws) {
        $this->draws = $draws;
    }

    public function getLosses() {
        return $this->losses;
    }

    public function setLosses($losses) {
        $this->losses = $losses;
    }

    public function getGoalDifference() {
        return $this->goalDifference;
    }

    public function setGoalDifference($goalDifference) {
        $this->goalDifference = $goalDifference;
    }

    public function getStrength() {
        return $this->strength;
    }

    public function addMatchResult($goalsFor, $goalsAgainst) {
        $this->played++;
        $this->goalDifference += ($goalsFor - $goalsAgainst);
        if ($goalsFor > $goalsAgainst) {
            $this->wins++;
            $this->points += 3;
        } elseif ($goalsFor == $goalsAgainst) {
            $this->draws++;
            $this->points += 1;
        } else {
            $this->losses++;
        }
    }

    public function resetStatistics() {
        $this->points = 0;
        $this->played = 0;
        $this->wins = 0;
        $this->draws = 0;
        $this->losses = 0;
        $this->goalDifference = 0;
    }
}
