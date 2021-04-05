<?php

namespace App\Repository;

use App\Models\Product;

class ProductRepository
{
    protected $model;

    /**
     * Instantiate repository
     *
     * @param  App\Models\Product $model
     */
    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    /**
     * fetch all products
     *
     * @return App\Models\Product collection
     */
    public function getProducts()
    {
        return $this->model->get();
    }

    /**
     * fetch a single product by id
     *
     * @param  $id
     * @return App\Models\Product object
     */
    public function getProduct($id)
    {
        return $this->model->find($id);
    }

    /**
     * create a product or update by id
     *
     * @param  $id, $dataArray
     * @return App\Models\Product object
     */
    public function saveProduct($id = null, $dataArray)
    {
        return $this->model::updateOrCreate(['id' => $id], $dataArray);
    }

    /**
     * delete a product by id
     *
     * @param  $id
     * @return boolean
     */
    public function destroyProduct($id)
    {
        return $this->model->find($id)->delete();
    }
}
