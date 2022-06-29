<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Infrapwd extends Model
{
    protected $fillable = [
        'displayname', 'username', 'password', 'access_to_all'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
