<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomersReportController;
use App\Http\Controllers\Api\InvoiceReportController;
use App\Http\Controllers\Api\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\InvoiceAttachmentController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\InvoiceDetailsController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::post('/reset-password', [AuthController::class, 'reset_password']);




});
Route::post('forgot-password', [AuthController::class, 'forgot_password']);


Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('users', [UserController::class, 'index'])->name('user.users');
    Route::get('users_show/{id}', [UserController::class, 'show'])->name('user.show');
    Route::post('users/store', [UserController::class, 'store'])->name('user.store');
    Route::post('users/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::post('users/update_status/{id}', [UserController::class, 'update_status'])->name('user.update_status');
    Route::delete('users/delete/{id}', [UserController::class, 'delete'])->name('user.delete');
});



Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('invoices/{skip}/{limit}', [InvoiceController::class, 'index'])->name('invoice.invoices');
    Route::get('invoices_unPaid/{skip}/{limit}', [InvoiceController::class, 'Invoice_unPaid'])->name('invoice.Invoice_unPaid');
    Route::get('invoices_Partial/{skip}/{limit}', [InvoiceController::class, 'Invoice_Partial'])->name('invoice.Invoice_Partial');
    Route::get('invoices_paid/{skip}/{limit}', [InvoiceController::class, 'Invoice_Paid'])->name('invoice.Invoice_Paid');
    Route::get('invoices_getdestroyinvoice', [InvoiceController::class, 'GetDestroyInvoice'])->name('invoice.GetDestroyInvoice');
    Route::get('invoices_restoredestroyinvoice/{id}', [InvoiceController::class, 'RestoreDestroyInvoice'])->name('invoice.RestoreDestroyInvoice');
    Route::get('invoices_getinvoicedetails/{id}', [InvoiceController::class, 'GetInvoice_details'])->name('invoice.GetInvoice_details');
    Route::get('invoices_getcategory/{id}', [InvoiceController::class, 'GetCategory'])->name('invoice.GetCategory');
    Route::get('invoices_getproduct/{id}', [InvoiceController::class, 'GetProduct'])->name('invoice.GetProduct');
    Route::get('invoices_getuser/{id}', [InvoiceController::class, 'GetUser'])->name('invoice.GetUser');
    Route::get('invoices_status_update/{id}', [InvoiceController::class, 'status_update'])->name('invoice.status_update');
    Route::get('invoices_getattachments/{id}', [InvoiceController::class, 'GetAttachments'])->name('invoice.GetAttachments');
    Route::get('invoices_show/{id}', [InvoiceController::class, 'show'])->name('invoice.show');
    Route::get('invoices/create', [InvoiceController::class, 'create'])->name('invoice.create');;
    Route::post('invoices/store', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::get('invoices/edit/{id}', [InvoiceController::class, 'edit'])->name('invoice.edit');
    Route::post('invoices/update/{id}', [InvoiceController::class, 'update'])->name('invoice.update');
    Route::delete('invoices/delete/{id}', [InvoiceController::class, 'delete'])->name('invoice.delete');
    Route::delete('invoices/deleteAll', [InvoiceController::class, 'deleteAll'])->name('invoice.deleteAll');
    Route::delete('invoices/destroy/{id}', [InvoiceController::class, 'destroy'])->name('invoice.destroy');
    Route::delete('invoices/destroyAll', [InvoiceController::class, 'destroyAll'])->name('invoice.destroyAll');
    Route::get('invoices/truncate', [InvoiceController::class, 'truncate'])->name('invoice.truncate.all');
    Route::get('invoices/MarkAsRead_all', [InvoiceController::class, 'MarkAsRead_all']);
    Route::get('invoices/unreadNotifications_count', [InvoiceController::class, 'unreadNotifications_count']);
    Route::get('invoices/unreadNotifications', [InvoiceController::class, 'unreadNotifications']);
    Route::get('invoices/export/', [InvoiceController::class, 'export']);
    Route::get('invoices/Search_invoices', [InvoiceReportController::class, 'Search_invoices']);


});


Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('products/{skip}/{limit}', [ProductController::class, 'index'])->name('product.products');
    Route::get('products_getcategory/{id}', [ProductController::class, 'GetCategory'])->name('product.GetCategory');
    Route::get('products_getinvoices/{id}', [ProductController::class, 'GetInvoices'])->name('category.GetInvoices');
    Route::get('products_show/{id}', [ProductController::class, 'show'])->name('product.show');
    Route::get('oneproducts/show/{id}', [ProductController::class, 'GetOneProdect'])->name('product.GetOneProdect');
    Route::get('products/create', [ProductController::class, 'create'])->name('product.create');;
    Route::post('products/store', [ProductController::class, 'store'])->name('product.store');
    Route::get('products/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
    Route::post('products/update/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::delete('products/delete/{id}', [ProductController::class, 'delete'])->name('product.delete');
    Route::delete('products/deleteAll', [ProductController::class, 'deleteAll'])->name('product.deleteAll');
    Route::get('products/truncate', [ProductController::class, 'truncate'])->name('product.truncate.all');
});


Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('invoices_Attachments/{skip}/{limit}', [InvoiceAttachmentController::class, 'index'])->name('invoices_Attachment.invoices_Attachments');
    Route::get('invoices_Attachments_getdestroyinvoicesattachments', [InvoiceAttachmentController::class, 'GetDestroyInvoiceAttachment'])->name('invoice.GetDestroyInvoiceAttachment');
    Route::get('invoices_Attachments_restoredestroyinvoicesattachments/{id}', [InvoiceAttachmentController::class, 'RestoreDestroyInvoiceAttachment'])->name('invoice.RestoreDestroyInvoiceAttachment');
    Route::get('invoices_Attachments_getinvoice/{id}', [InvoiceAttachmentController::class, 'GetInvoice'])->name('invoices_Attachment.GetInvoice');
    Route::get('invoices_Attachments_show/{id}', [InvoiceAttachmentController::class, 'show'])->name('invoices_Attachment.show');
    Route::get('invoices_Attachments/create', [InvoiceAttachmentController::class, 'create'])->name('invoices_Attachment.create');;
    Route::post('invoices_Attachments/store', [InvoiceAttachmentController::class, 'store'])->name('invoices_Attachment.store');
    Route::get('invoices_Attachments/edit/{id}', [InvoiceAttachmentController::class, 'edit'])->name('invoices_Attachment.edit');
    Route::post('invoices_Attachments/update/{id}', [InvoiceAttachmentController::class, 'update'])->name('invoices_Attachment.update');
    Route::delete('invoices_Attachments/delete/{id}', [InvoiceAttachmentController::class, 'delete'])->name('invoices_Attachment.delete');
    Route::delete('invoices_Attachments/deleteAll', [InvoiceAttachmentController::class, 'deleteAll'])->name('invoices_Attachment.deleteAll');
    Route::delete('invoices_Attachments/destroy/{id}', [InvoiceAttachmentController::class, 'destroy'])->name('invoices_Attachment.destroy');
    Route::delete('invoices_Attachments/destroyAll', [InvoiceAttachmentController::class, 'destroyAll'])->name('invoices_Attachment.destroyAll');
    Route::get('invoices_Attachments/truncate', [InvoiceAttachmentController::class, 'truncate'])->name('invoices_Attachment.truncate.all');
    Route::get('invoices_Attachments_download/{id}', [InvoiceAttachmentController::class, 'download'])->name('invoices_Attachment.download');;

});
Route::post('invoice-paid', [InvoiceController::class, 'sendInvoicePaidNotification'])
    ->name('notify.invoice.paid');


Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('categories/{skip}/{limit}', [CategoryController::class, 'index'])->name('category.categories');
    Route::get('categories_show/{id}', [CategoryController::class, 'show'])->name('category.show');
    Route::get('categories_getinvoices/{id}', [CategoryController::class, 'GetInvoices'])->name('category.GetInvoices');
    Route::get('categories_getproducts/{id}', [CategoryController::class, 'GetProducts'])->name('category.GetProdects');
    Route::get('categories_getusers/{id}', [CategoryController::class, 'GetUser'])->name('category.GetUser');
    Route::get('categories/create', [CategoryController::class, 'create'])->name('category.create');;
    Route::post('categories/store', [CategoryController::class, 'store'])->name('category.store');
    Route::get('categories/edit/{id}', [CategoryController::class, 'edit'])->name('category.edit');
    Route::post('categories/update/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('categories/delete/{id}', [CategoryController::class, 'delete'])->name('category.delete');
    Route::delete('categories/deleteAll', [CategoryController::class, 'deleteAll'])->name('category.deleteAll');
    Route::get('categories/truncate', [CategoryController::class, 'truncate'])->name('category.truncate');
});



Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('roles', [RoleController::class, 'index'])->name('role.roles');
    Route::get('roles_show/{id}', [RoleController::class, 'show'])->name('role.show');
    Route::post('roles/store', [RoleController::class, 'store'])->name('role.store');
    Route::post('roles/update/{id}', [RoleController::class, 'update'])->name('role.update');
    Route::delete('roles/delete/{id}', [RoleController::class, 'delete'])->name('role.delete');
    Route::get('roles/truncate', [RoleController::class, 'truncate'])->name('role.truncate');
});

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('category', [CustomersReportController::class, 'index'])->name('role.roles');
    Route::get('customers/Search_customers', [CustomersReportController::class, 'Search_customers'])->name('role.Search_customers');

});



Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('invoices_Details/{skip}/{limit}', [InvoiceDetailsController::class, 'index'])->name('invoice_Details.invoices_Details');

});



















