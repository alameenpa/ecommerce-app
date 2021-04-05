<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\TransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    const CODE_SUCCESS = 200, CODE_NOT_ACCEPTABLE = 406, CODE_UNPROCESSABLE = 422;

    protected $orderRepository, $productRepository, $transactionRepository;

    public function __construct(
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        TransactionRepository $transactionRepository) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->transactionRepository = $transactionRepository;
        $this->middleware('auth:api');
    }

    /**
     * fetch products
     * @return \Illuminate\Http\Response
     */
    public function getProducts()
    {
        try {
            //get products
            $content['data'] = $this->productRepository->getProducts();
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = ApiController::CODE_SUCCESS;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = ApiController::CODE_NOT_ACCEPTABLE;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * fetch product by id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getSingleProduct(Request $request)
    {
        try {
            if (empty($request->has('id'))) {
                $content['success'] = false;
                $content['message'] = 'Invalid input parameter';
                $content['code'] = ApiController::CODE_UNPROCESSABLE;
            } else {
                //get product by product id
                $content['data'] = $this->productRepository->getProduct($request->get('id'));
                $content['success'] = true;
                $content['message'] = 'ok';
                $content['code'] = ApiController::CODE_SUCCESS;
            }
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = ApiController::CODE_NOT_ACCEPTABLE;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * fetch orders
     * @return \Illuminate\Http\Response
     */
    public function getOrders()
    {
        try {
            //get orders
            $content['data'] = $this->orderRepository->getOrders();
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = ApiController::CODE_SUCCESS;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = ApiController::CODE_NOT_ACCEPTABLE;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * fetch order by id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getSingleOrder(Request $request)
    {
        try {
            if (empty($request->has('id'))) {
                $content['success'] = false;
                $content['message'] = 'Invalid input parameter';
                $content['code'] = ApiController::CODE_UNPROCESSABLE;
            } else {
                //get order by order id
                $content['data'] = $this->orderRepository->getOrderDetails($request->get('id'));
                $content['success'] = true;
                $content['message'] = 'ok';
                $content['code'] = ApiController::CODE_SUCCESS;
            }
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = ApiController::CODE_NOT_ACCEPTABLE;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * cancel order by id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function cancelOrder(Request $request)
    {
        try {
            DB::beginTransaction();
            //empty check
            if (empty($request->has('id'))) {
                $content['success'] = false;
                $content['message'] = 'Invalid input parameter';
                $content['code'] = ApiController::CODE_UNPROCESSABLE;
            } else {
                //check for any status movement
                $transactionInitiated = $this->transactionRepository->isProcessInitiatedOrder($request->get('id'));
                if ($transactionInitiated) {
                    $content['success'] = false;
                    $content['message'] = 'Unable to cancel order, Some transactions already initiated';
                    $content['code'] = ApiController::CODE_UNPROCESSABLE;
                } else {
                    //change order status
                    $status = $this->orderRepository->changeOrderStatus($request->get('id'), 1);
                    //change transactions status associated with the particular order
                    $transactionStatus = $this->transactionRepository->cancelTransactionsByOrder($request->get('id'));
                    $content['success'] = true;
                    $content['message'] = 'ok';
                    $content['code'] = ApiController::CODE_SUCCESS;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = ApiController::CODE_NOT_ACCEPTABLE;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * create order
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function createOrder(Request $request)
    {
        try {
            DB::beginTransaction();
            //input parameter validations
            $validated = $request->validate([
                'address' => 'required|max:255',
                'amount' => 'required|numeric',
            ]);

            //#note - items list validation need to be handle in client side
            $itemsArray = $request->has('items') ? json_decode($request->items) : null;
            if (empty($itemsArray)) {
                $content['success'] = false;
                $content['message'] = "No products selected to order";
                $content['code'] = ApiController::CODE_NOT_ACCEPTABLE;
                return response()->json(['content' => $content], $content['code']);
            }

            $inputArray = [
                'address' => $request->address,
                'created_by' => auth()->user()->id,
                'amount' => $request->amount,
                'status' => false,
            ];
            //create orders
            $order = $this->orderRepository->createOrder(null, $inputArray);
            $orderDetails = $this->orderRepository->getOrderDetails($order->id);
            $mailStatus = $this->orderRepository->sendEmailWithOrder($orderDetails);

            //create transactions associated with the order
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
                $transaction = $this->transactionRepository->createTransaction(null, $itemArray);
            }

            $content['data'] = $order;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = ApiController::CODE_SUCCESS;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = ApiController::CODE_NOT_ACCEPTABLE;
        }
        return response()->json(['content' => $content], $content['code']);
    }
}
