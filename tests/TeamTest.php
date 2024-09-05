<?php

use PHPUnit\Framework\TestCase;

class TeamTest extends TestCase {
public function testAddMatchResult() {
$team = new Team("Chelsea", 5);
$team->addMatchResult(3, 1);
$this->assertEquals(3, $team->getPoints());
$this->assertEquals(2, $team->getGoalDifference());
}

public function testDrawMatch() {
$team = new Team("Arsenal", 4);
$team->addMatchResult(1, 1);
$this->assertEquals(1, $team->getPoints());
$this->assertEquals(0, $team->getGoalDifference());
}
}
