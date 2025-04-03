<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeagueTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'played',
        'won',
        'drawn',
        'lost',
        'goals_for',
        'goals_against',
        'goal_difference',
        'points'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
