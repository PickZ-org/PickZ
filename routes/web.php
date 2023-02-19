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

// Auth
Route::post('login/qrlogin', 'UserController@qrLogin')->name('qrlogin');
Auth::routes();

/**
 * Routes used for desktop and scanner
 */

// Find through ajax / available for all
Route::group(['middleware' => ['auth', 'role:admin,manager,picker']], function () {
    // Products
    Route::get('products/find', 'ProductController@find');
    Route::resource('products', 'ProductController');

    // Stock groups
    Route::get('stockgroups/find/{stockgrouptype?}', 'StockGroupController@find');
    Route::get('stockgroups/generate/{stockgrouptype}', 'StockGroupController@generate');

    //Documents & labels
    Route::get('document/stocklabel/{stock}', 'DocumentController@stockLabel');
    Route::post('document/zpl/stock', 'DocumentController@stockZpl');
    Route::get('scanner/label/stock', 'DocumentController@scannerLabel');
});

Route::group(['middleware' => ['auth', 'role:admin,manager,owner']], function () {
    //Dashboard
    Route::get('/', 'DashboardController@index');

    // Orders
    Route::get('orders/{direction}', 'OrderController@index')->where('direction', '(inbound)|(outbound)');
    Route::post('orders/bulk', 'OrderController@bulkActions');
    Route::resource('orders', 'OrderController')->name('show', 'showOrderRoute');

    // Invoices
    Route::post('invoices/bulk', 'InvoiceController@bulkActions');
    Route::resource('invoices', 'InvoiceController');

    // Contacts
    Route::resource('contacts', 'ContactController');

    // ProductUoms
    Route::post('productuoms/findbyproduct', 'ProductUomController@findByProduct');
    Route::post('productuoms/findbybarcode', 'ProductUomController@findByBarcode');
    Route::resource('productuoms', 'ProductUomController');
    // Set fixed locations
    Route::post('productuoms/fixedlocations', 'ProductUomController@addFixedLocation');
    Route::delete('productuoms/fixedlocations/{productuom}', 'ProductUomController@removeFixedLocation');
    Route::get('productuoms/fixedlocations/{productuom}', 'ProductUomController@getFixedLocation');
    Route::put('productuoms/fixedlocations/{productuom}', 'ProductUomController@updateFixedLocation');

    // Locations
    Route::get('locations/find', 'LocationController@find');
    Route::resource('locations', 'LocationController');

    // Users
    Route::delete('users/delete', 'UserController@destroy');
    Route::get('users/generateqr/{user}', 'UserController@generateQr');
    Route::post('users/generateapitoken', 'UserController@generateApiToken');
    Route::resource('users', 'UserController');

    // Tasks
    Route::get('tasks/{type}', 'TaskController@index')->where('type',
        '(putaway)|(replenishment)|(picking)|(shipping)|(crossdock)');

    // Tasklines
    Route::get('tasklines/{type}', 'TaskLineController@index')->where('type',
        '(putaway)|(replenishment)|(picking)|(shipping)|(move)|(crossdock)');
    Route::resource('tasklines', 'TaskLineController');

    // Stock
    Route::post('stock/move/{stock}', 'StockController@move');
    Route::resource('stock', 'StockController');

    //Stock group types
    Route::resource('stockgrouptype', 'StockGroupTypeController');

    // Configuration
    Route::resource('configuration', 'ConfigurationController');

    // Logs
    Route::resource('logs', 'LogController');

    // Documents
    Route::get('document/deliverynote/{order}', 'DocumentController@deliveryNote');
    Route::get('document/checklist/{order}', 'DocumentController@checkList');
    Route::get('document/picklist/{order}', 'DocumentController@picklist');
    Route::get('document/locationlabel/{location}', 'DocumentController@locationLabel');
    Route::get('document/productlabel/{product}', 'DocumentController@productLabel');
    Route::get('document/qrCode/{string?}', 'DocumentController@qrCode')->name('qrdoc');
});

/**
 * Picker / Scanner routes
 */
Route::group(['middleware' => ['auth', 'role:picker']], function () {
    // Scanner
    Route::get('scanner', 'Scanner\ScannerController@index')->name('scanner');

    // Scanner - receiving
    Route::get('scanner/receiving', 'Scanner\ReceivingController@index');
    Route::get('scanner/receiving/cold', 'Scanner\ReceivingController@receiveCold');
    Route::post('scanner/receiving/cold', 'Scanner\ReceivingController@processCold');
    Route::get('scanner/receiving/order/{order?}', 'Scanner\ReceivingController@displayOrder');
    Route::post('scanner/receiving/order/{order}', 'Scanner\ReceivingController@receiveOrder');


    // Scanner - putaway
    Route::get('scanner/putaway', 'Scanner\PutawayController@index');
    Route::get('scanner/putaway/{taskline}', 'Scanner\PutawayController@show');
    Route::get('scanner/putaway/{taskline}/move', 'Scanner\PutawayController@move');
    Route::get('scanner/putaway/{taskline}/finish', 'Scanner\PutawayController@finish');

    // Scanner - crossdock
    Route::get('scanner/crossdock', 'Scanner\CrossdockController@index');
    Route::get('scanner/crossdock/finish', 'Scanner\CrossdockController@finish');
    Route::get('scanner/crossdock/{task}', 'Scanner\CrossdockController@task');
    Route::get('scanner/crossdock/{task}/drop', 'Scanner\CrossdockController@drop');

    // Scanner - replenishment
    Route::get('scanner/replenishment', 'Scanner\ReplenishmentController@index');
    Route::get('scanner/replenishment/{taskline}', 'Scanner\ReplenishmentController@show');
    Route::get('scanner/replenishment/{taskline}/move', 'Scanner\ReplenishmentController@move');
    Route::get('scanner/replenishment/{taskline}/finish', 'Scanner\ReplenishmentController@finish');

    // Scanner - picking
    Route::get('scanner/picking', 'Scanner\PickingController@index');
    Route::get('scanner/picking/finish', 'Scanner\PickingController@finish');
    Route::get('scanner/picking/{task}', 'Scanner\PickingController@task');
    Route::get('scanner/picking/{task}/drop', 'Scanner\PickingController@drop');

    // Scanner - shipping
    Route::get('scanner/shipping', 'Scanner\ShippingController@index');
    Route::get('scanner/shipping/finish', 'Scanner\ShippingController@finish');
    Route::get('scanner/shipping/{task}', 'Scanner\ShippingController@task');
    Route::get('scanner/shipping/{task}/drop', 'Scanner\ShippingController@drop');

    // Scanner - move
    Route::get('scanner/move/new', 'Scanner\MoveController@newMove');
    Route::post('scanner/move/new/source', 'Scanner\MoveController@validateSource');
    Route::get('scanner/move/new/destination', 'Scanner\MoveController@newMoveDestination');
    Route::post('scanner/move/new/destination', 'Scanner\MoveController@validateDestination');
    Route::get('scanner/move', 'Scanner\MoveController@index');
    Route::get('scanner/move/{taskline}', 'Scanner\MoveController@show');
    Route::get('scanner/move/{taskline}/move', 'Scanner\MoveController@move');
    Route::get('scanner/move/{taskline}/finish', 'Scanner\MoveController@finish');

    // Scanner - AJAX Calls
    // Find UOMs by barcode
    Route::post('productuoms/findbybarcode', 'ProductUomController@findByBarcode');
});

/**
 * Datatables routes
 */
Route::group(['middleware' => ['auth', 'role:admin,manager,owner']], function () {
    // Orders
    Route::post('/datatables/orders/{direction?}', 'DatatablesController@orders');
    Route::post('/datatables/orderlines', 'DatatablesController@orderlines');

    // Invoices
    Route::post('/datatables/invoices', 'DatatablesController@invoices');
    Route::post('/datatables/invoicelines', 'DatatablesController@invoicelines');

    // Users
    Route::post('/datatables/users', 'DatatablesController@users');

    // Contacts
    Route::post('/datatables/contacts', 'DatatablesController@contacts');

    // Tasks
    Route::post('/datatables/tasks/{type?}', 'DatatablesController@tasks');

    // Tasklines
    Route::post('/datatables/tasklines/{type?}', 'DatatablesController@tasklines');

    // Stock
    Route::post('/datatables/stock', 'DatatablesController@stock');

    // Stock group types
    Route::post('/datatables/stockgrouptypes', 'DatatablesController@stockgrouptypes');

    // Products
    Route::post('/datatables/products', 'DatatablesController@products');

    // ProductsUoms
    Route::post('/datatables/productuoms', 'DatatablesController@productuoms');

    // Fixed locations
    Route::post('/datatables/fixedlocations', 'DatatablesController@fixedlocations');

    // Locations
    Route::post('/datatables/locations', 'DatatablesController@locations');

    // Logs
    Route::post('/datatables/logs', 'DatatablesController@logs');

    // Shipments
    Route::post('/datatables/shipmentlines', 'DatatablesController@shipmentlines');
});
