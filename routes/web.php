<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

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

Route::get('/', function () {
    return view('welcome');
});
Route::get('invoices', [InvoiceController::class, 'index'])->name('invoice.invoices');
Route::delete('invoices/destroy/{id}', [InvoiceController::class, 'destroy'])->name('invoice.destroy');


Route::get('invoices/export/', [InvoiceController::class, 'export'])->name('invoice.export');
Route::get('invoices', [InvoiceController::class, 'index']);
