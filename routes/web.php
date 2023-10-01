<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

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

Route::redirect('/', '/login');
Auth::routes();

Route::controller(HomeController::class)->group(function () {
    Route::get('/home', 'index')->name('home');
    Route::post('/deposit', 'depositAmount')->name('amount.deposit');
    Route::post('/withdraw', 'withdrawAmount')->name('amount.withdraw');
    Route::post('/transfer', 'transferAmount')->name('amount.transfer');
    Route::post('/fetch_statements', 'fetchStatementData')->name('fetch.statements');
});