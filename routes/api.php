<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CompanieController;
use App\Http\Controllers\Api\Pubs\PubController;
use App\Http\Controllers\Api\CarteEcomController;
use App\Http\Controllers\Api\AuthanticedController;
use App\Http\Controllers\Api\Pubs\SliderController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\Mailsend\MailController;
use App\Http\Controllers\Api\Pubs\PartenaireController;
use App\Http\Controllers\Api\CostumerCompanieController;
use App\Http\Controllers\Api\Promotions\PromotionController;
use App\Http\Controllers\Api\Pageview\PageViewModelController;

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

//companie

Route::post('createcompany',[CompanieController::class,'create_company']);
Route::get('getcompany',[CompanieController::class,'index']);
Route::post('logincompany',[CompanieController::class,'login_company']);
Route::post('create/client/company',[CompanieController::class,'createClient'])->middleware(['auth:sanctum','abilities:companie']);
Route::get('get/client',[CompanieController::class,'get_client'])->middleware(['auth:sanctum','abilities:companie']);
Route::post('change/Password/Company',[CompanieController::class,'changePasswordCompany'])->middleware(['auth:sanctum','abilities:companie']);
Route::post('upadate/company/{id}',[CompanieController::class,'upadate_company'])->middleware(['auth:sanctum','abilities:companie']);

Route::post('upadate/company/image/{id}',[CompanieController::class,'upadate_company_image'])->middleware(['auth:sanctum','abilities:companie']);

Route::put('Update/Client/{id}',[CompanieController::class,'UpdateClient'])->middleware(['auth:sanctum','abilities:companie']);

//create carte controller
Route::get('get/cart/with/company/transaction/{cartcode}',[CartController::class,'getcartwithcompanytransaction'])->middleware(['auth:sanctum','abilities:companie']);
Route::post('create/cart',[CartController::class,'create_cart'])->middleware(['auth:sanctum','abilities:companie']);
Route::post('ActivDesactiveCart/{code}',[CartController::class,'ActivDesactiveCart'])->middleware(['auth:sanctum','abilities:companie']);
Route::put('edit/cart/{id}',[CartController::class,'edit_cart'])->middleware(['auth:sanctum','abilities:companie']);
Route::get('get/cart/with/company',[CartController::class,'getcartwithcompany'])->middleware(['auth:sanctum','abilities:companie']);
Route::post('add/amount/with/cart/{code}',[CartController::class,'addamountwithcart'])->middleware(['auth:sanctum','abilities:companie']);
Route::post('remove/amount/with/cart/{id}',[CartController::class,'removeamountwithcart'])->middleware(['auth:sanctum','abilities:companie']);
Route::post('verifyCart/{id}',[CartController::class,'verifyCart'])->middleware(['auth:sanctum','abilities:companie']);
//logaout company
Route::post('logout/company',[CompanieController::class,'logout_company'])->middleware(['auth:sanctum','abilities:companie']);


//client companie controller

Route::post('login/costumer',[CostumerCompanieController::class,'login_costumer']);
Route::get('getauthcart',[CostumerCompanieController::class,'getauthcart'])->middleware(['auth:sanctum','abilities:companiecostumer']);
Route::get('getauthcarttransaction/{code}',[CostumerCompanieController::class,'getauthcarttransaction'])->middleware(['auth:sanctum','abilities:companiecostumer']);
Route::get('getalltransaction/{code}',[CostumerCompanieController::class,'getalltransaction'])->middleware(['auth:sanctum','abilities:companiecostumer']);
Route::get('gettransactionoffweek/{code}',[CostumerCompanieController::class,'gettransactionoffweek'])->middleware(['auth:sanctum','abilities:companiecostumer']);
Route::get('gettransactionoffmonth/{code}',[CostumerCompanieController::class,'gettransactionoffmonth'])->middleware(['auth:sanctum','abilities:companiecostumer']);
Route::post('logout/user',[CostumerCompanieController::class,'logout_user'])->middleware(['auth:sanctum','abilities:companiecostumer']);



//transaction controller
Route::post('new/transaction',[TransactionController::class,'new_transaction'])->middleware(['auth:sanctum','abilities:companiecostumer']);
Route::get('cancel/transaction/{codetansaction}',[TransactionController::class,'cancel_transaction'])->middleware(['auth:sanctum','abilities:companiecostumer']);

// a revoir pour le metter aussi le montant dans le paramètres
Route::post('validate/transaction/{codetansaction}',[TransactionController::class,'validate_transaction'])->middleware(['auth:sanctum','abilities:companie']);

Route::post('payement/carte/neworder',[TransactionController::class,'payement']);




//ecommerce  controller
