<?php

namespace App\Repository;

use App\Models\Order;
use App\Notifications\NewOrderNotify;
use Illuminate\Support\Facades\Notification;

class OrderRepository
{
    protected $model;

    /**
     * Instantiate repository
     *
     * @param  App\Models\Order $model
     */
    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    /**
     * fetch all order by id
     *
     * @return App\Models\Order collection
     */
    public function getOrders()
    {
        return $this->model::with('transactions')->get();
    }

    /**
     * fetch a single order by id
     *
     * @param  $id
     * @return App\Models\Order object
     */
    public function getOrderDetails($id)
    {
        return $this->model::with(['transactions.product', 'user'])->find($id);
    }

    /**
     * create an order or update by id
     *
     * @param  $id, $dataArray
     * @return App\Models\Order object
     */
    public function createOrder($id = null, $dataArray)
    {
        return $this->model::updateOrCreate(['id' => $id], $dataArray);
    }

    /**
     * change status of an order by id
     *
     * @param  $id
     * @return boolean
     */
    public function changeOrderStatus($id, $status)
    {
        return $this->model->find($id)->update(["status" => $status]);
    }

    /**
     * order mail notification
     * @param  $order
     * @return boolean
     */
    public function sendEmailWithOrder($order)
    {
        Notification::route('mail', $order->user->email) //Sending email to ordered user
            ->notify(new NewOrderNotify($order));
        return true;
    }

    /**
     * get Orders count
     * @return integer
     */
    public function getTotalNumberOfOrders()
    {
        return $this->model->count();
    }
}
