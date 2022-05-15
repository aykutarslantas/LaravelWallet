<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PromotionsController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('api/backoffice/promotion-codes', [PromotionsController::class, 'index']);
Route::get('api/backoffice/promotion-codes/{id}', [PromotionsController::class, 'index']);
Route::post('api/backoffice/promotion-codes', [PromotionsController::class, 'getPromotionCode']);
Route::post('api/assign-promotion', [PromotionsController::class, 'assignPromotion']);
