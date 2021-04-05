<?php

namespace App\Http\Controllers\Web;

use App\DataTables\OrdersDataTable;
use App\Http\Controllers\Controller;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\TransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $orderRepository, $productRepository, $transactionRepository;

    /**
     * @param App\Repository\OrderRepository $orderRepository
     * @param App\Repository\ProductRepository $productRepository
     * @param App\Repository\TransactionRepository $transactionRepository
     */
    public function __construct(
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        TransactionRepository $transactionRepository) {
        $this->middleware('auth');
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Display a listing of orders with initial form.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(OrdersDataTable $dataTable)
    {
        $products = $this->productRepository->getProducts();
        return $dataTable->render('orders.index', compact("products"));
    }

    /**
     * Store a newly created order or update an existing order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $id = $request->id;
            $inputArray = [
                'address' => $request->address,
                'created_by' => Auth::user()->id,
                'amount' => $request->total,
                'status' => false,
            ];

            //decode json encoded array parameters
            $itemsArray = json_decode($request->get('order_hidden_input'));
            if (empty($itemsArray)) {
                return response()->json(array('success' => false, 'message' => 'Operation Failed, please add any product to order'));
            }

            //create or update orders
            $order = $this->orderRepository->createOrder($id, $inputArray);
            if (empty($id)) {
                $id = $order->id;
            }

            //remove already existing transactions by id
            $status = $this->transactionRepository->removeTransactionsByOrder($id);
            foreach ($itemsArray as $item) {
                $itemArray = [
                    "product_id" => $item->product_id,
                    "order_id" => $order->id,
                    "quantity" => $item->quantity,
                    "updated_by" => Auth::user()->id,
                    "status" => 1,
                    "amount" => ($item->product_price * $item->quantity),
                    "active" => 1,
                ];

                //create new transactions
                $transaction = $this->transactionRepository->createTransaction(null, $itemArray);
            }
            DB::commit();
            return Response()->json(array('success' => true));
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }

    /**
     * Show the form for editing the specified order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $order = $this->orderRepository->getOrderDetails($request->id);
        return Response()->json($order);
    }

    /**
     * Remove the specified order from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request)
    {
        try {
            DB::beginTransaction();
            $transactionInitiated = $this->transactionRepository->isProcessInitiatedOrder($request->id);
            if ($transactionInitiated) {
                return response()->json(array('success' => false, 'message' => "Unable to cancel order, Some transactions already initiated"));
            }
            //mark order status as cancel
            $status = $this->orderRepository->changeOrderStatus($request->id, 1);
            //mark transaction status as cancel
            $transactionStatus = $this->transactionRepository->cancelTransactionsByOrder($request->id);
            DB::commit();
            return Response()->json(array('success' => true));
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }
}
