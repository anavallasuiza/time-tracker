<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Clients
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Activities[] $activities
 * @mixin \Eloquent
 */
class Clients extends Model
{
    protected $table = 'clients';

    public function activities()
    {
        return $this->hasMany(Activities::class, 'id_clients', 'id');
    }
}
