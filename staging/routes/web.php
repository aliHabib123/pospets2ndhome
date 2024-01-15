<?php
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/', 'HomeController@index');

Route::get('home', 'HomeController@index');

Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('cache:clear');
    // return what you want
});

// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Password reset link request routes...
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');

// Change Password Routes...
$this->get('change_password', 'Auth\ChangePasswordController@showChangePasswordForm')->name('auth.change_password');
$this->patch('change_password', 'Auth\ChangePasswordController@changePassword')->name('auth.change_password');

//    // Password Reset Routes...
//    $this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('auth.password.reset');
//    $this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('auth.password.reset');
//    $this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
//    $this->post('password/reset', 'Auth\ResetPasswordController@reset')->name('auth.password.reset');
//
Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/home', 'HomeController@index');
    Route::resource('permissions', 'Admin\PermissionsController');
    Route::post('permissions_mass_destroy', ['uses' => 'Admin\PermissionsController@massDestroy', 'as' => 'permissions.mass_destroy']);
    Route::resource('roles', 'Admin\RolesController');
    Route::post('roles_mass_destroy', ['uses' => 'Admin\RolesController@massDestroy', 'as' => 'roles.mass_destroy']);
    Route::resource('users', 'Admin\UsersController');
    Route::post('users_mass_destroy', ['uses' => 'Admin\UsersController@massDestroy', 'as' => 'users.mass_destroy']);
});


Route::resource('api/items', 'SaleApiController');
Route::resource('reports/transfer', 'TransferReportController');
Route::post('customers/search', 'CustomerController@search');
Route::resource('customers', 'CustomerController');
Route::resource('items', 'ItemController');
Route::get('barcodes', 'ItemController@barcodes');
Route::get('barcode-search', 'ItemController@barcodesSearch');
Route::get('barcode-view/{id}', 'ItemController@barcodeView');
Route::get('items-search', 'ItemController@search');
Route::resource('categories', 'CategoryController');
Route::get('locations/choose', 'LocationController@choose');
Route::get('/locations/saveLocation/{id}', ['as' => 'locations.saveLocation.update', 'uses' => 'LocationController@saveLocation']);
Route::resource('locations', 'LocationController');
Route::post('expenses/search', 'ExpenseController@search');
Route::resource('expenses', 'ExpenseController');
Route::resource('item-kits', 'ItemKitController');
Route::resource('inventory', 'InventoryController');
Route::get('inventory/view-item-movement/{id}', 'InventoryController@viewInventory');
Route::post('suppliers/search', 'SupplierController@search');
Route::resource('suppliers', 'SupplierController');
Route::resource('receivings', 'ReceivingController');
Route::resource('receiving-item', 'ReceivingItemController');
Route::resource('sales', 'SaleController');
Route::resource('transfer', 'TransferController');
Route::resource('employees', 'EmployeeController');
Route::post('store-item-kits', 'ItemKitController@storeItemKits');
Route::resource('settings', 'SettingController');

Route::resource('api/saleitems', 'SaleApiController');
Route::get('api/getDollarRate', 'SaleApiController@getRate');
Route::resource('api/transferitems', 'TransferApiController');
Route::resource('api/item', 'ReceivingApiController');
Route::resource('api/receivingtemp', 'ReceivingTempApiController');
Route::resource('api/transfertemp', 'TransferTempApiController');

Route::resource('api/saletemp', 'SaleTempApiController');

Route::resource('api/itemkittemp', 'ItemKitController');
Route::get('api/item-kit-temp', 'ItemKitController@itemKitApi');
Route::get('api/item-kits', 'ItemKitController@itemKits');


Route::get('generalReports/transfer', 'GeneralController@transfer');
Route::get('generalReports/printtransfer/{id}', 'GeneralController@printTransfer');
Route::get('generalReports/transfersearch', 'GeneralController@transfersearch');
Route::get('generalReports/sales', 'GeneralController@sales');
Route::get('generalReports/salessearch', 'GeneralController@salessearch');
Route::get('generalReports/receivings', 'GeneralController@receivings');
Route::get('generalReports/printreceiving/{id}', 'GeneralController@printReceiving');
Route::get('generalReports/receivingssearch', 'GeneralController@receivingssearch');
Route::get('generalReports/closeout', 'GeneralController@closeout');
Route::get('generalReports/closeoutApi', 'GeneralController@closeoutApi');
Route::get('generalReports/itemReport', 'GeneralController@itemReport');
Route::get('generalReports/itemReportApi', 'GeneralController@itemReportApi');
Route::get('generalReports/categoriesProfit', 'GeneralController@categoriesProfit');
Route::resource('refund', 'RefundController');

//refund ajax routes
Route::post('update-invoice', 'RefundController@updateInvoice');
Route::get('getReceivingItems/{id}', 'RefundController@getReceivingItems');
Route::get('generalReports/refundSale/{id}', 'GeneralController@refundSale');
Route::get('generalReports/refundwholesale/{id}', 'GeneralController@refundwholesale');
Route::get('getSaleItems/{id}', 'RefundController@getSaleItems');
Route::get('getWholesaleItems/{id}', 'RefundController@getWholesaleItems');
Route::get('getSaleInvoice/{id}', 'RefundController@getSaleInvoice');
Route::get('getCustomerInvoive/{id}', 'GeneralController@getWholeSaleInvoiceCustomerPayment');
//Route::get('getInvoiceInfo/{id}', 'RefundController@getInvoiceInfo');
Route::get('getWholesaleInvoice/{id}', 'RefundController@getWholesaleInvoice');
Route::post('update-sale-invoice', 'RefundController@updateSaleInvoice');
Route::post('update-wholesale-invoice', 'RefundController@updateWholesaleInvoice');
Route::resource('wholesales', 'WholeSaleController');
Route::resource('api/wholesaleitems', 'WholeSaleApiController');
Route::resource('api/wholesaletemp', 'WholeSaleTempApiController');
Route::get('generalReports/wholesalessearch', 'GeneralController@wholesalessearch');

Route::get('generalReports/wholesales', 'GeneralController@wholesales');
Route::get('generalReports/closeout2', 'GeneralController@closeout2');
Route::get('generalReports/printWholesale/{id}', 'GeneralController@printWholesale');

//the 2 below routes are for inventory printing
Route::get('generalReports/inventoryLocations', 'GeneralController@inventoryLocations');
Route::post('generalReports/inventoryItems', 'GeneralController@inventoryItems');
//Route::get('itemsss/custom-search', 'GeneralController@customSearch');
