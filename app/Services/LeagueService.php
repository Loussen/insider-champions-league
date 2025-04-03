<?php

namespace App\Services;

use App\Models\Team;
use App\Models\GameMatch;
use App\Models\LeagueTable;

class LeagueService
{
    public function generateFixtures()
    {
        $teams = Team::all();
        $totalWeeks = count($teams) - 1;
        $matchesPerWeek = count($teams) / 2;

        for ($week = 1; $week <= $totalWeeks; $week++) {
            $teamsInWeek = $teams->shuffle();

            for ($i = 0; $i < $matchesPerWeek; $i++) {
                GameMatch::create([
                    'home_team_id' => $teamsInWeek[$i]->id,
                    'away_team_id' => $teamsInWeek[$i + $matchesPerWeek]->id,
                    'week' => $week
                ]);
            }
        }
    }

    public function simulateMatch(GameMatch $match)
    {
        if ($match->played) {
            return $match;
        }

        $homeTeam = $match->homeTeam;
        $awayTeam = $match->awayTeam;

        $homeGoals = $this->calculateGoals($homeTeam->strength);
        $awayGoals = $this->calculateGoals($awayTeam->strength);

        $match->forceFill([
            'home_team_score' => $homeGoals,
            'away_team_score' => $awayGoals,
            'played' => true
        ])->save();

        $this->updateLeagueTable($homeTeam, $awayTeam, $homeGoals, $awayGoals);

        return $match;
    }

    private function calculateGoals($strength)
    {
        $baseChance = $strength / 20;
        return rand(0, round($baseChance));
    }

    public function updateLeagueTable($homeTeam, $awayTeam, $homeGoals, $awayGoals)
    {
        $homeTable = $homeTeam->leagueTable;
        $awayTable = $awayTeam->leagueTable;

        $homeTable->played++;
        $homeTable->goals_for += $homeGoals;
        $homeTable->goals_against += $awayGoals;

        $awayTable->played++;
        $awayTable->goals_for += $awayGoals;
        $awayTable->goals_against += $homeGoals;

        if ($homeGoals > $awayGoals) {
            $homeTable->won++;
            $homeTable->points += 3;
            $awayTable->lost++;
        } elseif ($homeGoals < $awayGoals) {
            $awayTable->won++;
            $awayTable->points += 3;
            $homeTable->lost++;
        } else {
            $homeTable->drawn++;
            $awayTable->drawn++;
            $homeTable->points++;
            $awayTable->points++;
        }

        $homeTable->goal_difference = $homeTable->goals_for - $homeTable->goals_against;
        $awayTable->goal_difference = $awayTable->goals_for - $awayTable->goals_against;

        $homeTable->save();
        $awayTable->save();
    }

    public function getLeagueTable()
    {
        return LeagueTable::with('team')
            ->orderBy('points', 'desc')
            ->orderBy('goal_difference', 'desc')
            ->orderBy('goals_for', 'desc')
            ->get();
    }

    public function predictChampionship()
    {
        $teams = Team::with('leagueTable')->get();
        $predictions = [];

        foreach ($teams as $team) {
            $chance = ($team->strength * 0.4) + 
                     ($team->leagueTable->points * 0.4) + 
                     ($team->leagueTable->goal_difference * 0.2);
            
            $predictions[$team->name] = round($chance, 1);
        }

        arsort($predictions);
        return $predictions;
    }
}
