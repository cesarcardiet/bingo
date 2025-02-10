<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Raffle extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'agent_id',
        'name',
        'game_type',
        'prize',
        'total_cards',
        'order',
        'status', 
    ];
    
    // Relación: Un sorteo pertenece a una sala
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    // ✅ Relación: Un sorteo tiene muchos números sorteados
    public function numbers()
    {
        return $this->hasMany(RaffleNumber::class, 'raffle_id');
    }
    
}
