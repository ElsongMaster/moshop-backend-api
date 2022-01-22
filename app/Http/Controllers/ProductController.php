<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Faker\Factory as Faker;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $rq)
    {

        $validator = Validator::make($rq->all(),[
            'name'=>'required|string|min:4',
            'description'=>'required|string|min:4',
            'price'=>'required|string',
            'cover' => 'required|max:2048||mimes:jpeg,jpg,png',
           ]);
        // $rq->validate([
        //     'name'=>'required|string|min:4',
        //     'description'=>'required|string|min:4',
        //     'price'=>'required|string',
        //     'cover' => 'required|max:2048||mimes:jpeg,jpg,png',
        // ]);

        if($validator->fails()){
            // dd($validator);
            return response([
                "message"=>"Votre produit n'a pas pu être crée.",
                "data"=>[],
                "status"=>400,
                "error"=>[
                    "flashToSession"=>false,
                    "messages"=>[
                        "errors"=>$validator->errors()
                    ]
                ]
            ]);
        }

        $faker = Faker::create();
        $newProduct = new Product;
        $newProduct->name = $rq->name;
        $newProduct->description = $rq->description;
        $rq->file('cover')->storePublicly('img/uploads','public');
        $newProduct->cover_path = "/uploads/".$rq->file('cover')->hashName();
        $newProduct->price = $rq->price;
        $newProduct->quantity = $faker->randomNumber( $min = 5, $max = 30);

        $newProduct->shop_id = $rq->user()->shop->id;
        $newProduct->cart_id = null;
        $newProduct->created_at= now() ;
        $newProduct->updated_at= now() ;
        $newProduct->save();

        return response([
            "message"=>"Votre produit a été crée.",
            "data"=>$newProduct,
            "status"=>200,
            "error"=>[]
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
        $product = Product::find($id);


        if(!$product){
            return response(["message"=>"the product doesn't exist","status"=>401]);
        }

        return response()->json([
            "message"=>"Succès",
            "data"=>$product,
            "status"=>200,
            "error"=>[]

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $rq, $id)
    {
        $validator = Validator::make($rq->all(),[
            'name'=>'required|string|min:4',
            'description'=>'required|string|min:4',
            'price'=>'required|string',
           ]);
        // $rq->validate([
        //     'name'=>'required|string|min:4',
        //     'description'=>'required|string|min:4',
        //     'price'=>'required|string',
        //     'cover' => 'required|max:2048||mimes:jpeg,jpg,png',
        // ]);

        if($validator->fails()){
            // dd($validator);
            return response([
                "message"=>"Votre produit n'a pas pu être crée.",
                "data"=>[],
                "status"=>400,
                "error"=>[
                    "flashToSession"=>false,
                    "messages"=>[
                        "errors"=>$validator->errors()
                    ]
                ]
            ]);
        }


        $product = Product::find($id);
        
        if(!$product){
            return response([
                "message"=>"Votre produit n'a pas pu être modifié.",
                "data"=>[],
                "status"=>400,
                "error"=>[
                    "flashToSession"=>false,
                    "messages"=>[
                        "errors"=>"Le produit n'existe pas"
                    ]
                ]
            ]);
        }
        $product->name = $rq->name;
        $product->description = $rq->description;
        $product->price = $rq->price;
        $product->updated_at= now() ;
        $product->save();

        return response([
            "message"=>"Le produit a bien été modifié.",
            "data"=>$product,
            "status"=>200,
            "error"=>[]
        ]);
    }

 /**
     * Update the picture in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePicture(Request $rq, $id)
    {
        // dd($request->all());

        $rq->validate([
            'cover' => 'required|max:2048||mimes:jpeg,jpg,png',
        ]);
        $product = Product::find($id);

        if(!$product){
            return response([
                "message"=>"Votre produit n'a pas pu être modifié.",
                "data"=>[],
                "status"=>400,
                "error"=>[
                    "flashToSession"=>false,
                    "messages"=>[
                        "errors"=>"Le produit n'existe pas"
                    ]
                ]
            ]);           
        }
        Storage::disk('public')->delete('img/' . $product->picture);
        $product->cover_path = $rq->file('cover')->hashName();
        $rq->file('cover')->storePublicly('img/uploads/', 'public');

        $product->save();

        return response([
            "message" => "le produit a bien été modifié.",
            "data" =>$product,
            "status" => 200,
            "error" => []
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if(!$product){
            return response([
                "message"=>"votre produit n'as pas été supprimé.",
                "data"=>[],
                "status"=>400,
                "error"=>[
                    "message"=>"le produit n'existe pas"
                ]
            ]);


        }
        // suppression dans la table commande
        $product->cart_id = null;
        $product->save();

        if(Storage::disk('public')->exists('img/'.$product->cover_path)){
            Storage::disk('public')->delete('img/'.$product->cover_path);
        }
        $product->delete();
        return response([
            "message"=>"votre produit a bien été supprimé.",
            "data"=>[],
            "status"=>200,
            "error"=>[]
        ]);
    }
}
