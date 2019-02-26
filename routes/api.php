<?php

use Illuminate\Http\Request;

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

Route::post('clients/create', 'API\ClientController@store')->name('clients.store');
Route::get('clients/wallet/balance', 'API\ClientController@balance')->name('clients.balance');


Route::post('wallets/charge', 'API\WalletController@charge')->name('wallets.charge');
Route::post('wallets/payrequest', 'API\WalletController@payRequest')->name('wallets.payRequest');
Route::post('wallets/paycheck', 'API\WalletController@payCheck')->name('wallets.payCheck');
