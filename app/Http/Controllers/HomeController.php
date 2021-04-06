<?php

namespace App\Http\Controllers;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;

class HomeController extends Controller
{
    protected $userRepository, $productRepository, $orderRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        ProductRepository $productRepository,
        OrderRepository $orderRepository
    ) {
        $this->middleware('auth');
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $users = $this->userRepository->getTotalNumberOfUsers();
        $products = $this->productRepository->getTotalNumberOfProducts();
        $orders = $this->orderRepository->getTotalNumberOfOrders();
        return view('home', compact('users', 'products', 'orders'));
    }
}
