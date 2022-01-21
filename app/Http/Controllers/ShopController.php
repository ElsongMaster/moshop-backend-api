<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use stdClass;
class ShopController extends Controller
{
    public function index(){
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
}
