<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Agent extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'id_number',
        'email',
        'password',
        'phone',
        'bank_name',
        'referral_id',
        'remember_token'
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class, 'agent_id');
    }

    public function players()
    {
        return $this->hasMany(Player::class, 'agent_id');
    }
    
}
