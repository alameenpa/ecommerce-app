<?php

namespace App\Repository;

use App\Models\User;
use App\Notifications\NewUserNotify;
use Illuminate\Support\Facades\Notification;

class UserRepository
{
    protected $model;

    /**
     * Instantiate repository
     *
     * @param  App\Models\User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * fetch all users
     *
     * @return \App\Models\User collection
     */
    public function getUsers()
    {
        return $this->model->get();
    }

    /**
     * fetch a single users by id
     *
     * @param  $id
     * @return \App\Models\User object
     */
    public function getUser($id)
    {
        return $this->model->find($id);
    }

    /**
     * create a user or update by id
     *
     * @param  $id, $dataArray
     * @return \App\Models\User object
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

    /**
     * user signup mail notification
     * @param  $user
     * @return boolean
     */
    public function sendWelcomeMail($user)
    {
        Notification::route('mail', $user->email) //Sending email to user
            ->notify(new NewUserNotify($user));
        return true;
    }

}
