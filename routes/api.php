<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\AccountPayableController;
use App\Http\Controllers\AccountReceivableController;
use App\Http\Controllers\ExtensionController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DetailSparePartController;
use App\Http\Controllers\GroupMenuController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\OptionMenuController;
use App\Http\Controllers\PaymentConceptController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SparePartController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TypeUserController;
use App\Http\Controllers\UnitController;
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

Route::group(
    ['middleware' => ['auth:sanctum']],
    function () {

        Route::get('/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/logs', [AuthController::class, 'logs'])->name('logs');

//        REPORTS
        Route::get('/repuestos', [PdfController::class, 'getRepuestos'])->name('repuesto');

//         SEARCH
        Route::get('searchByDni/{dni}', [SearchController::class, 'searchByDni']);
        Route::get('searchByRuc/{ruc}', [SearchController::class, 'searchByRuc']);

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

//        BANK
        Route::resource('bank', BankController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'bank.index',
                'store' => 'bank.store',
                'show' => 'bank.show',
                'update' => 'bank.update',
                'destroy' => 'bank.destroy',
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

//        DEPARTMENT
        Route::resource('department', DepartmentController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'department.index',
                'store' => 'department.store',
                'show' => 'department.show',
                'update' => 'department.update',
                'destroy' => 'department.destroy',
            ]
        );

//        PROVINCE
        Route::resource('province', ProvinceController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'province.index',
                'store' => 'province.store',
                'show' => 'province.show',
                'update' => 'province.update',
                'destroy' => 'province.destroy',
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
        Route::post('/client/excel', [ClientController::class, 'importExcel'])->name('client-excel');
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


//        UNIT
        Route::resource('unit', UnitController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'unit.index',
                'store' => 'unit.store',
                'show' => 'unit.show',
                'update' => 'unit.update',
                'destroy' => 'unit.destroy',
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
        Route::resource('purchase', PurchaseController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'purchase.index',
                'store' => 'purchase.store',
                'show' => 'purchase.show',
                'update' => 'purchase.update',
                'destroy' => 'purchase.destroy',
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

//        PAYMENT CONCEPT
        Route::resource('paymentConcept', PaymentConceptController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'paymentConcept.index',
                'store' => 'paymentConcept.store',
                'show' => 'paymentConcept.show',
                'update' => 'paymentConcept.update',
                'destroy' => 'paymentConcept.destroy',
            ]
        );

//        SALE
        Route::resource('sale', SaleController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'sale.index',
                'store' => 'sale.store',
                'show' => 'sale.show',
                'update' => 'sale.update',
                'destroy' => 'sale.destroy',
            ]
        );

//        ACCOUNT RECEIVABLE
        Route::resource('accountReceivable', AccountReceivableController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'accountReceivable.index',
                'store' => 'accountReceivable.store',
                'show' => 'accountReceivable.show',
                'update' => 'accountReceivable.update',
                'destroy' => 'accountReceivable.destroy',
            ]
        );
        Route::post('accountReceivable/{id}/payment', [AccountReceivableController::class, 'storePayment'])->name('accountReceivable.payment');
        Route::delete('accountReceivable/deletePayment/{id}', [AccountReceivableController::class, 'deletePayment'])->name('accountReceivable.deletePayment');

//        ACCOUNT PAYABLE
        Route::resource('accountPayable', AccountPayableController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'accountPayable.index',
                'store' => 'accountPayable.store',
                'show' => 'accountPayable.show',
                'update' => 'accountPayable.update',
                'destroy' => 'accountPayable.destroy',
            ]
        );
        Route::post('accountPayable/{id}/payment', [AccountPayableController::class, 'storePayment'])->name('accountPayable.payment');
        Route::delete('accountPayable/deletePayment/{id}', [AccountPayableController::class, 'deletePayment'])->name('accountPayable.deletePayment');

//    EXTENSION
        Route::resource('extension', ExtensionController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
            ->names(['index' => 'extension.index', 'store' => 'extension.store', 'show' => 'extension.show',
                'update' => 'extension.update', 'destroy' => 'extension.destroy']);


    }
);
