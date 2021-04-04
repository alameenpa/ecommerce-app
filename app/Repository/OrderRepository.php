<?php

namespace App\Repository;

use App\Models\Order;

class OrderRepository
{
    protected $model;

    /**
     * Instantiate repository
     *
     * @param  $model
     */
    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    /**
     * fetch all users
     *
     * @return \App\User collection
     */
    public function getProducts()
    {
        return $this->model->get();
    }

    /**
     * fetch a single users by id
     *
     * @param  $id
     * @return \App\User object
     */
    public function getOrderDetails($id)
    {
        return $this->model::with(['transactions.product'])->find($id);
    }

    /**
     * create a user or update by id
     *
     * @param  $id, $dataArray
     * @return \App\User object
     */
    public function createOrder($id = null, $dataArray)
    {
        return $this->model::updateOrCreate(['id' => $id], $dataArray);
    }

    /**
     * delete a user by id
     *
     * @param  $id
     * @return boolean
     */
    public function cancelOrder($id)
    {
        return $this->model->find($id)->update(["status" => 2]);
    }
}
