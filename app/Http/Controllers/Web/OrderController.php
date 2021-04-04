<?php

namespace App\Http\Controllers\Web;

use App\DataTables\OrdersDataTable;
use App\Http\Controllers\Controller;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\TransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $orderRepository, $productRepository, $transactionRepository;

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
     * Display a listing of users with initial form.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(OrdersDataTable $dataTable)
    {
        $products = $this->productRepository->getProducts();
        return $dataTable->render('orders.index', compact("products"));
    }

    /**
     * Store a newly created user or update an existing user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $id = $request->id;
            $inputArray = [
                'address' => $request->address,
                'created_by' => Auth::user()->id,
                'amount' => $request->total,
                'status' => false,
            ];

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
                    'amount' => ($item->product_price * $item->quantity),
                ];

                //create new transactions
                $transaction = $this->transactionRepository->createTransaction(null, $itemArray);
            }
            return Response()->json(array('success' => true));
        } catch (\Exception $e) {
            return response()->json(array('success' => false, 'message' => 'Operation Failed, please contact admin'));
        }
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $product = $this->orderRepository->getOrderDetails($request->id);
        return Response()->json($product);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request)
    {
        try {
            $status = $this->orderRepository->cancelOrder($request->id);
            return Response()->json(array('success' => true));
        } catch (\Exception $e) {
            return response()->json(array('success' => false, 'message' => 'Operation Failed, please contact admin'));
        }
    }
}
