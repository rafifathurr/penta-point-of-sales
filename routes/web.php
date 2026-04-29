<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\PaymentMethod\PaymentMethodController;
use App\Http\Controllers\Product\CategoryProductController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\SalesOrder\SalesOrderController;
use App\Http\Controllers\Stock\StockInController;
use App\Http\Controllers\Stock\StockOutController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

/**
 * Home Route
 */
Route::group(['middleware' => 'auth'], function () {
    Route::get('/', function () {
        return view('home');
    })->name('home');
});

/**
 * Admin Route Access
 */
Route::group(['middleware' => ['role:admin']], function () {
    /**
     * Route Dashboard Method Module
     */
    Route::group(['controller' => DashboardController::class, 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('sales-order', [DashboardController::class, 'salesOrder'])->name('sales-order');
        Route::get('stock', [DashboardController::class, 'stock'])->name('stock');
        Route::get('coa', [DashboardController::class, 'coa'])->name('coa');
        Route::get('product', [DashboardController::class, 'product'])->name('product');
        Route::get('sales-order/profit-loss', [DashboardController::class, 'salesOrderProfitLoss'])->name('sales-order.profitLoss');
        Route::get('sales-order/datatable', [DashboardController::class, 'salesOrderDataTable'])->name('sales-order.dataTable');
        Route::get('sales-order/export', [DashboardController::class, 'salesOrderExport'])->name('sales-order.export');
        Route::get('stock/datatable', [DashboardController::class, 'stockDataTable'])->name('stock.dataTable');
        Route::get('stock/export', [DashboardController::class, 'stockExport'])->name('stock.export');
        Route::get('coa/datatable', [DashboardController::class, 'coaDataTable'])->name('coa.dataTable');
        Route::get('coa/export', [DashboardController::class, 'coaExport'])->name('coa.export');
    });
    
    /**
     * Route Stock In Module
     */
    Route::group(['controller' => StockInController::class, 'prefix' => 'stock-in', 'as' => 'stock-in.'], function () {
        Route::get('datatable', 'dataTable')->name('dataTable');
    });
    Route::resource('stock-in', StockInController::class)->parameters(['stock-in' => 'id']);

    /**
     * Route Stock Out Module
     */
    Route::group(['controller' => StockOutController::class, 'prefix' => 'stock-out', 'as' => 'stock-out.'], function () {
        Route::get('datatable', 'dataTable')->name('dataTable');
    });
    Route::resource('stock-out', StockOutController::class)->parameters(['stock-out' => 'id']);

    /**
     * Route Product Module
     */
    Route::resource('product', ProductController::class, ['except' => ['index', 'show']])->parameters(['product' => 'id']);

    /**
     * Route Category Product Module
     */
    Route::group(['controller' => CategoryProductController::class, 'prefix' => 'category-product', 'as' => 'category-product.'], function () {
        Route::get('datatable', 'dataTable')->name('dataTable');
    });
    Route::resource('category-product', CategoryProductController::class)->parameters(['category-product' => 'id']);

    /**
     * Route Payment Method Module
     */
    Route::group(['controller' => PaymentMethodController::class, 'prefix' => 'payment-method', 'as' => 'payment-method.'], function () {
        Route::get('datatable', 'dataTable')->name('dataTable');
    });
    Route::resource('payment-method', PaymentMethodController::class, ['except' => ['show']])->parameters(['payment-method' => 'id']);

    /**
     * Route User Module
     */
    Route::group(['controller' => UserController::class, 'prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('datatable', 'dataTable')->name('dataTable');
    });
    Route::resource('user', UserController::class, ['except' => ['show']])->parameters(['user' => 'id']);
});

/**
 * Admin and Cashier Route Access
 */
Route::group(['middleware' => ['role:admin|cashier']], function () {
    /**
     * Route Sales Order Module
     */
    Route::group(['controller' => SalesOrderController::class, 'prefix' => 'sales-order', 'as' => 'sales-order.'], function () {
        Route::get('datatable', 'dataTable')->name('dataTable');
        Route::get('invoice/{id}', 'invoice')->name('invoice');
        Route::get('catalogue-product', 'catalogueProduct')->name('catalogueProduct');
        Route::get('payment-method-form', 'paymentMethodForm')->name('paymentMethodForm');
    });
    Route::resource('sales-order', SalesOrderController::class)->parameters(['sales-order' => 'id']);

    /**
     * Route Product Module
     */
    Route::group(['controller' => ProductController::class, 'prefix' => 'product', 'as' => 'product.'], function () {
        Route::get('datatable', 'dataTable')->name('dataTable');
        Route::post('get-product', 'getProduct')->name('getProduct');
    });
    Route::resource('product', ProductController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']])->parameters(['product' => 'id']);
});
