<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $products = [];

        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            if ($product) {
                $products[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image,
                    'quantity' => $item['quantity'],
                ];
            }
        }

        return Inertia::render('Shop/Cart', [
            'cartItems' => $products
        ]);
    }

    public function add(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id', 'quantity' => 'integer|min:1']);

        $cart = session()->get('cart', []);
        $id = $request->product_id;
        $qty = $request->quantity ?? 1;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $qty;
        } else {
            $cart[$id] = ['quantity' => $qty];
        }

        session()->put('cart', $cart);
        return redirect()->back();
    }

    public function update(Request $request)
    {
        $request->validate(['product_id' => 'required', 'quantity' => 'required|integer|min:1']);

        $cart = session()->get('cart', []);
        $cart[$request->product_id]['quantity'] = $request->quantity;
        session()->put('cart', $cart);
        return redirect()->back();
    }

    public function remove(Request $request)
    {
        $request->validate(['product_id' => 'required']);

        $cart = session()->get('cart', []);
        unset($cart[$request->product_id]);
        session()->put('cart', $cart);
        return redirect()->back();
    }
}