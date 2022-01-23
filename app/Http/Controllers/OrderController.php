<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Orderdetail;
use App\Models\Orderdetails;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $rq)
    {
        $user = $rq->user();
        return response([
            "message" => "succès",
            "data" => $user->orders()->get(),
            "status" => 200,
            "error" => []
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $rq)
    {
        $user = $rq->user();
        if (count($user->cart->products()->get()) == 0) {
            return response([
                "message" => "Le panier n'a pas pu être acheté",
                "data" => $user->cart->products()->get(),
                "status" => 400,
                "error" => [
                    "message" => "le panier est vide"
                ]
            ]);
        }
        $newOrder = new Order;
        $newOrder->date = now();
        $newOrder->user_id = $user->id;
        // dd($user->cart->products()->get());
        foreach ($user->cart->products()->get() as $product) {
            // create detail order
            $newOrderdetail = new Orderdetail;
            $newOrderdetail->quantity = $product->cartdetail->quantity;
            $newOrderdetail->product_id = $product->id;


            $newOrder->price += ($product->price * $product->cartdetail->quantity);
            $newOrder->save();
            $newOrderdetail->order_id = $newOrder->id;
            $newOrderdetail->save();

            // take out product of the cart
            $product->cart_id = null;

            // Delete the cartDetail of product
            $product->cartdetail->delete();
            $product->save();
        }

        // mise à zéro du panier
        $user->cart->quantity = 0;
        $user->cart->save();



        return response([
            "message" => "Le contenu du panier a été acheté",
            "data" => $newOrder,
            "status" => 200,
            "error" => []
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response(["message" => "the shop doesn't exist", "status" => 401]);
        }

        $orders =  $order->orderdetails()->get();
        $order_items = [];
        $products = [];
        foreach ($order->orderdetails()->get() as $orderdetail) {
            $obj = [
                "detailOrder"=>$orderdetail,
                "product"=>Product::find($orderdetail->product_id)
            ];
            array_push($order_items, $obj);
        }



        // dd($order_items);

        return response()->json([
            "message" => "Succès",
            "data" => [
                "order" => $order,
                "order_items" => $order_items
            ],
            "status" => 200,
            "error" => []

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function buy(Request $request)
    {
    }
}
