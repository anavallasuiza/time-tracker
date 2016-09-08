<?php

namespace App\Database\Models;

use ANavallaSuiza\Laravel\Database\Contracts\Repository\HasCustomRepository;
use App\Database\Repositories\ClientRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Database\Models\Clients
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Database\Models\Activity[] $activities
 * @mixin \Eloquent
 */
class Client extends Model implements HasCustomRepository
{
    protected $table = 'clients';

    public function activities()
    {
        return $this->hasMany(Activity::class, 'id_clients', 'id');
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
