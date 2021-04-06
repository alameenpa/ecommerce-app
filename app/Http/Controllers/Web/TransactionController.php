<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repository\TransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    protected $orderRepository, $productRepository, $transactionRepository;

    /**
     * @param App\Repository\TransactionRepository $transactionRepository
     */
    public function __construct(
        TransactionRepository $transactionRepository) {
        $this->middleware('auth');
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Display a listing of transactions with popup.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $transactions = $this->transactionRepository->getActiveTransactionByOrderId($request->get('id'));
        return response()->json(['transactions' => $transactions]);
    }

    /**
     * Change transaction status.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request)
    {
        try {
            DB::beginTransaction();
            $transaction = $this->transactionRepository->statusUpdate($request->get('id'), $request->get('transaction_status'));
            $status = $this->transactionRepository->changeOrderStatusByTransactionStatusChange($request->get('id'));
            $this->transactionRepository->sendEmailWithTransactionStatusChange($transaction, $request->get('transaction_status'));
            DB::commit();
            return response()->json(['transaction' => $transaction]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array('transaction' => []));
        }
    }
}
