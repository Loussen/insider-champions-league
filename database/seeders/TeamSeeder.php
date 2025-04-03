<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\LeagueTable;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            ['name' => 'Chelsea', 'strength' => 85],
            ['name' => 'Arsenal', 'strength' => 83],
            ['name' => 'Manchester City', 'strength' => 88],
            ['name' => 'Liverpool', 'strength' => 86]
        ];

        foreach ($teams as $team) {
            $createdTeam = Team::create($team);
            LeagueTable::create([
                'team_id' => $createdTeam->id
            ]);
        }
    }
}
