<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    public function create(Order $order)
    {
        $order->save();
    }

    public function addToOrder(Order $order)
    {
    }

    public function show()
{
    $user_id = Auth::id();
    $orders = Order::with('foods')->where('user_id', $user_id)->orderBy('date', 'desc')->get();

    foreach ($orders as $order) {
        $total = 0.0;

        foreach ($order->foods as $food) {
            $total += $food->price * $food->pivot->quantity;
        }

        $order->total = $total;
    }

    return view('order', ['orders' => $orders]);
}

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        // For each food in the order:
        foreach ($order->foods as $foods){
            // Remove from pivot table
            $order->foods()->detach($foods->id);
        }
        $order->delete();
        // $order->food()->detach($food_id);
        Session::flash('success', 'Successfully deleted order from order history.');
        return redirect('/order');
    }

    public function updateCart(Request $req)
    {
        if(Auth::check()){
            if(Session::get('cart') == null) {
                Session::put('cart', array());
            }
            // $food = Food::findOrFail($req['id']);
            // $order->food()->attach($food, ['quantity' => $req['quantity']]);
            // $order->deliveryAddress = 'aaa';

            // Check if in the cart session array already has the same food_id in any of its sub-array's 'id' key.
            $foodExists = false;    // variable for whether that food exists in the cart session array

            // If have, need to add to that quantity, don't push a new array to the cart session array.
            if (is_array(Session::get('cart'))) {
                $cart_arr = Session::get('cart');
                $cart_id = -1;
                foreach ($cart_arr as $subarray) {
                    $cart_id++;
                    // Cart session array consists of subarrays.
                    // Check if array key 'id' is set and whether it is equals to $value that we put into the function
                    if (isset($subarray['id']) && $subarray['id'] == $req->id) {
                        // If true, set $foodExists to true.
                        $foodExists = true;
                        // Increment the food in the cart session array by the specified quantity.
                        Session::increment('cart.'.$cart_id.'.quantity', $req->quantity);
                        Session::save();
                        break;  // break out of this foreach loop
                    }
                }

                // If don't have, push a new array to the cart session array.
                if(!$foodExists) {
                    $food = [
                        'id' => $req->id,
                        'name' => $req->name,
                        'price' => $req->price,
                        'picture' => $req->picture,
                        'quantity' => $req->quantity,
                        // 'message' => $req->message, // NEW
                    ];

                    Session::push('cart', $food);
                }

                Session::flash('success', 'Successfully added to cart.');
            }

            return '/home';
        }
        else {
            Session::flash('info', 'You must be logged in to add to cart and place orders.');
            return '/login';
        }
    }

    public function removeFromCart($id)
    {
        // Function that returns a new cart that doesn't include the food that is to be removed
        function getNewCart($array, $key, $value, $cart_id)
        {
            $cart_arr = array();
            $results = array();

            // check if it is an array
            if (is_array($array)) {

                foreach ($array as $subarray) {
                    $cart_id++;
                    // Cart session array consists of subarrays.
                    // Check if array key 'id' is set and whether it is equals to $value that we put into the function
                    if (isset($subarray[$key]) && $subarray[$key] == $value) {
                        // If true, assign this array to array $results
                        $subarray['cart_id'] = $cart_id;
                        $results[] = $subarray; // $results contains food that is to be deleted
                        break;  // break out of this foreach loop
                    }
                }
            }

            $cart_arr = $array;
            array_splice($cart_arr, $cart_id, 1);   // remove the item from the cart array
            return $cart_arr;
        }

        $new_cart_arr = getNewCart(Session::pull('cart'), 'id', $id, -1);
        Session::save();

        // Replace the existing array in the cart session with $new_cart_arr
        Session::put('cart', $new_cart_arr);

        Session::flash('success', 'Successfully removed from cart.');
        return redirect('/cart');
    }

    public function placeOrder(Request $req)
{
    $order = Order::create([
        'user_id' => Auth::id(),
        'date' => Carbon::now(),
        'type' => $req->type,
        'deliveryAddress' => $req->address,
    ]);

    $cart_arr = Session::pull('cart');

    foreach ($cart_arr as $value) {
        $food = Food::findOrFail($value['id']);
        $order->foods()->attach($food, [
            'quantity' => $value['quantity'],
            // 'message' => $value['message'] ?? null  // Add this line
        ]);
    }

    Session::flash('success', 'Successfully placed order.');
    return redirect('/order');
}

public function index()
{
    $orders = Order::with('foods', 'feedback')->where('user_id', auth()->id())->get();
    return view('order', compact('orders'));
}

public function updateCartItem(Request $request, $food_id)
{
    $request->validate([
        'quantity' => 'required|integer|min:1',
        // 'message' => 'nullable|string|max:255',
    ]);

    $cart = session('cart', []);
    foreach ($cart as $key => $item) {
        if ($item['id'] == $food_id) {
            $cart[$key]['quantity'] = $request->quantity;
            // $cart[$key]['message'] = $request->message;
            break;
        }
    }
    session(['cart' => $cart]);
    return redirect()->back()->with('success', 'Cart item updated successfully.');
}


}
