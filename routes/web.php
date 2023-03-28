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

Auth::routes(['verify' => true]);
Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');
Route::get('/user/verify/{token}', 'Auth\LoginController@verifyUser');
Route::get('/send/verification', 'UserController@resend')->middleware(['ajax']);
Route::get('barchart', 'ReportController@chart');


Route::get('schedule', 'PreventiveController@index')->name('index.schedule');
Route::post('schedule', 'PreventiveController@Store');
Route::get('scheduled', 'PreventiveController@show');
Route::get('pmlist', 'PreventiveController@list');
Route::get('pmlistdata', 'PreventiveController@data');
Route::get('export', 'PreventiveController@ExportData');
Route::get('report', 'PreventiveController@ReportData');
Route::get('getpm', 'PreventiveController@getpm');
Route::get('genpm', 'PreventiveController@genpm');
Route::get('getbranch', 'PreventiveController@getbranch');
Route::get('checkfsr', 'PreventiveController@checkfsr');



Route::get('login', 'Auth\LoginController@showLoginForm')->name('login')->middleware('checkBrowser');

Route::get('/forget-password', 'ForgotPasswordController@getEmail');
Route::post('/forget-password', 'ForgotPasswordController@postEmail');
Route::get('/reset-password/{token}', 'ResetPasswordController@getPassword');
Route::post('/reset-password', 'ResetPasswordController@updatePassword');

Route::get('change-password', 'ChangePasswordController@index')->name('password.change');
Route::get('confirm', 'ChangePasswordController@confirm');
Route::post('change-password', 'ChangePasswordController@store')->name('change.password');
Route::get('report-a-problem', 'HomeController@report');
Route::any('respond', 'HomeController@responder');
Route::post('report-a-problem', 'HomeController@reportAproblem')->name('report.problem');


Route::get('item', 'HomeController@item')->name('home.item');
Route::get('items', 'HomeController@items')->name('home.items');
Route::put('items-edit', 'HomeController@itemsedit')->name('home.items.edit')->middleware('ajax');
Route::any('item-update', 'HomeController@itemsUpdate')->name('Update.items')->middleware('ajax');


Route::get('/', 'HomeController@index')->name('home.index');
Route::get('/pending', 'HomeController@pending')->name('home.request');
//Route::get('/home', 'HomeController@index')->name('home.indexs');
Route::get('/log', 'HomeController@log');
Route::get('/unrepair', 'HomeController@unrepair')->name('index.unrepair');
Route::get('/disposed', 'HomeController@index')->name('disposed');
Route::get('/dispose', 'DefectiveController@disposed')->name('dispose');//->middleware('ajax');
Route::get('/sdispose', 'DefectiveController@sdisposed')->name('sdispose');//->middleware('ajax');
Route::get('/unrepairable', 'DefectiveController@unrepairable');//->middleware('ajax');



//Route::get('service_center', 'BranchController@index');unrepairable
//Route::get('service_units', 'HomeController@service_units');
//Route::get('spare_parts', 'HomeController@spare_parts');service-out
Route::get('activity', 'HomeController@activity');//->middleware('ajax');


Route::get('customerbranch-list/{id}', 'CustomerController@customerbranchtable');//->middleware('ajax');
Route::get('customerbranch', 'CustomerController@branchtable');//->middleware('ajax');
Route::get('getcustomerid', 'CustomerController@getid');//->middleware('ajax');
Route::get('hint', 'CustomerController@hint');//->middleware('ajax');
Route::get('pulloutclient', 'CustomerController@pulloutclient');//->middleware('ajax');
Route::get('getclient', 'CustomerController@getclient');//->middleware('ajax');getPm-client
Route::get('getPm-client', 'CustomerController@getPmclient');//->middleware('ajax');
Route::get('checkPm-client', 'CustomerController@checkPmclient');//->middleware('ajax');
Route::get('customer-list', 'CustomerController@customertable')->middleware('ajax');
Route::get('customer/{id}', 'CustomerController@branchindex')->name('customerbranch.index');
Route::get('customer', 'CustomerController@index')->name('customer.index');
Route::post('customer_add', 'CustomerController@store')->middleware('ajax');
Route::put('customer_add', 'CustomerController@update')->middleware('ajax');
Route::post('cbranch_add', 'CustomerController@branchadd')->middleware('ajax');
Route::put('cbranch_update', 'CustomerController@branchupdate')->middleware('ajax');
Route::get('verifyserial', 'StockController@verifyserial');//->middleware('ajax');




Route::put('conversion', 'DefectiveController@conversion')->middleware('ajax');
Route::any('return-update', 'DefectiveController@update')->middleware('ajax');
Route::get('return-table', 'DefectiveController@table');//->middleware('ajax');
Route::get('convertion-table', 'DefectiveController@convertiontable');//->middleware('ajax');
Route::get('printtable', 'DefectiveController@printtable')->middleware('ajax');
Route::get('retno', 'DefectiveController@returntable');//->middleware('ajax');
Route::get('return', 'DefectiveController@index')->name('return.index');
Route::get('POS', 'DefectiveController@pos')->name('return.pos');
Route::get('postable', 'DefectiveController@postable')->name('return.postable');


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

Route::any('rep-update', 'StockController@update');//->middleware('ajax');
Route::any('PMrep-update', 'StockController@PMupdate')->middleware('ajax');
Route::get('searchall', 'StockController@searchall');//->middleware('ajax');
Route::get('searchserial', 'StockController@searchserial');//->middleware('ajax');
Route::get('pull-details1/{id}', 'StockController@pulldetails1')->middleware('ajax');
Route::get('pull-details/{id}', 'StockController@pulldetails')->middleware('ajax');
Route::any('service-in', 'StockController@servicein')->middleware('ajax');
Route::any('pmservice-in', 'StockController@pmservicein')->middleware('ajax');
Route::get('serial', 'StockController@serial')->middleware('ajax');
Route::get('description', 'StockController@description')->middleware('ajax');
Route::get('category', 'StockController@category')->middleware('ajax');
Route::get('bcategory', 'StockController@bcategory')->middleware('ajax');
Route::get('bitem', 'StockController@bitem')->middleware('ajax');
Route::get('bserial/{id}', 'StockController@bserial')->middleware('ajax');
Route::get('service-unit', 'StockController@service')->name('index.service-unit');
Route::get('sUnit', 'StockController@serviceUnit');//->middleware('ajax');
Route::get('bill', 'StockController@bill');//->middleware('ajax');
Route::any('delbill', 'StockController@delbill');//->middleware('ajax');
Route::put('approvebill', 'StockController@approvebill');//->middleware('ajax');
Route::any('prcbill', 'StockController@prcbill');//->middleware('ajax');

Route::get('pmsUnit', 'StockController@pmserviceUnit');//->middleware('ajax');
Route::get('pclient-autocomplete', 'StockController@pautocompleteClient')->middleware('ajax');
//Route::get('pmclient-autocomplete', 'StockController@pmautocompleteClient')->middleware('ajax');
Route::get('pmcustomer-autocomplete', 'StockController@pmautocompleteCustomer');//->middleware('ajax');
Route::get('pcustomer-autocomplete', 'StockController@pautocompleteCustomer')->middleware('ajax');
Route::get('client-autocomplete', 'StockController@autocompleteClient')->middleware('ajax');
Route::get('customer-autocomplete', 'StockController@autocompleteCustomer')->middleware('ajax');
Route::get('customer-autocomplate', 'StockController@autocomplateCustomer')->middleware('ajax');
Route::put('service-out', 'StockController@serviceOut')->middleware('ajax');
Route::put('pm-out', 'StockController@pmOut')->middleware('ajax');
Route::post('pull-out', 'StockController@pullOut')->middleware('ajax');
Route::post('upload', 'StockController@import')->name('stocks.upload');
Route::any('additem', 'StockController@addItem');//->middleware('ajax');
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
Route::put('pullout', 'StockController@pull')->middleware('ajax');
Route::get('pullview', 'StockController@pullview')->name('pullout.index');
Route::get('pullviewlist', 'StockController@pullviewlist');
Route::get('returnview', 'DefectiveController@returnview')->name('return.view');
Route::get('repaired-ware', 'DefectiveController@repaired')->name('repaired.list');
Route::get('repaired-list', 'DefectiveController@repairedlist')->name('repaired.view');
Route::put('pullnr', 'StockController@pullnr')->middleware('ajax');
Route::put('repairednr', 'DefectiveController@repairednr')->middleware('ajax');
Route::put('pullrec', 'StockController@pullrec')->middleware('ajax');
Route::put('repairedrec', 'DefectiveController@repairedrec')->middleware('ajax');
Route::put('returnrec', 'DefectiveController@returnrec')->middleware('ajax');
Route::get('repairedget', 'DefectiveController@repairedget');//->middleware('ajax');
Route::get('pullget', 'StockController@pullget');//->middleware('ajax');
Route::get('returnget', 'DefectiveController@returnget');//->middleware('ajax');
Route::get('pullitem', 'StockController@pullitem');//->middleware('ajax');
Route::get('repaireditem', 'DefectiveController@repaireditem');//->middleware('ajax');
Route::get('returnitem', 'DefectiveController@returnitem');//->middleware('ajax');
Route::put('pullupdate', 'StockController@pullupdate')->middleware('ajax');
Route::put('repairedupdate', 'DefectiveController@repairedupdate')->middleware('ajax');
// Route::get('export', 'ImportController@export')->name('export');

Route::put('bufferupdate', 'StockRequestController@bufferupdate');//->middleware('ajax');
Route::put('bufferreceived', 'StockRequestController@bufferreceived');//->middleware('ajax');
Route::any('buffreceived', 'StockRequestController@buffreceived');//->middleware('ajax');
Route::delete('bufferdelete', 'StockRequestController@bufferdelete');//->middleware('ajax');
Route::put('buffersend', 'StockRequestController@buffersend');//->middleware('ajax');
Route::get('buffersenditems', 'StockRequestController@buffersenditems');//->middleware('ajax');
Route::put('bufferapproved', 'StockRequestController@bufferapproved')->middleware('ajax');
Route::get('bufferviewlist', 'StockRequestController@bufferviewlist');
Route::get('checkrequest', 'StockRequestController@checkrequest');//->middleware('ajax');
Route::get('checkbuffer', 'StockRequestController@checkbuffer');//->middleware('ajax');
Route::get('bufferitem', 'StockRequestController@bufferitem');//->middleware('ajax');
Route::get('bufferget', 'StockRequestController@bufferget');//->middleware('ajax');
Route::get('bufferlist', 'StockRequestController@bufferlist');//->middleware('ajax');
Route::post('bufferstore', 'StockRequestController@bufferstore');//->middleware('ajax');
Route::get('checkrequestitem', 'StockRequestController@checkrequestitem');//->middleware('ajax');
Route::get('checkrequestitemqty', 'StockRequestController@checkrequestitemqty');//->middleware('ajax');
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
Route::get('getItemCodeServiceOut', 'StockRequestController@getItemCodeServiceOut');//->middleware('ajax');
Route::get('itemcodes', 'StockRequestController@getItemCodes');//->middleware('ajax');
Route::get('getcode', 'StockRequestController@getCode');//->middleware('ajax');
Route::get('checkserial', 'StockRequestController@checkserial');//->middleware('ajax');
Route::get('checkserials', 'StockRequestController@checkserials');//->middleware('ajax');
Route::get('servicerequest', 'StockRequestController@servicerequest');//->middleware('ajax');
Route::get('getuomq', 'StockRequestController@getuomq');//->middleware('ajax');
Route::get('getcon', 'StockRequestController@getcon');//->middleware('ajax');
Route::get('getstockid', 'StockRequestController@getstockid');//->middleware('ajax');
Route::put('updatestat', 'StockRequestController@updatestat');//->middleware('ajax');
//Route::get('read/{id}', 'StockRequestController@read')->name('stock.read');
Route::delete('delete/{id}', 'StockRequestController@destroy')->middleware('ajax');
Route::get('send/{id}', 'StockRequestController@getsendDetails');//->middleware('ajax');
Route::get('intransit/{id}', 'StockRequestController@getintransitDetails');//->middleware('ajax');
Route::get('requests/{id}', 'StockRequestController@getRequestDetails');//->middleware('ajax');
Route::get('getrequests', 'StockRequestController@getReqDetails')->middleware('ajax');
Route::get('prep/{id}', 'StockRequestController@prepitemdetails')->middleware('ajax');
Route::get('requests', 'StockRequestController@getRequests');//->middleware('ajax');
Route::get('requestsdata', 'StockRequestController@requestsdata');//->middleware('ajax');
Route::get('res', 'StockRequestController@getResolved');//->middleware('ajax');
Route::get('pcount', 'StockRequestController@pcount')->middleware('ajax');
Route::get('request', 'StockRequestController@index')->name('stock.index');
Route::get('itemrequest', 'StockRequestController@itemrequest')->name('stock.itemrequest');
Route::get('itemrequestdata', 'StockRequestController@itemrequestdata')->name('stock.itemrequestdata');
Route::get('branchitemdata', 'StockRequestController@branchitemdata')->name('stock.branchitemdata');
Route::get('branchitemdata2', 'StockRequestController@branchitemdata2')->name('stock.branchitemdata2');
Route::get('billable', 'StockRequestController@billable')->name('stock.billable');
Route::get('buffer', 'StockRequestController@buffer')->name('stock.buffer');
//Route::get('view', 'StockRequestController@view')->name('stock.view');
Route::put('update/{id}', 'StockRequestController@updateRequestDetails')->middleware('ajax');
Route::put('notrec', 'StockRequestController@notreceived')->middleware('ajax');
Route::put('resolved', 'StockRequestController@resolved')->middleware('ajax');
Route::delete('requesteditems', 'StockRequestController@requesteditems')->middleware('ajax');
Route::put('update_serial', 'StockRequestController@upserial')->middleware('ajax');
Route::get('mytest', 'StockRequestController@test');//->middleware('ajax');
Route::get('getitems', 'StockRequestController@getitems');//->middleware('ajax');

Route::get('users', 'UserController@getUsers')->middleware(['ajax', 'verified']);
Route::get('user', 'UserController@index')->name('user.index')->middleware(['verified']);
Route::get('getBranchName', 'UserController@getBranchName')->middleware(['ajax', 'verified']);
Route::post('user_add', 'UserController@store')->middleware(['ajax', 'verified']);
Route::put('user_update/{id}', 'UserController@update')->middleware(['ajax', 'verified']);

Route::get('stocks/{id}', 'BranchController@getStocks');//->middleware('ajax');
Route::get('branches', 'BranchController@getBranches')->middleware('ajax');
Route::get('loanbranches', 'BranchController@getLoanBranches')->middleware('ajax');
Route::get('branch', 'BranchController@index')->name('branch.index');
Route::post('branch_add', 'BranchController@store')->middleware('ajax');
Route::put('branch_ini', 'BranchController@initial')->middleware('ajax');
Route::put('branch_update/{id}', 'BranchController@update')->middleware('ajax');

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::get('print/{id}', 'HomeController@print')->name('branch.print.index');
Route::get('getprint/{id}', 'HomeController@getprint');//->middleware('ajax');
Route::get('initial/{id}', 'HomeController@initial');//->middleware('ajax');
Route::get('defective/print', 'HomeController@printDefective')->name('defective.print.index');
Route::get('defective/retno', 'HomeController@showret')->name('defective.print.index');
Route::get('retno', 'HomeController@showret')->name('defective.print.index');
Route::get('retno/{id}', 'HomeController@retshow')->name('defective.print.index');


Route::get('preventive', 'HomeController@preventive')->name('index.preventive');
Route::get('convert', 'HomeController@convert');
Route::get('sync', 'HomeController@sync');
Route::get('imp', 'HomeController@imp');
Route::get('backup-inventory', 'StockController@Backupinv')->name('backup-inventory');
Route::get('backup-branch', 'StockController@Backupbranch');

Route::get('getfsr', 'PreventiveController@getfsr');

Route::get('reports', 'ReportsController@index');

Route::get('reqcode', 'StockRequestController@reqcode');//->middleware('ajax');
Route::get('checkreqcode', 'StockRequestController@checkreqcode');//->middleware('ajax');

Route::get('delreqdata', 'StockRequestController@delreqdata');//->middleware('ajax');

Route::get('delete-approval', 'MailController@delapproval');//->middleware('ajax');
Route::get('delreqapproved', 'MailController@delreqapproved');//->middleware('ajax');
