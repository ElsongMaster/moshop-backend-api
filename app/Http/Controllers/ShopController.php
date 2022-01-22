<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use stdClass;
class ShopController extends Controller
{

    public function index(){
        return response()->json([
            "message"=>"Succès",
            "data"=>Shop::all(),
            "status"=>200,
            "error"=>[]                      
        ]);
    }
    public function mgshop(){
        $Moshop = Shop::where("user_id", "=", 1)->get()[0];
        $user = User::find(1);
        $products = [];

        foreach ($Moshop->products->all() as $product) {
            $objProduct = new stdClass;
            $objProduct->name = $product->name;
            $objProduct->description = $product->description;
            $objProduct->cover_path = $product->cover_path;
            $objProduct->price = $product->price;
            $objProduct->shop_id = 1;
            $objProduct->created_at = $product->created_at;
            $objProduct->updated_at = $product->updated_at;

            array_push($products, $objProduct);
        }
        $response = [
            "message"=>"Succès",
            "data"=>[
                "id"=>1,
                "name"=>"La boutique de Sami de Molengeek",
                "user_id"=>$Moshop->user_id,
                "created_at"=>$Moshop->created_at,
                "update_at"=>$Moshop->updated_at?$Moshop->updated_at:$Moshop->created_at,
                "user"=>[
                    "id"=>1,
                    "email"=>$user->email,
                    "created_at"=>$user->created_at,
                    "update_at"=>$user->updated_at?$user->updated_at:$user->created_at,
                ],
                "products"=>$products

            ],
            "status"=>200,
            "error"=>[]

            ];

            return response($response);
    }

    public function personnalShop(Request $rq){
        $personnalShop = $rq->user()->shop;
        return response()->json([
            "message"=>"Succès",
            "data"=>[
                "id"=>1,
                "name"=>$personnalShop->name,
                "user_id"=>$personnalShop->user_id,
                "created_at"=>$personnalShop->created_at,
                "update_at"=>$personnalShop->updated_at?$personnalShop->updated_at:$personnalShop->created_at,
                "products"=>$personnalShop->products()->get()         
            ],
            "status"=>200,
            "error"=>[]

        ]);
    }


    public function show($id){
        $shop = Shop::find($id);


        if(!$shop){
            return response(["message"=>"the shop doesn't exist","status"=>401]);
        }

        return response()->json([
            "message"=>"Succès",
            "data"=>[
                "id"=>$shop->id,
                "name"=>$shop->name,
                "user_id"=>$shop->user_id,
                "created_at"=>$shop->created_at,
                "update_at"=>$shop->updated_at?$shop->updated_at:$shop->created_at,
                "products"=>$shop->products()->get()         
            ],
            "status"=>200,
            "error"=>[]

        ]);
    }
}
