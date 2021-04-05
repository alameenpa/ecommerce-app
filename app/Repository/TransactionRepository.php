<?php

namespace App\Repository;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionRepository
{
    protected $model, $orderRepository;

    /**
     * Instantiate repository
     *
     * @param  $model
     */
    public function __construct(Transaction $model, OrderRepository $orderRepository)
    {
        $this->model = $model;
        $this->orderRepository = $orderRepository;
    }

    /**
     * fetch all users
     *
     * @return \App\User collection
     */
    public function getTransaction($id)
    {
        return $this->model::with(['product', 'order'])->find($id);
    }

    /**
     * fetch all users
     *
     * @return \App\User collection
     */
    public function statusUpdate($id, $status)
    {
        $currentTransaction = $this->model->find($id);
        $sts = $this->model->find($id)->update(['active' => 0]);
        $newTransactionObject = $this->createTransaction(null, [
            "product_id" => $currentTransaction->product_id,
            "order_id" => $currentTransaction->order_id,
            "quantity" => $currentTransaction->quantity,
            "updated_by" => Auth::user()->id,
            "status" => $status,
            "active" => 1,
            "amount" => $currentTransaction->amount,
        ]);
        return $this->getTransaction($newTransactionObject->id);
    }

    /**
     * fetch a single users by id
     *
     * @param  $id
     * @return \App\User object
     */
    public function getActiveTransactionByOrderId($id)
    {
        return $this->model::with(['product'])->where("order_id", $id)->where('active', 1)->get();
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

    public function changeOrderStatusByTransactionStatusChange($id)
    {
        $transaction = $this->getTransaction($id);
        $openTransactionsCount = $this->model::with(['product'])
            ->where("order_id", $transaction->order_id)
            ->whereNotIn('status', [0, 4])
            ->count();

        if ($openTransactionsCount == 0) {
            $this->orderRepository->changeOrderStatus($transaction->order_id, 1);
        } else {
            $this->orderRepository->changeOrderStatus($transaction->order_id, 0);
        }
        return true;
    }
}
