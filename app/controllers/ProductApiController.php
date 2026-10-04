<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ProductApiController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->library('api');
        $this->call->model('ProductModel');
    }

    // GET /api/products
    public function index()
    {
        $this->api->require_method('GET');
        $this->api->require_jwt();

        $products = $this->ProductModel->all();
        $this->api->respond(['data' => $products]);
    }

    // GET /api/products/{id}
    public function show($id)
    {
        $this->api->require_method('GET');
        $this->api->require_jwt();

        $product = $this->ProductModel->find($id);

        if (!$product) {
            $this->api->respond_error('Product not found.', 404);
        }

        $this->api->respond(['data' => $product]);
    }

    // POST /api/products
    public function create()
    {
        $this->api->require_method('POST');
        $this->api->require_jwt();

        $body = $this->api->body();

        if (empty($body['product_name']) || !isset($body['price']) || !isset($body['quantity'])) {
            $this->api->respond_error('product_name, price, and quantity are required.', 422);
        }

        $id = $this->ProductModel->insert([
            'product_name' => $body['product_name'],
            'description'  => $body['description'] ?? '',
            'price'        => $body['price'],
            'quantity'     => $body['quantity'],
        ]);

        $product = $this->ProductModel->find($id);
        $this->api->respond(['message' => 'Product created', 'data' => $product], 201);
    }

    // PUT /api/products/{id}
    public function update($id)
    {
        $this->api->require_method('PUT');
        $this->api->require_jwt();

        $existing = $this->ProductModel->find($id);
        if (!$existing) {
            $this->api->respond_error('Product not found.', 404);
        }

        $body = $this->api->body();

        $this->ProductModel->update($id, [
            'product_name' => $body['product_name'] ?? $existing['product_name'],
            'description'  => $body['description'] ?? $existing['description'],
            'price'        => $body['price'] ?? $existing['price'],
            'quantity'     => $body['quantity'] ?? $existing['quantity'],
        ]);

        $product = $this->ProductModel->find($id);
        $this->api->respond(['message' => 'Product updated', 'data' => $product]);
    }

    // DELETE /api/products/{id}
    public function delete($id)
    {
        $this->api->require_method('DELETE');
        $this->api->require_jwt();

        $existing = $this->ProductModel->find($id);
        if (!$existing) {
            $this->api->respond_error('Product not found.', 404);
        }

        $this->ProductModel->delete($id);
        $this->api->respond(['message' => 'Product deleted']);
    }
}