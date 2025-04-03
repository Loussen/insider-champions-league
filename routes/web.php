<?php

use App\Http\Controllers\LeagueController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LeagueController::class, 'index'])->name('league.index');
Route::post('/fixtures/generate', [LeagueController::class, 'generateFixtures'])->name('league.fixtures.generate');
Route::post('/play/all', [LeagueController::class, 'playAll'])->name('league.play.all');
Route::post('/play/week', [LeagueController::class, 'playWeek'])->name('league.play.week');
Route::put('/match/{match}', [LeagueController::class, 'updateMatch'])->name('league.match.update');


