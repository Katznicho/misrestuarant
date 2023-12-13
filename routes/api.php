<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post("/login", [ApiController::class , "login"]);

//protect all routes
Route::group(['middleware' => ['auth:sanctum']], function () {

    //logout user
    Route::post('/logout', [ApiController::class , "logout"]);
    Route::post('/deposit', [ApiController::class , "deposit"]);
    Route::post('/withdraw', [ApiController::class , "withdraw"]);
    Route::post("getTransactionsByUserId", [ApiController::class , "getTransactionsByUserId"]);

        //get cutsomer details by card number
    Route::post('/customer', [ApiController::class , "getCustomer"]);



    //delete customer
});


Route::post("registerIPN", [PaymentController::class, "registerIPN"]);
Route::get("listIPNS", [PaymentController::class, "listIPNS"]);
Route::get("completePayment", [PaymentController::class, "completePayment"]);
Route::post("processOrder", [PaymentController::class, "processOrder"]);

Route::post("testSendingMessages", [PaymentController::class, "testSendingMessages"]);

Route::post("checkTransactionStatus", [PaymentController::class, "checkTransactionStatus"]);

//customer routes
Route::post("validateCustomerPin", [ApiController::class, "validateCustomerPin"]);
Route::post("loginCustomerWithPhoneNumber", [ApiController::class, "loginCustomerWithPhoneNumber"]);
Route::post("logoutCustomer", [ApiController::class, "logoutCustomer"]);
Route::post("changeCustomerPin", [ApiController::class, "changeCustomerPin"]);
Route::post("getCustomerBalance", [ApiController::class, "getCustomerBalance"]);
Route::post("getCustomerTransactions", [ApiController::class, "getCustomerTransactions"]);

