<?php

use PHPUnit\Framework\TestCase;
use App\Model\League;
use App\Model\Team;
use Redis;

class LeagueTest extends TestCase
{
    private $teams;
    private $league;

    protected function setUp(): void
    {
        // Создаем экземпляры команд
        $chelsea = new Team('Chelsea', 10);
        $arsenal = new Team('Arsenal', 8);
        $manCity = new Team('Manchester City', 12);
        $liverpool = new Team('Liverpool', 6);

        // Инициализируем команды
        $this->teams = [$chelsea, $arsenal, $manCity, $liverpool];
        $this->league = new League($this->teams);

        // Очищаем Redis перед каждым тестом
        $redis = new Redis();
        $redis->connect('redis-league', 6379);
        $redis->flushAll();
    }

    public function testLeagueInitialization()
    {
        $this->assertCount(4, $this->league->getLeagueTable(), 'There should be 4 teams in the league.');
    }

    public function testSimulateNextRound()
    {
        $this->league->simulateNextRound();
        $table = $this->league->getLeagueTable();

        // Проверяем, что у каждой команды появилась хотя бы одна сыгранная игра
        foreach ($table as $team) {
            $this->assertGreaterThanOrEqual(1, $team['played'], 'Each team should have at least one game played.');
        }
    }

    public function testGetChampion()
    {
        // Симулируем все раунды
        for ($i = 0; $i < 6; $i++) {
            $this->league->simulateNextRound();
        }

        $this->assertTrue($this->league->isFinished(), 'The league should be finished.');
        $this->assertNotNull($this->league->getChampion(), 'The league should have a champion.');
    }

    public function testGetChampionshipPredictions()
    {
        // Прогнозы на основе текущих очков
        $predictions = $this->league->getChampionshipPredictions();

        $this->assertIsArray($predictions, 'Predictions should return an array.');
        $this->assertCount(4, $predictions, 'There should be predictions for 4 teams.');
    }

    public function testResetLeague()
    {
        // Сбрасываем лигу
        $this->league->resetLeague();
        $table = $this->league->getLeagueTable();

        // Убедимся, что все данные сброшены
        foreach ($table as $team) {
            $this->assertEquals(0, $team['points'], 'Points should be reset to 0.');
            $this->assertEquals(0, $team['played'], 'Games played should be reset to 0.');
        }
    }
}
