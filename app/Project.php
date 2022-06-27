<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Project extends Model
{
    protected $fillable = [
        'name', 'type', 'scriptlink', 'weight'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
