<?php

namespace App\Models;
use App\Models\RaffleNumber;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaffleNumber extends Model
{
    use HasFactory;

    protected $fillable = ['raffle_id', 'number'];

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }
}
 