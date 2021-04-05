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
     * fetch a single users by id
     *
     * @return \App\User object
     */
    public function getOrders()
    {
        return $this->model::with('transactions')->get();
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
    public function changeOrderStatus($id, $status)
    {
        return $this->model->find($id)->update(["status" => $status]);
    }
}
