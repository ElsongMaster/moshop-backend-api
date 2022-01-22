<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route public
Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login', [AuthController::class, 'login']);
Route::get('/v1/mg/shop', [ShopController::class, 'mgshop']);
Route::get('/v1/shops', [ShopController::class, 'index']);

// Route privée
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/v1/user', [AuthController::class, 'index']);
    Route::post('/v1/logout', [AuthController::class, 'logout']);
    Route::put('/v1/user', [AuthController::class, 'update']);
    Route::put('/v1/user/picture', [AuthController::class, 'updatePicture']);
    Route::get('/v1/shop', [ShopController::class, 'personnalShop']);
    Route::get('/v1/shop/{id}', [ShopController::class, 'show']);

});

// Route::get('/v1/users',function(){
//     return User::all();
// });
// Route::get('/v1/products',function(){
//     return Product::all();
// });
