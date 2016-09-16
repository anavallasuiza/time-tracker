<?php


namespace App\Database\Repositories;

use ANavallaSuiza\Laravel\Database\Repository\Eloquent\Repository;
use App\Database\Models\Fact;

class FactRepository extends Repository
{

    /**
     * @return Fact
     */
    public function getModel()
    {
        return parent::getModel();
    }

    
    /**
     * @param $factId
     * @return Fact
     */
    public function getFact($factId)
    {
        return $this->getModel()->whereId($factId)->firstOrFail();
    }

    
}
