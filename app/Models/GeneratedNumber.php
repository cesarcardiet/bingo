<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneratedNumber extends Model
{
    use HasFactory;

    protected $fillable = ['raffle_id', 'number'];

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }
}
 