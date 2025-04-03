<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\GameMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameMatchTest extends TestCase
{
    use RefreshDatabase;

    private Team $homeTeam;
    private Team $awayTeam;

    protected function setUp(): void
    {
        parent::setUp();

        $this->homeTeam = Team::create([
            'name' => 'Home Team',
            'strength' => 75
        ]);

        $this->awayTeam = Team::create([
            'name' => 'Away Team',
            'strength' => 80
        ]);
    }

    public function test_can_create_match(): void
    {
        $matchData = [
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'week' => 1
        ];

        $match = GameMatch::create($matchData);

        $this->assertDatabaseHas('matches', $matchData);
        $this->assertEquals($matchData['home_team_id'], $match->home_team_id);
        $this->assertEquals($matchData['away_team_id'], $match->away_team_id);
        $this->assertEquals($matchData['week'], $match->week);
        $this->assertFalse($match->played);
    }

    public function test_match_has_team_relationships(): void
    {
        $match = GameMatch::create([
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'week' => 1
        ]);

        $this->assertInstanceOf(Team::class, $match->homeTeam);
        $this->assertInstanceOf(Team::class, $match->awayTeam);
        $this->assertEquals($this->homeTeam->id, $match->homeTeam->id);
        $this->assertEquals($this->awayTeam->id, $match->awayTeam->id);
    }

    public function test_can_update_match_score(): void
    {
        $match = GameMatch::create([
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'week' => 1
        ]);

        $match->update([
            'home_team_score' => 2,
            'away_team_score' => 1,
            'played' => true
        ]);

        $this->assertTrue($match->played);
        $this->assertEquals(2, $match->home_team_score);
        $this->assertEquals(1, $match->away_team_score);
    }

    public function test_cannot_play_same_team_against_itself(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('A team cannot play against itself');

        GameMatch::create([
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->homeTeam->id,
            'week' => 1
        ]);
    }

    public function test_can_get_matches_by_week(): void
    {
        GameMatch::create([
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'week' => 1
        ]);

        GameMatch::create([
            'home_team_id' => $this->awayTeam->id,
            'away_team_id' => $this->homeTeam->id,
            'week' => 2
        ]);

        $week1Matches = GameMatch::where('week', 1)->get();
        $week2Matches = GameMatch::where('week', 2)->get();

        $this->assertCount(1, $week1Matches);
        $this->assertCount(1, $week2Matches);
        $this->assertEquals(1, $week1Matches->first()->week);
        $this->assertEquals(2, $week2Matches->first()->week);
    }
}
