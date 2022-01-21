<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        $user = $request->user();
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->picture = $request->file('picture')->hashName();
        $user->save();
        // $user = User::create([
        //     'firstname' => $fields['firstname'],
        //     'lastname' => $fields['lastname'],
        //     'email' => $fields['email'],
        //     'password' => Hash::make($fields['password']),
        //     'picture' => $request->file('picture')->hashName(),
        // ]);


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
            return response(['message' => 'Bad creds'], 401);
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
