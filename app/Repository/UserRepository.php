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

    /**
     * fetch all users
     *
     * @return \App\User collection
     */
    public function getUsers()
    {
        return $this->model->get();
    }

    /**
     * fetch a single users by id
     *
     * @param  $id
     * @return \App\User object
     */
    public function getUser($id)
    {
        return $this->model->find($id);
    }

    /**
     * create a user or update by id
     *
     * @param  $id, $dataArray
     * @return \App\User object
     */
    public function saveUser($id = null, $dataArray)
    {
        return $this->model::updateOrCreate(['id' => $id], $dataArray);
    }

    /**
     * delete a user by id
     *
     * @param  $id
     * @return boolean
     */
    public function destroyUser($id)
    {
        return $this->model->find($id)->delete();
    }

}