<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BingoCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id', 'player_id', 'card_number', 'card_data', 'status'
    ];

protected $casts = [
    'card_data' => 'array', // ✅ Asegura que se maneje siempre como array
];


    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
    
}
