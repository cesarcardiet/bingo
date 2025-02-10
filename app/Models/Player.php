<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Player extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'name',
        'email',
        'password',
        'balance',
        'referral_id', // 🔹 Asegurar que se puede llenar este campo
    ];
    
    public function agentRooms()
{
    return $this->hasOne(Agent::class, 'id', 'agent_id')->with('rooms');
}

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function agent()
    {
        return $this->belongsTo(\App\Models\Agent::class, 'agent_id');
    }

    public function recharges()
    {
        return $this->hasMany(BalanceRecharge::class, 'player_id');
    }
}
