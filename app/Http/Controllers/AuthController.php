<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Faker\Factory as Faker;
class AuthController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response([
                "message" => "Vous n'êtes pas connecté.",
                "data" => [],
                "status" => 401,
                "error" => []
            ]);
        }


        return response([
            "message" => "Succès.",
            "data" => [
                "id" => $user->id,
                "email" => $user->email,
                "created_at" => $user->created_at,
                "updated_at" => $user->update_at,
                "profile" => [
                    "firstname" => $user->firstname,
                    "lastname" => $user->lastname,
                    "picture_path" => $user->picture,
                    "user_id" => $user->id,
                    "created_at" => $user->created_at,
                    "updated_at" => $user->update_at,
                ]
            ],
            "status" => 200,
            "error" => []
        ]);
    }
    public function register(Request $request)
    {
         $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string',
            'picture' => 'required|max:2048||mimes:jpeg,jpg,png',
        ]);
        // try {

        //     $fields = $request->validate([
        //         'firstname' => 'required|string',
        //         'lastname' => 'required|string',
        //         'email' => 'required|string|unique:users,email',
        //         'password' => 'required|string',
        //         'picture' => 'required|max:2048||mimes:jpeg,jpg,png',
        //     ]);
        // } catch (Request $rq) {
        //     dd($rq->errors());
        // }

        // dd($fields);
        // Sauvegarde de l'image
        $request->file('picture')->storePublicly('img/users', 'public');
        $user = new User;
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->picture = $request->file('picture')->hashName();
        $user->save();

        // Création du shop personnel
        $shopPersonnal = new Shop;
        $shopPersonnal->name =  "La boutique de ".$user->firstname." ".$user->lastname;
        $shopPersonnal->user_id = $user->id;
        $shopPersonnal->save();

        // panier personnel
        $personnalCart = new Cart;
        $personnalCart->quantity = 0;
        $personnalCart->user_id = $user->id;
        $personnalCart->save();


        //produit liée au shop
        $faker = Faker::create();
        for($i=0;$i<8;$i++){
            $product = new Product;
            $product->name = $faker->randomElement($array = array ("jus d'orange","coca","pomme","poire","baguette","sauce tomate","spaghetti","couque","fanta","gateau","bouteille d'eau","poisson","mangue","carotte","yahourt","pizza","nouilles")) ;
            $product->description = $faker->text(50);
            $product->cover_path = $faker->imageUrl($width = 640, $height = 480) ;
            $product->price = $faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 2);
            $product->quantity = $faker->randomNumber( $min = 1, $max = 30);
            $product->shop_id = $shopPersonnal->id;
           $product->cart_id = null;
            $product->created_at = now() ;
            $product->updated_at = now() ;
            $product->save();
        }





        return response()->json([
            "message" => "votre compte a bien été crée.",
            "data" => "{}",
            "status" => 200,
            "error" => "{}",
        ]);
    }

    public function login(Request $request)
    {

        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string|min:4',
        ]);

        // Getting user who want to login
        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['message' => 'Bad creds, email or password is not correct'], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'message' => 'Vous avez réussi à vous connecter',
            'data' => [
                'type' => 'bearer',
                'token' => $token,
            ],
            'status' => 200,
            'error' => []
        ];

        return response($response);
    }

    public function logout(Request $request)
    {
        // Getting the current connected user
        $user = $request->user();

        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        return [
            'message' => 'logged out',
            'data' => [],
            'status' => 200,
            'error' => []
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {


        $user = User::find($request->user()->id);
        // dd($request->all());
        $user->update($request->all());

        return response([
            "message" => "Votre profil a été mis à jour.",
            "data" => [


                "firstname" => $user->firstname,
                "lastname" => $user->lastname,
                "picture_path" => $user->picture,
                "user_id" => $user->id,
                "created_at" => $user->created_at,
                "updated_at" => $user->update_at,

            ],
            "status" => 200,
            "error" => []
        ]);
    }
    /**
     * Update the picture in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePicture(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'picture' => 'required|max:2048||mimes:jpeg,jpg,png',
        ]);
        $user = $request->user();
        Storage::disk('public')->delete('img/users/' . $user->picture);
        $user->image = $request->file('picture');
        $request->file('picture')->storePublicly('img/users/', 'public');

        $user->save();

        return response([
            "message" => "Votre profil a été mis à jour.",
            "data" => [


                "firstname" => $user->firstname,
                "lastname" => $user->lastname,
                "picture_path" => $user->picture,
                "user_id" => $user->id,
                "created_at" => $user->created_at,
                "updated_at" => $user->update_at,

            ],
            "status" => 200,
            "error" => []
        ]);
    }
}
