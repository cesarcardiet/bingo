<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceRecharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id', 'bank', 'reference_number', 'amount', 'receipt', 'status'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
