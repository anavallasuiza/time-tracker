<?php

namespace App\Models;

use ANavallaSuiza\Laravel\Database\Contracts\Repository\HasCustomRepository;
use App\Database\Repositories\ClientRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Clients
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Activities[] $activities
 * @mixin \Eloquent
 */
class Clients extends Model implements HasCustomRepository
{
    protected $table = 'clients';

    public function activities()
    {
        return $this->hasMany(Activities::class, 'id_clients', 'id');
    }

    public function repository()
    {
        return ClientRepository::class;
    }

    /**
     * @return Collection
     */
    public function activitiesActives()
    {
        return $this->activities()->where('archived',0)->get();
    }

    /**
     * @return Collection
     */
    public function activitiesArchived()
    {
        return $this->activities()->where('archived',1)->get();
    }

}
