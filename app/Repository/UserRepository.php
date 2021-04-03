<?php

namespace App\Repository;

use App\Models\User;

class UserRepository
{
    protected $model;
    /**
     * Instantiate repository
     *
     * @param  $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getUsers()
    {
        return $this->model->get();
    }

    public function getUser($id)
    {
        return $this->model->find($id);
    }

    public function saveUser($id = null, $dataArray)
    {
        return $this->model::updateOrCreate(['id' => $id], $dataArray);
    }

    public function destroyUser($id)
    {
        return $this->model->find($id)->delete();
    }

}
