<?php

namespace App\Repository;

use App\Models\Product;

class ProductRepository
{
    protected $model;

    /**
     * Instantiate repository
     *
     * @param  $model
     */
    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    /**
     * fetch all users
     *
     * @return \App\User collection
     */
    public function getProducts()
    {
        return $this->model->get();
    }

    /**
     * fetch a single users by id
     *
     * @param  $id
     * @return \App\User object
     */
    public function getProduct($id)
    {
        return $this->model->find($id);
    }

    /**
     * create a user or update by id
     *
     * @param  $id, $dataArray
     * @return \App\User object
     */
    public function saveProduct($id = null, $dataArray)
    {
        return $this->model::updateOrCreate(['id' => $id], $dataArray);
    }

    /**
     * delete a user by id
     *
     * @param  $id
     * @return boolean
     */
    public function destroyProduct($id)
    {
        return $this->model->find($id)->delete();
    }
}
