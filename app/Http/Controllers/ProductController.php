<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private $jsonFilePath = 'products.json';

    public function index()
    {
        $products = $this->listproducts();

        return view('products.index')
            ->with('products', $products);
    }

    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $product = $this->listproducts();

        $product[] = [
            'id' => uniqid(),
            'name' => $request->name,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'date_submitted' => now()
        ];


        $this->saveProduct($product);


        return response()->json(['success' => true, 'message' => 'Product saved!', 'products' => $product]);
    }

    private function saveProduct(array $products)
    {
        Storage::put($this->jsonFilePath, json_encode($products, JSON_PRETTY_PRINT));
    }

    private function listproducts()
    {
        $products = [];

        if (Storage::exists($this->jsonFilePath)) {
            $products =  json_decode(Storage::get($this->jsonFilePath), true);

            usort($products, function ($a, $b) {
                return strtotime($a['date_submitted']) - strtotime($b['date_submitted']);
            });
        }

        return $products;
    }

    public function edit($id)
    {

        $products = $this->listproducts();

        $product = collect($products)->firstwhere('id', $id);

        return response()->json(['product' => $product]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $products = $this->listproducts();

        $products = collect($products)->map(function ($product) use ($request, $id) {
            if ($product['id'] == $id) {
                $product['name'] = $request->name;
                $product['quantity'] = $request->quantity;
                $product['price'] = $request->price;
            }
            return $product;
        })->toArray();

        $this->saveProduct($products);

        return response()->json(['success' => true, 'message' => 'Product updated!', 'products' => $products]);
    }
}
