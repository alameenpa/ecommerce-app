<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\TransactionRepository;
use Illuminate\Http\Request;

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
    }

    /**
     * fetch products
     * @return \Illuminate\Http\Response
     */
    public function getProducts()
    {
        try {
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
            if (empty($request->has('id'))) {
                $content['success'] = false;
                $content['message'] = 'Invalid input parameter';
                $content['code'] = ApiController::CODE_UNPROCESSABLE;
            } else {
                $transactionInitiated = $this->transactionRepository->isProcessInitiatedOrder($request->get('id'));
                if ($transactionInitiated) {
                    $content['success'] = false;
                    $content['message'] = 'Unable to cancel order, Some transactions already initiated';
                    $content['code'] = ApiController::CODE_UNPROCESSABLE;
                } else {
                    $status = $this->orderRepository->changeOrderStatus($request->get('id'), 1);
                    $transactionStatus = $this->transactionRepository->cancelTransactionsByOrder($request->get('id'));
                    $content['success'] = true;
                    $content['message'] = 'ok';
                    $content['code'] = ApiController::CODE_SUCCESS;
                }
            }
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = ApiController::CODE_NOT_ACCEPTABLE;
        }
        return response()->json(['content' => $content], $content['code']);
    }
}
