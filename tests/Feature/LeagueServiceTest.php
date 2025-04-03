<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\GameMatch;
use App\Models\LeagueTable;
use App\Services\LeagueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeagueServiceTest extends TestCase
{
    use RefreshDatabase;

    private LeagueService $leagueService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->leagueService = new LeagueService();
    }

    public function test_can_generate_fixtures(): void
    {
        // Create 4 teams
        $teams = [];
        for ($i = 1; $i <= 4; $i++) {
            $teams[] = Team::create([
                'name' => "Team {$i}",
                'strength' => 75
            ]);
        }

        $this->leagueService->generateFixtures();

        $this->assertEquals(6, GameMatch::count());
        $this->assertEquals(3, GameMatch::distinct()->count('week'));
    }

    public function test_can_simulate_match(): void
    {
        $homeTeam = Team::create([
            'name' => 'Home Team',
            'strength' => 75
        ]);

        $awayTeam = Team::create([
            'name' => 'Away Team',
            'strength' => 80
        ]);

        LeagueTable::create(['team_id' => $homeTeam->id]);
        LeagueTable::create(['team_id' => $awayTeam->id]);

        $match = GameMatch::create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'week' => 1
        ]);

        $this->leagueService->simulateMatch($match);

        $match->refresh();

        $this->assertTrue($match->played);
        $this->assertNotNull($match->home_team_score);
        $this->assertNotNull($match->away_team_score);
    }

    public function test_league_table_updates_after_match(): void
    {
        $homeTeam = Team::create([
            'name' => 'Home Team',
            'strength' => 75
        ]);

        $awayTeam = Team::create([
            'name' => 'Away Team',
            'strength' => 80
        ]);

        $homeTable = LeagueTable::create(['team_id' => $homeTeam->id]);
        $awayTable = LeagueTable::create(['team_id' => $awayTeam->id]);

        $match = GameMatch::create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'week' => 1,
            'home_team_score' => 2,
            'away_team_score' => 1,
            'played' => true
        ]);

        $this->leagueService->updateLeagueTable($homeTeam, $awayTeam, 2, 1);

        $homeTable->refresh();
        $awayTable->refresh();

        $this->assertEquals(1, $homeTable->played);
        $this->assertEquals(1, $homeTable->won);
        $this->assertEquals(0, $homeTable->drawn);
        $this->assertEquals(0, $homeTable->lost);
        $this->assertEquals(2, $homeTable->goals_for);
        $this->assertEquals(1, $homeTable->goals_against);
        $this->assertEquals(1, $homeTable->goal_difference);
        $this->assertEquals(3, $homeTable->points);

        $this->assertEquals(1, $awayTable->played);
        $this->assertEquals(0, $awayTable->won);
        $this->assertEquals(0, $awayTable->drawn);
        $this->assertEquals(1, $awayTable->lost);
        $this->assertEquals(1, $awayTable->goals_for);
        $this->assertEquals(2, $awayTable->goals_against);
        $this->assertEquals(-1, $awayTable->goal_difference);
        $this->assertEquals(0, $awayTable->points);
    }

    public function test_championship_predictions(): void
    {
        $teams = [
            Team::create(['name' => 'Team 1', 'strength' => 90]),
            Team::create(['name' => 'Team 2', 'strength' => 80]),
            Team::create(['name' => 'Team 3', 'strength' => 70]),
            Team::create(['name' => 'Team 4', 'strength' => 60])
        ];

        foreach ($teams as $team) {
            LeagueTable::create([
                'team_id' => $team->id,
                'points' => 10,
                'goal_difference' => 5
            ]);
        }

        $predictions = $this->leagueService->predictChampionship();

        $this->assertEquals($teams[0]->name, array_key_first($predictions));
    }
}
