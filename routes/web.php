<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\TravelPackageController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\TransactionController;
use Illuminate\Support\Facades\Auth;

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

Route::get('/', [HomeController::class, 'index'])
    ->name('home');
    
Route::get('/detail/{slug}', [DetailController::class, 'index']) //slug untuk mengidentifikasi halaman yg di klik dari home
    ->name('detail');

Route::post('/checkout/{id}', [CheckoutController::class, 'process']) //utk memproses data dari checkout parameter id karna pake id user, pake post karna mengirim data
    ->name('checkout_process')
    ->middleware(['auth','verified']);

Route::get('/checkout/{id}', [CheckoutController::class, 'index']) //dari proses masuk ke sini, idnya adalah id transaction
    ->name('checkout')
    ->middleware(['auth','verified']);

Route::post('/checkout/create/{detail_id}', [CheckoutController::class, 'create']) //untuk nambahin anggota baru yg ikut perjalanan travel
    ->name('checkout-create')
    ->middleware(['auth','verified']);

Route::get('/checkout/remove/{detail_id}', [CheckoutController::class, 'remove']) //menghapus anggota yg ikut
    ->name('checkout-remove')
    ->middleware(['auth','verified']);

Route::get('/checkout/confirm/{id}', [CheckoutController::class, 'success']) //untuk succes klo udah success
    ->name('checkout-success')
    ->middleware(['auth','verified']);

Route::get('/admin', [DashboardController::class, 'index'])
    ->middleware(['auth','admin'])
    ->name('DashboardAdmin');

Route::resource('travel-package', TravelPackageController::class);
Route::resource('gallery', GalleryController::class);
Route::resource('transaction', TransactionController::class);

Auth::routes(['verify' => true]);
