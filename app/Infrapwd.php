<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Infrapwd extends Model
{
    protected $fillable = [
        'displayname', 'username', 'password'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
