<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class user extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $primaryKey = 'id';
    protected $hidden = ['created_at', 'updated_at'];
    protected $guard = 'user';

    protected $fillable = [
        'name',
        'email',
        'phoneno',
        'password',
        'disable'
    ];

    public function getAuthIdentifierName()
    {
        return 'id';
    }
}
