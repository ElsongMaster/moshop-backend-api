<?php

namespace App\Http\Controllers;

use App\Models\Cartdetail;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
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
            "message"=>"succès",
            "data"=>$user->cart->products()->get(),
            "status"=>200,
            "error"=>[]
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
        $messages = array(
            'product.exists' => [
                'rules' => "exists",
                'field' => "product",
                'message' => "Le produit n'existe pas",
            ],

        );

        $rules = array(
            'product' => 'required|numeric|exists:products,id',
            'quantity' => 'required|numeric|min:1|max:99',
        );
        $validator = Validator::make($rq->all(), $rules, $messages);

        if ($validator->fails()) {
            return response([
                "message" => "Le produit n'a pas pu être ajouter au panier.",
                "data" => [],
                "status" => 400,
                "error" => [
                    "flashToSession" => false,
                    "messages" => [
                        "errors" => $validator->errors()
                    ]
                ]
            ]);
        }

        $product = Product::find($rq->product);
        if ($product->quantity - $rq->quantity >= 0) {
            $user = $rq->user();
            // dd($user);
            $personnalCart = $user->cart;
            $product->cart_id = $personnalCart->id;
            $product->quantity - $rq->quantity;
            $product->save();

            // details of product in the cart
            $newCartdetail  = new Cartdetail;
            $newCartdetail->quantity = $rq->quantity;
            $newCartdetail->product_id = $product->id;
            $newCartdetail->save();
        }

        return response([
            "message" => "le produit a été ajouté au panier.",
            "data" => [],
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
}
