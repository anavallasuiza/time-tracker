<?php

namespace App\Database\Repositories;

use ANavallaSuiza\Laravel\Database\Repository\Eloquent\Repository;
use App\Database\Models\User;
use App\Exceptions\ModelException;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends Repository
{

    private $editableAttributes = [
        'name',
        'user',
        'email',
        'api_key',
        'store_hours',
        'enabled'
    ];

    /**
     * @return User
     */
    public function getModel()
    {
        return parent::getModel();
    }

    /**
     * @return Collection
     */
    public function getUsers()
    {
        return $this->getModel()->orderBy('name', 'ASC')->get();
    }

    public function persist(array $input)
    {
        $user = new User();
        foreach ($this->editableAttributes as $attribute) {
            if (isset($input[$attribute])) {
                $user->setAttribute($attribute, $input[$attribute]);
            }
        }

        if (isset($input['password']) && !empty($input['password'])) {
            $user->password = \Hash::make($input['password']);
        }

        if (!$user->save()) {
            throw new ModelException(_('Impossible to add the client'));
        }
        return $user;
    }

    /**
     * @param $userId
     * @return User
     */
    public function getUser($userId)
    {
        return $this->getModel()->whereId($userId)->firstOrFail();
    }

    public function updateUser($userId, $data)
    {
        $user = $this->getUser($userId);
        foreach ($this->editableAttributes as $attribute) {

            if (isset($data[$attribute])) {
                $user->setAttribute($attribute, $data[$attribute]);
            }
        }
        if (isset($data['password']) && !empty($data['password'])) {
            $user->password = \Hash::make($data['password']);
        }

        if (!$user->save()) {
            throw new ModelException(_('Impossible to update the user'));
        }

        return $user;
    }
}