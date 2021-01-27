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

Auth::routes();

Route::get('change-password', 'ChangePasswordController@index');
Route::post('change-password', 'ChangePasswordController@store')->name('change.password');


Route::get('/', 'HomeController@index')->name('home.index');
//Route::get('/home', 'HomeController@index')->name('home.indexs');
Route::get('/log', 'HomeController@log')->name('home.log');
Route::get('/unrepair', 'HomeController@unrepair')->name('home.unrepair');
Route::get('/unrepairable', 'DefectiveController@unrepairable')->name('home.unrepairable');



//Route::get('service_center', 'BranchController@index');
Route::get('service_units', 'HomeController@service_units');
Route::get('spare_parts', 'HomeController@spare_parts');
Route::get('activity', 'HomeController@activity')->name('get.activity');


Route::get('customerbranch-list/{id}', 'CustomerController@customerbranchtable');
Route::get('customer-list', 'CustomerController@customertable')->name('customer.list');
Route::get('customer/{id}', 'CustomerController@branchindex')->name('customerbranch.index');
Route::get('customer', 'CustomerController@index')->name('customer.index');
Route::post('customer_add', 'CustomerController@store')->name('customer.store');
Route::put('customer_add', 'CustomerController@update')->name('customer.update');
Route::post('cbranch_add', 'CustomerController@branchadd');
Route::put('cbranch_update', 'CustomerController@branchupdate');



Route::put('return-update', 'DefectiveController@update')->name('return.update');
Route::get('return-table', 'DefectiveController@table')->name('return.table');
Route::get('printtable', 'DefectiveController@printtable')->name('return.print');
Route::get('return', 'DefectiveController@index')->name('return.index');

Route::put('loandelete', 'LoanController@destroy')->name('loans.stock.delete');
Route::put('loanupdate', 'LoanController@stockUpdate')->name('loans.stock.update');
Route::get('loanget', 'LoanController@getitem')->name('loans.getitem');
Route::put('loanstock', 'LoanController@stock')->name('loans.stock');
Route::get('loanitemcode', 'LoanController@getItemCode')->name('loan.get.itemcode');
Route::put('loansapproved', 'LoanController@update')->name('loans.approved');
Route::get('loanrequesttable', 'LoanController@tablerequest')->name('loansrequest.table');
Route::get('loanstable', 'LoanController@table')->name('loans.table');
Route::get('loans', 'LoanController@index')->name('loans');
Route::post('loan', 'StockController@loan')->name('stocks.loan');

Route::put('rep-update', 'StockController@update')->name('stocks.update');
Route::get('pull-details1/{id}', 'StockController@pulldetails1')->name('stocks.details1.pullout');
Route::get('pull-details/{id}', 'StockController@pulldetails')->name('stocks.details.pullout');
Route::put('service-in', 'StockController@servicein')->name('stock.service-in');
Route::get('serial', 'StockController@serial')->name('stock.serial');
Route::get('description', 'StockController@description')->name('stock.description');
Route::get('category', 'StockController@category')->name('stock.category');
Route::get('bcategory', 'StockController@bcategory')->name('stock.bcategory');
Route::get('bitem', 'StockController@bitem')->name('stock.bitem');
Route::get('bserial/{id}', 'StockController@bserial')->name('stock.bserial');
Route::get('service-unit', 'StockController@service')->name('stock.service-unit');
Route::get('sUnit', 'StockController@serviceUnit')->name('stock.sUnit');
Route::get('pclient-autocomplete', 'StockController@pautocompleteClient')->name('pclient.autocomplete');
Route::get('pcustomer-autocomplete', 'StockController@pautocompleteCustomer')->name('pcustomer.autocomplete');
Route::get('client-autocomplete', 'StockController@autocompleteClient')->name('client.autocomplete');
Route::get('customer-autocomplete', 'StockController@autocompleteCustomer')->name('customer.autocomplete');
Route::put('service-out', 'StockController@serviceOut')->name('stocks.out');
Route::post('pull-out', 'StockController@pullOut')->name('stocks.pullout');
Route::post('upload', 'StockController@import')->name('stocks.upload');
Route::post('additem', 'StockController@addItem')->name('add.item');
Route::post('addcategory', 'StockController@addCategory')->name('add.category');
Route::post('store', 'StockController@store')->name('stocks.store');
Route::get('viewStock', 'StockController@viewStocks')->name('stocks.view');
Route::get('checkStock', 'StockController@checkStocks')->name('stocks.check');
Route::get('show', 'StockController@show')->name('stocks.show');
Route::get('stocks', 'StockController@index')->name('stocks.index');
Route::delete('def', 'StockController@def')->name('stock.def');


Route::POST('storerreceived', 'StockRequestController@received')->name('stock.received.request');
Route::get('gen', 'StockRequestController@generateBarcodeNumber')->name('stock.gen');
Route::get('getcatreq', 'StockRequestController@getCatReq')->name('stock.getcatreq');
Route::get('prepitem', 'StockRequestController@prepitem')->name('stock.prepitem');
Route::put('update', 'StockRequestController@update')->name('stock.update');
Route::post('storerequest', 'StockRequestController@store')->name('stock.store.request');
Route::delete('remove', 'StockRequestController@dest')->name('stock.remove');
Route::get('getstock', 'StockRequestController@getStock')->name('stock.get');
Route::get('getserials', 'StockRequestController@getSerials')->name('stock.serials');
Route::get('itemcode', 'StockRequestController@getItemCode')->name('stock.get.itemcode');
Route::get('getcode', 'StockRequestController@getCode')->name('stock.get.Code');
Route::get('read/{id}', 'StockRequestController@read')->name('stock.read');
Route::delete('delete/{id}', 'StockRequestController@destroy')->name('stock.delete');
Route::get('send/{id}', 'StockRequestController@getsendDetails')->name('stock.send');
Route::get('requests/{id}', 'StockRequestController@getRequestDetails')->name('get.reqdetails');
Route::get('getrequests', 'StockRequestController@getReqDetails')->name('get.req');
Route::get('prep/{id}', 'StockRequestController@prepitemdetails')->name('get.prepdetails');
Route::get('requests', 'StockRequestController@getRequests')->name('get.requests');
Route::get('pcount', 'StockRequestController@pcount')->name('get.pcount');
Route::get('request', 'StockRequestController@index')->name('stock.index');
Route::get('view', 'StockRequestController@view')->name('stock.view');
Route::put('update/{id}', 'StockRequestController@updateRequestDetails')->name('update.reqdetails');


Route::get('users', 'UserController@getUsers')->name('get.users');
Route::get('user', 'UserController@index')->name('user.index');
Route::get('getBranchName', 'UserController@getBranchName')->name('user.getBranch');
Route::post('user_add', 'UserController@store')->name('user.add');
Route::put('user_update/{id}', 'UserController@update')->name('user.update');

Route::get('stocks/{id}', 'BranchController@getStocks')->name('get.stocks');
Route::get('branches', 'BranchController@getBranches')->name('get.branches');
Route::get('branch', 'BranchController@index')->name('branch.index');
Route::post('branch_add', 'BranchController@store')->name('branch.add');
Route::put('branch_ini', 'BranchController@initial')->name('branch.ini');
Route::put('branch_update/{id}', 'BranchController@update')->name('branch.update');

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::get('print/{id}', 'HomeController@print')->name('branch.print');
Route::get('getprint/{id}', 'HomeController@getprint')->name('getprint');
Route::get('initial/{id}', 'HomeController@initial');
Route::get('defective/print', 'HomeController@printDefective')->name('defective.print');


Route::get('convert', 'HomeController@convert');