<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Services\LeagueService;
use Illuminate\Http\Request;

class LeagueController extends Controller
{
    protected $leagueService;

    public function __construct(LeagueService $leagueService)
    {
        $this->leagueService = $leagueService;
    }

    public function index()
    {
        $table = $this->leagueService->getLeagueTable();
        $matches = GameMatch::with(['homeTeam', 'awayTeam'])
            ->orderBy('week')
            ->get()
            ->groupBy('week');
        $predictions = $this->leagueService->predictChampionship();

        return view('league.index', compact('table', 'matches', 'predictions'));
    }

    public function generateFixtures()
    {
        $this->leagueService->generateFixtures();
        return redirect()->route('league.index');
    }

    public function playAll()
    {
        $matches = GameMatch::where('played', false)->get();
        foreach ($matches as $match) {
            $this->leagueService->simulateMatch($match);
        }
        return redirect()->route('league.index');
    }

    public function playWeek()
    {
        $nextWeek = GameMatch::where('played', false)
            ->min('week');

        if ($nextWeek) {
            $matches = GameMatch::where('week', $nextWeek)
                ->where('played', false)
                ->get();

            foreach ($matches as $match) {
                $this->leagueService->simulateMatch($match);
            }
        }

        return redirect()->route('league.index');
    }

    public function updateMatch(Request $request, GameMatch $match)
    {
        $match->update([
            'home_team_score' => $request->home_score,
            'away_team_score' => $request->away_score,
            'played' => true
        ]);

        $this->leagueService->updateLeagueTable(
            $match->homeTeam,
            $match->awayTeam,
            $request->home_score,
            $request->away_score
        );

        return redirect()->route('league.index');
    }
}
