<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    use HasFactory;

    protected $table = 'winners'; // Asegura el nombre correcto de la tabla

    protected $fillable = ['raffle_id', 'player_id', 'bingo_card_id', 'prize'];

    // 🔹 Relación con el sorteo
    public function raffle()
    {
        return $this->belongsTo(Raffle::class, 'raffle_id');
    }

    // 🔹 Relación con el jugador
    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    // 🔹 Relación con el cartón de bingo
    public function bingoCard()
    {
        return $this->belongsTo(BingoCard::class, 'bingo_card_id');
    }
}
