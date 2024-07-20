<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\GroupMenuController;
use App\Http\Controllers\MachineryPurchaseController;
use App\Http\Controllers\OptionMenuController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\SparePartController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TypeUserController;
use App\Http\Controllers\UserController;
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

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/repuestos', [PdfController::class, 'getRepuestos'])->name('repuesto');

Route::group(
    ['middleware' => ['auth:sanctum']],
    function () {

        Route::get('/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

//        GROUP MENU
        Route::resource('groupmenu', GroupMenuController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'groupmenu.index',
                'store' => 'groupmenu.store',
                'show' => 'groupmenu.show',
                'update' => 'groupmenu.update',
                'destroy' => 'groupmenu.destroy',
            ]
        );

//        OPTION MENU
        Route::resource('optionmenu', OptionMenuController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'optionmenu.index',
                'store' => 'optionmenu.store',
                'show' => 'optionmenu.show',
                'update' => 'optionmenu.update',
                'destroy' => 'optionmenu.destroy',
            ]
        );

//        TYPE USER
        Route::resource('typeuser', TypeUserController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'typeuser.index',
                'store' => 'typeuser.store',
                'show' => 'typeuser.show',
                'update' => 'typeuser.update',
                'destroy' => 'typeuser.destroy',
            ]
        );

//        ACCESS
        Route::resource('access', AccessController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'access.index',
                'store' => 'access.store',
                'show' => 'access.show',
                'update' => 'access.update',
                'destroy' => 'access.destroy',
            ]
        );

//        USER
        Route::resource('user', UserController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'user.index',
                'store' => 'user.store',
                'show' => 'user.show',
                'update' => 'user.update',
                'destroy' => 'user.destroy',
            ]
        );

//        COUNTRY
        Route::resource('country', CountryController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'country.index',
                'store' => 'country.store',
                'show' => 'country.show',
                'update' => 'country.update',
                'destroy' => 'country.destroy',
            ]
        );

//        SUPPLIER
        Route::resource('supplier', SupplierController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'supplier.index',
                'store' => 'supplier.store',
                'show' => 'supplier.show',
                'update' => 'supplier.update',
                'destroy' => 'supplier.destroy',
            ]
        );

//        CLIENT
        Route::resource('client', ClientController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'client.index',
                'store' => 'client.store',
                'show' => 'client.show',
                'update' => 'client.update',
                'destroy' => 'client.destroy',
            ]
        );

//        SPARE PART
        Route::resource('sparepart', SparePartController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'sparepart.index',
                'store' => 'sparepart.store',
                'show' => 'sparepart.show',
                'update' => 'sparepart.update',
                'destroy' => 'sparepart.destroy',
            ]
        );

//        MACHINERY PURCHASE
        Route::resource('machinerypurchase', MachineryPurchaseController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'machinerypurchase.index',
                'store' => 'machinerypurchase.store',
                'show' => 'machinerypurchase.show',
                'update' => 'machinerypurchase.update',
                'destroy' => 'machinerypurchase.destroy',
            ]
        );

//


    }
);
