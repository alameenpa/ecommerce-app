<?php

namespace App\Http\Controllers\Web;

use App\DataTables\ProductsDataTable;
use App\Http\Controllers\Controller;
use App\Repository\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $productRepository;

    /**
     * @param App\Repository\ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->middleware('auth');
        $this->productRepository = $productRepository;
    }

    /**
     * Display a listing of products with initial form.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProductsDataTable $dataTable)
    {
        return $dataTable->render('products.index');
    }

    /**
     * Store a newly created product or update an existing product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|max:50',
                'description' => 'required|max:100',
                'price' => 'required|numeric',
            ]);

            DB::beginTransaction();
            $id = $request->id;
            $inputArray = [
                'name' => ucwords($request->name),
                'description' => $request->description,
                'price' => $request->price,
                'created_by' => Auth::user()->id,
            ];
            //save product
            $product = $this->productRepository->saveProduct($id, $inputArray);
            DB::commit();
            return Response()->json(array('success' => true));
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $product = $this->productRepository->getProduct($request->id);
        return Response()->json($product);
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            DB::beginTransaction();
            //delete product
            $status = $this->productRepository->destroyProduct($request->id);
            DB::commit();
            return Response()->json(array('success' => true));
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }
}
