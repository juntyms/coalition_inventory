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
        return view('products.index');
    }

    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $product = [];

        if (Storage::exists($this->jsonFilePath)) {
            $product =  json_decode(Storage::get($this->jsonFilePath), true);
        }

        $product[] = [
            'id' => uniqid(),
            'name' => $request->name,
            'quantity' => $request->quantity,
            'price' => $request->price,
        ];

        Storage::put($this->jsonFilePath, json_encode($product, JSON_PRETTY_PRINT));

        return response()->json(['success' => true, 'message' => 'Product saved!', 'product' => $product]);
    }

    public function update(Request $request, $id) {}
}
