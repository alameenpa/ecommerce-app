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
     * @param  App\Models\Transaction $model
     * @param  App\Repository\OrderRepository $orderRepository
     */
    public function __construct(Transaction $model, OrderRepository $orderRepository)
    {
        $this->model = $model;
        $this->orderRepository = $orderRepository;
    }

    /**
     * fetch a single transaction by id
     * @param  $id
     * @return \App\Models\Transaction object
     */
    public function getTransaction($id)
    {
        return $this->model::with(['product', 'order'])->find($id);
    }

    /**
     * create new transaction entries with new status
     * @param  $id, $status
     * @return \App\Models\Transaction object
     */
    public function statusUpdate($id, $status)
    {
        //fetch transaction by id
        $currentTransaction = $this->model->find($id);
        //change transaction status to cancel
        $sts = $this->model->find($id)->update(['active' => 0]);

        if ($sts) {
            //create new transaction with current de-activated transaction data
            $newTransactionObject = $this->createTransaction(null, [
                "product_id" => $currentTransaction->product_id,
                "order_id" => $currentTransaction->order_id,
                "quantity" => $currentTransaction->quantity,
                "updated_by" => Auth::user()->id,
                "status" => $status,
                "active" => 1,
                "amount" => $currentTransaction->amount,
            ]);
        }

        //return newly created transaction
        return $this->getTransaction($newTransactionObject->id);
    }

    /**
     * fetch all active transactions by order id
     *
     * @param  $id
     * @return \App\Models\Transaction collection
     */
    public function getActiveTransactionByOrderId($id)
    {
        return $this->model::with(['product'])->where("order_id", $id)->where('active', 1)->get();
    }

    /**
     * create new transaction or update existing by id
     * @param  $id, $dataArray
     * @return \App\Models\Transaction object
     */
    public function createTransaction($id = null, $dataArray)
    {
        return $this->model::updateOrCreate(['id' => $id], $dataArray);
    }

    /**
     * delete all transaction by order id
     * @param  $id
     * @return boolean
     */
    public function removeTransactionsByOrder($id)
    {
        return $this->model->where("order_id", $id)->delete();
    }

    /**
     * change order status based on transaction status
     * @param  $id
     * @return boolean
     */
    public function changeOrderStatusByTransactionStatusChange($id)
    {
        $transaction = $this->getTransaction($id);
        //check for any transaction flow initiated transactions
        $openTransactionsCount = $this->model::with(['product'])
            ->where('active', 1)
            ->where("order_id", $transaction->order_id)
            ->whereNotIn('status', [0, 4])
            ->count();

        if ($openTransactionsCount == 0) {
            //make order status to closed, if all transactions completed there flow or cancelled
            $this->orderRepository->changeOrderStatus($transaction->order_id, 1);
        } else {
            //make order status to open,  if any transactions with pending flow to complete it
            $this->orderRepository->changeOrderStatus($transaction->order_id, 0);
        }
        return true;
    }

    /**
     * check whether any transaction process initiated against a order
     * @param  $orderId
     * @return boolean
     */
    public function isProcessInitiatedOrder($orderId)
    {
        //fetch order details by order id
        $order = $this->orderRepository->getOrderDetails($orderId);
        if ($order->status == 1) {
            return true;
        }

        //fetch count of other than cancelled,received status  transactions
        $openTransactionsCount = $this->model::with(['product'])
            ->where('active', 1)
            ->where("order_id", $orderId)
            ->whereNotIn('status', [1, 0])
            ->count();

        if ($openTransactionsCount > 0) {
            return true;
        }

        return false;

    }

    /**
     * make transaction status to cancel
     * @param  $id
     * @return boolean
     */
    public function cancelTransactionsByOrder($id)
    {
        return $this->model->where("order_id", $id)->update(['status' => 0]);
    }
}
