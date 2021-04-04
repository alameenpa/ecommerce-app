<?php

namespace App\Repository;

use App\Models\Transaction;

class TransactionRepository
{
    protected $model;

    /**
     * Instantiate repository
     *
     * @param  $model
     */
    public function __construct(Transaction $model)
    {
        $this->model = $model;
    }

    /**
     * fetch all users
     *
     * @return \App\User collection
     */
    public function getTransaction($id)
    {
        return $this->model::with('product')->find($id);
    }

    /**
     * fetch all users
     *
     * @return \App\User collection
     */
    public function statusUpdate($id, $status)
    {
        return $this->model->find($id)->update(['status' => $status]);
    }

    /**
     * fetch a single users by id
     *
     * @param  $id
     * @return \App\User object
     */
    public function getTransactionByOrderId($id)
    {
        return $this->model::with(['product'])->where("order_id", $id)->get();
    }

    /**
     * create a user or update by id
     *
     * @param  $id, $dataArray
     * @return \App\User object
     */
    public function createTransaction($id = null, $dataArray)
    {
        return $this->model::updateOrCreate(['id' => $id], $dataArray);
    }

    /**
     * delete a user by id
     *
     * @param  $id
     * @return boolean
     */
    public function removeTransactionsByOrder($id)
    {
        return $this->model->where("order_id", $id)->delete();
    }
}
