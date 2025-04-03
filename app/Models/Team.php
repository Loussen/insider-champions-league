<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'strength'];

    public function homeMatches()
    {
        return $this->hasMany(GameMatch::class, 'home_team_id');
    }

    public function awayMatches()
    {
        return $this->hasMany(GameMatch::class, 'away_team_id');
    }

    public function leagueTable()
    {
        return $this->hasOne(LeagueTable::class);
    }
}
