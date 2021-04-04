<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repository\TransactionRepository;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $orderRepository, $productRepository, $transactionRepository;

    public function __construct(
        TransactionRepository $transactionRepository) {
        $this->middleware('auth');
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Display a listing of users with initial form.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $transactions = $this->transactionRepository->getTransactionByOrderId($request->get('id'));
        return response()->json(['transactions' => $transactions]);
    }

    public function status(Request $request)
    {
        $status = $this->transactionRepository->statusUpdate($request->get('id'), $request->get('transaction_status'));
        return response()->json(['transaction' => $this->transactionRepository->getTransaction($request->get('id'))]);
    }
}
