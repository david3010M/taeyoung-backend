<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DetailSparePartController;
use App\Http\Controllers\GroupMenuController;
use App\Http\Controllers\MachineryPurchaseController;
use App\Http\Controllers\OptionMenuController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\QuotationController;
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
        Route::resource('sparePart', SparePartController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'sparePart.index',
                'store' => 'sparePart.store',
                'show' => 'sparePart.show',
                'update' => 'sparePart.update',
                'destroy' => 'sparePart.destroy',
            ]
        );

//        QUOTATION
        Route::resource('quotation', QuotationController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'quotation.index',
                'store' => 'quotation.store',
                'show' => 'quotation.show',
                'update' => 'quotation.update',
                'destroy' => 'quotation.destroy',
            ]
        );

//        MACHINERY PURCHASE
        Route::resource('machineryPurchase', MachineryPurchaseController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'machineryPurchase.index',
                'store' => 'machineryPurchase.store',
                'show' => 'machineryPurchase.show',
                'update' => 'machineryPurchase.update',
                'destroy' => 'machineryPurchase.destroy',
            ]
        );

//        CURRENCY
        Route::resource('currency', CurrencyController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'currency.index',
                'store' => 'currency.store',
                'show' => 'currency.show',
                'update' => 'currency.update',
                'destroy' => 'currency.destroy',
            ]
        );

//        DETAIL SPARE PART
        Route::resource('detailSparePart', DetailSparePartController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'detailSparePart.index',
                'store' => 'detailSparePart.store',
                'show' => 'detailSparePart.show',
                'update' => 'detailSparePart.update',
                'destroy' => 'detailSparePart.destroy',
            ]
        );


    }
);
