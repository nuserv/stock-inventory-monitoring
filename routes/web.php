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
Route::get('/forget-password', 'ForgotPasswordController@getEmail');
Route::post('/forget-password', 'ForgotPasswordController@postEmail');
Route::get('/reset-password/{token}', 'ResetPasswordController@getPassword');
Route::post('/reset-password', 'ResetPasswordController@updatePassword');

Route::get('change-password', 'ChangePasswordController@index');
Route::get('confirm', 'ChangePasswordController@confirm');
Route::post('change-password', 'ChangePasswordController@store')->name('change.password');
Route::get('report-a-problem', 'HomeController@report');
Route::any('respond', 'HomeController@responder');
Route::post('report-a-problem', 'HomeController@reportAproblem')->name('report.problem');



Route::get('/', 'HomeController@index')->name('home.index');
//Route::get('/home', 'HomeController@index')->name('home.indexs');
Route::get('/log', 'HomeController@log')->middleware('ajax');
Route::get('/unrepair', 'HomeController@unrepair')->name('index.unrepair');
Route::get('/disposed', 'HomeController@index')->name('disposed');
Route::get('/dispose', 'DefectiveController@disposed')->name('dispose');//->middleware('ajax');
Route::get('/sdispose', 'DefectiveController@sdisposed')->name('sdispose');//->middleware('ajax');
Route::get('/unrepairable', 'DefectiveController@unrepairable');//->middleware('ajax');



//Route::get('service_center', 'BranchController@index');unrepairable
//Route::get('service_units', 'HomeController@service_units');
//Route::get('spare_parts', 'HomeController@spare_parts');
Route::get('activity', 'HomeController@activity')->middleware('ajax');


Route::get('customerbranch-list/{id}', 'CustomerController@customerbranchtable')->middleware('ajax');
Route::get('customer-list', 'CustomerController@customertable')->middleware('ajax');
Route::get('customer/{id}', 'CustomerController@branchindex')->name('customerbranch.index');
Route::get('customer', 'CustomerController@index')->name('customer.index');
Route::post('customer_add', 'CustomerController@store')->middleware('ajax');
Route::put('customer_add', 'CustomerController@update')->middleware('ajax');
Route::post('cbranch_add', 'CustomerController@branchadd')->middleware('ajax');
Route::put('cbranch_update', 'CustomerController@branchupdate')->middleware('ajax');



Route::put('return-update', 'DefectiveController@update')->middleware('ajax');
Route::get('return-table', 'DefectiveController@table');//->middleware('ajax');
Route::get('printtable', 'DefectiveController@printtable')->middleware('ajax');
Route::get('return', 'DefectiveController@index')->name('return.index');

Route::put('loandelete', 'LoanController@destroy')->middleware('ajax');
Route::put('loanupdate', 'LoanController@stockUpdate')->middleware('ajax');
Route::get('loanget', 'LoanController@getitem')->middleware('ajax');
Route::put('loanstock', 'LoanController@stock')->middleware('ajax');
Route::get('loanitemcode', 'LoanController@getItemCode')->middleware('ajax');
Route::put('loansapproved', 'LoanController@update')->middleware('ajax');
//Route::get('loanrequesttable', 'LoanController@tablerequest')->name('loansrequest.table');
Route::get('loanstable', 'LoanController@table');//->middleware('ajax');
Route::get('loans', 'LoanController@index')->name('loans');
Route::post('loan', 'StockController@loan')->middleware('ajax');

Route::any('rep-update', 'StockController@update')->middleware('ajax');
Route::get('searchall', 'StockController@searchall');//->middleware('ajax');
Route::get('searchserial', 'StockController@searchserial');//->middleware('ajax');
Route::get('pull-details1/{id}', 'StockController@pulldetails1')->middleware('ajax');
Route::get('pull-details/{id}', 'StockController@pulldetails')->middleware('ajax');
Route::any('service-in', 'StockController@servicein')->middleware('ajax');
Route::get('serial', 'StockController@serial')->middleware('ajax');
Route::get('description', 'StockController@description')->middleware('ajax');
Route::get('category', 'StockController@category')->middleware('ajax');
Route::get('bcategory', 'StockController@bcategory')->middleware('ajax');
Route::get('bitem', 'StockController@bitem')->middleware('ajax');
Route::get('bserial/{id}', 'StockController@bserial')->middleware('ajax');
Route::get('service-unit', 'StockController@service')->name('index.service-unit');
Route::get('sUnit', 'StockController@serviceUnit');//->middleware('ajax');
Route::get('pmsUnit', 'StockController@pmserviceUnit');//->middleware('ajax');
Route::get('pclient-autocomplete', 'StockController@pautocompleteClient')->middleware('ajax');
//Route::get('pmclient-autocomplete', 'StockController@pmautocompleteClient')->middleware('ajax');
Route::get('pmcustomer-autocomplete', 'StockController@pmautocompleteCustomer');//->middleware('ajax');
Route::get('pcustomer-autocomplete', 'StockController@pautocompleteCustomer')->middleware('ajax');
Route::get('client-autocomplete', 'StockController@autocompleteClient')->middleware('ajax');
Route::get('customer-autocomplete', 'StockController@autocompleteCustomer')->middleware('ajax');
Route::put('service-out', 'StockController@serviceOut')->middleware('ajax');
Route::put('pm-out', 'StockController@pmOut')->middleware('ajax');
Route::post('pull-out', 'StockController@pullOut')->middleware('ajax');
Route::post('upload', 'StockController@import')->name('stocks.upload');
Route::post('additem', 'StockController@addItem')->middleware('ajax');
Route::post('addcategory', 'StockController@addCategory')->middleware('ajax');
Route::post('store', 'StockController@store');//->middleware('ajax');
Route::get('viewStock', 'StockController@viewStocks');//->middleware('ajax');
Route::get('checkStock', 'StockController@checkStocks');//->middleware('ajax');
Route::get('checkService', 'StockController@checkService');//->middleware('ajax');
Route::get('show', 'StockController@show');//->middleware('ajax');
Route::get('stocks', 'StockController@index')->name('stocks.index');
Route::get('uom', 'StockController@uom')->middleware('ajax');
Route::any('def', 'StockController@def')->middleware('ajax');
Route::any('repaired', 'StockController@repaired')->middleware('ajax');

Route::post('/branch/import', 'ImportController@branchstore');
Route::post('/warehouse/import', 'ImportController@warestore');


Route::get('resolved', 'StockRequestController@resolve')->name('resolved.index');
Route::POST('storerreceived', 'StockRequestController@received')->middleware('ajax');
Route::get('gen', 'StockRequestController@generateRandomNumber')->middleware('ajax');
Route::get('getcatreq', 'StockRequestController@getCatReq')->middleware('ajax');
Route::get('prepitem', 'StockRequestController@prepitem')->middleware('ajax');
Route::put('update', 'StockRequestController@update')->middleware('ajax');
Route::put('intransit', 'StockRequestController@intransit')->middleware('ajax');
Route::post('storerequest', 'StockRequestController@store')->middleware('ajax');
Route::delete('remove', 'StockRequestController@dest')->middleware('ajax');
Route::get('getstock', 'StockRequestController@getStock')->middleware('ajax');
Route::get('getserials', 'StockRequestController@getSerials')->middleware('ajax');
Route::get('itemcode', 'StockRequestController@getItemCode')->middleware('ajax');
Route::get('getcode', 'StockRequestController@getCode');//->middleware('ajax');
Route::get('servicerequest', 'StockRequestController@servicerequest');//->middleware('ajax');
Route::get('getuomq', 'StockRequestController@getuomq');//->middleware('ajax');
Route::get('getcon', 'StockRequestController@getcon');//->middleware('ajax');
//Route::get('read/{id}', 'StockRequestController@read')->name('stock.read');
Route::delete('delete/{id}', 'StockRequestController@destroy')->middleware('ajax');
Route::get('send/{id}', 'StockRequestController@getsendDetails');//->middleware('ajax');
Route::get('intransit/{id}', 'StockRequestController@getintransitDetails');//->middleware('ajax');
Route::get('requests/{id}', 'StockRequestController@getRequestDetails');//->middleware('ajax');
Route::get('getrequests', 'StockRequestController@getReqDetails')->middleware('ajax');
Route::get('prep/{id}', 'StockRequestController@prepitemdetails')->middleware('ajax');
Route::get('requests', 'StockRequestController@getRequests');//->middleware('ajax');
Route::get('res', 'StockRequestController@getResolved');//->middleware('ajax');
Route::get('pcount', 'StockRequestController@pcount')->middleware('ajax');
Route::get('request', 'StockRequestController@index')->name('stock.index');
//Route::get('view', 'StockRequestController@view')->name('stock.view');
Route::put('update/{id}', 'StockRequestController@updateRequestDetails')->middleware('ajax');
Route::put('notrec', 'StockRequestController@notreceived')->middleware('ajax');
Route::put('resolved', 'StockRequestController@resolved')->middleware('ajax');
Route::put('update_serial', 'StockRequestController@upserial')->middleware('ajax');
Route::get('mytest', 'StockRequestController@test');//->middleware('ajax');

Route::get('users', 'UserController@getUsers');//->middleware('ajax');
Route::get('user', 'UserController@index')->name('user.index');
Route::get('getBranchName', 'UserController@getBranchName')->middleware('ajax');
Route::post('user_add', 'UserController@store')->middleware('ajax');
Route::put('user_update/{id}', 'UserController@update')->middleware('ajax');

Route::get('stocks/{id}', 'BranchController@getStocks');//->middleware('ajax');
Route::get('branches', 'BranchController@getBranches')->middleware('ajax');
Route::get('branch', 'BranchController@index')->name('branch.index');
Route::post('branch_add', 'BranchController@store')->middleware('ajax');
Route::put('branch_ini', 'BranchController@initial')->middleware('ajax');
Route::put('branch_update/{id}', 'BranchController@update')->middleware('ajax');

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::get('print/{id}', 'HomeController@print')->name('branch.print.index');
Route::get('getprint/{id}', 'HomeController@getprint')->middleware('ajax');
Route::get('initial/{id}', 'HomeController@initial');//->middleware('ajax');
Route::get('defective/print', 'HomeController@printDefective')->name('defective.print.index');

Route::get('preventive', 'HomeController@preventive')->name('index.preventive');
Route::get('convert', 'HomeController@convert');
Route::get('imp', 'HomeController@imp');