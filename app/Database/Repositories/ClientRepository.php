<?php

namespace App\Database\Repositories;

use ANavallaSuiza\Laravel\Database\Repository\Eloquent\Repository;
use App\Exceptions\ModelException;
use App\Models\Clients;
use Illuminate\Database\Eloquent\Collection;

class ClientRepository extends Repository
{

    /**
     * @return Clients
     */
    public function getModel()
    {
        return parent::getModel();
    }

    /**
     * @return Collection
     */
    public function getClients()
    {
        return $this->getModel()->orderBy('name', 'ASC')->get();
    }

    public function persist(array $input)
    {
        $client = new Clients();
        $client->name = $input['name'];
        if (!$client->save()) {
            throw new ModelException(_('Impossible to add the client'));
        }
        return $client;
    }

    /**
     * @param $clientId
     * @return Clients
     */
    public function getClient($clientId)
    {
        return $this->getModel()->whereId($clientId)->firstOrFail();
    }

    public function updateClientName($clientId,$name)
    {
        $client = $this->getClient($clientId);
        $client->name = $name;
        if(!$client->save())
        {
            throw new ModelException(_('Impossible to update the client'));
        }
        return $client;
    }
}