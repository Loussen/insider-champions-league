<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GameMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'home_team_id',
        'away_team_id',
        'home_team_score',
        'away_team_score',
        'week',
        'played'
    ];

    protected $attributes = [
        'played' => false
    ];

    protected $casts = [
        'played' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($match) {
            if ($match->home_team_id === $match->away_team_id) {
                throw new \InvalidArgumentException('A team cannot play against itself');
            }
        });
    }

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }
}
