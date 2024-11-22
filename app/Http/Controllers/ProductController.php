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

        Storage::put($this->jsonFilePath, json_encode($product, JSON_PRETTY_PRINT));



        return response()->json(['success' => true, 'message' => 'Product saved!', 'products' => $product]);
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

    public function update(Request $request, $id) {}
}
