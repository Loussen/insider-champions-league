<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\LeagueTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_team(): void
    {
        $teamData = [
            'name' => 'Test Team',
            'strength' => 75
        ];

        $team = Team::create($teamData);

        $this->assertDatabaseHas('teams', $teamData);
        $this->assertEquals($teamData['name'], $team->name);
        $this->assertEquals($teamData['strength'], $team->strength);
    }

    public function test_team_has_league_table(): void
    {
        $team = Team::create([
            'name' => 'Test Team',
            'strength' => 75
        ]);

        LeagueTable::create([
            'team_id' => $team->id
        ]);

        $this->assertInstanceOf(LeagueTable::class, $team->leagueTable);
        $this->assertEquals($team->id, $team->leagueTable->team_id);
    }

    public function test_team_strength_is_within_valid_range(): void
    {
        $team = Team::create([
            'name' => 'Test Team',
            'strength' => 150 // Invalid strength
        ]);

        $this->assertLessThanOrEqual(100, $team->strength);
        $this->assertGreaterThanOrEqual(1, $team->strength);
    }

    public function test_team_can_have_matches(): void
    {
        $homeTeam = Team::create([
            'name' => 'Home Team',
            'strength' => 75
        ]);

        $awayTeam = Team::create([
            'name' => 'Away Team',
            'strength' => 80
        ]);

        $match = $homeTeam->homeMatches()->create([
            'away_team_id' => $awayTeam->id,
            'week' => 1
        ]);

        $this->assertCount(1, $homeTeam->homeMatches);
        $this->assertEquals($awayTeam->id, $homeTeam->homeMatches->first()->away_team_id);
    }
}
