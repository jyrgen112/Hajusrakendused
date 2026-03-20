<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) return redirect()->route('cart');

        $items = [];
        $total = 0;

        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            if ($product) {
                $items[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                ];
                $total += $product->price * $item['quantity'];
            }
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $paymentIntent = PaymentIntent::create([
            'amount' => (int)($total * 100),
            'currency' => 'eur',
        ]);

        return Inertia::render('Shop/Checkout', [
            'cartItems' => $items,
            'total' => $total,
            'stripeKey' => config('services.stripe.key'),
            'clientSecret' => $paymentIntent->client_secret,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'payment_intent_id' => 'required|string',
        ]);

        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            if ($product) $total += $product->price * $item['quantity'];
        }

        // Verify payment with Stripe
        Stripe::setApiKey(config('services.stripe.secret'));
        $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

        if ($paymentIntent->status !== 'succeeded') {
            return back()->withErrors(['payment' => 'Payment failed. Please try again.']);
        }

        // Create order
        $order = Order::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'total' => $total,
            'status' => 'paid',
            'stripe_payment_intent' => $request->payment_intent_id,
        ]);

        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            if ($product) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);
            }
        }

        session()->forget('cart');

        return Inertia::render('Shop/Success', [
            'order' => $order
        ]);
    }
}