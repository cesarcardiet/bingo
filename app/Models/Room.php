<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'name',
        'description',
        'start_time',
        'total_prizes',
        'card_price',
        'total_cards',
        'max_players',
        'status'
    ];

    // Relación: Una sala pertenece a un agente
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }
    
    // Relación: Una sala tiene muchos sorteos
    public function raffles()
    {
        return $this->hasMany(Raffle::class, 'room_id');
    }
    public function bingoCards()
    {
        return $this->hasMany(BingoCard::class, 'room_id');
    }
    
}
