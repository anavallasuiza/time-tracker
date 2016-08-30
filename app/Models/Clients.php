<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    protected $table = 'clients';

    public function activities()
    {
        return $this->hasMany(Activities::class, 'id_clients', 'id');
    }
}
