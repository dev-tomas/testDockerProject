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

use App\Exports\UsersExport;
use App\Http\Controllers\SireController;

Route::get('/', 'DashboardController@redirectLogin');

Auth::routes(); //Default user laravel
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::get('/home', 'HomeController@index')->name('home');

/**
 * Rutas para Permisos
 */
Route::get('/acl', 'AclController@index')->name('acl');
Route::get('/users', 'AclController@users')->name('users')->middleware('auth');;
Route::get('/roles', 'AclController@roles')->name('roles');
Route::get('/test', 'HomeController@test')->name('test');


/**
 * Rutas para Construir DataTable
 */
Route::get('acl.roles.dataTable', 'AclController@getRoleUserDataTable')->name('acl.roles.dataTable');


Route::post('consult.ruc/{ruc}', 'AjaxController@getCompanyInfo');
Route::post('consult.dni/{dni}', 'AjaxController@getPersonInfo');

//Routas para Ventas - SIRE
Route::get('/sire', [SireController::class, 'index'])->name('sire.index');
Route::get('/sire/obtener', [SireController::class, 'obtenerComprobantes'])->name('sunat.resumen');
Route::get('/exportar-propuesta', [SireController::class, 'exportarPropuesta'])->name('sunat.exportarPropuesta');
Route::get('/consultar-estado-periodo', [SireController::class, 'consultarEstadoPeriodo'])->name('consultar.estado.periodo');
Route::get('/descargar-archivo', [SireController::class, 'descargarArchivo'])->name('sunat.descargarArchivo');

//Routas para Compras - SIRE
Route::get('/sire/compras', [SireController::class, 'index_compras'])->name('compras.index');
Route::get('/sire/obtenercompras', [SireController::class, 'obtenerComprobantesCompras'])->name('sunat.resumencompras');

/**
 * Routes Commercial
 */
Route::get('/search/file', 'CommercialController@searchFile')->name('searchFile');
Route::get('/commercial.sales', 'CommercialController@sales')->name('commercial.sales');
Route::post('/commercial.sales/gettypeigv', 'CommercialController@getTypeIGV');
Route::get('/commercial.sales.create/{type}', 'CommercialController@sale')->name('nuevaventa');
Route::get('/commercial.sales.edit/{type}/{id}', 'CommercialController@saleEdit')->name('editarventa');
Route::get('/commercial.customers', 'CommercialController@customers');
Route::get('/commercial.config.vouchers', 'CommercialController@configCorrelatives');
Route::get('/commercial.report.sales', 'ReportController@reportSales');
Route::get('/commercial.export', 'CommercialController@exportSales')->name('export.sales');

Route::post('/comercial.getTotalSale', 'CommercialController@getTotalSales');


//Route::post('/commercial.download.xml', 'CommercialController@downloadXML');
Route::get('/commercial.download.xml/{file}', 'CommercialController@downloadXML');
Route::get('/files/cdr/{file}', 'CommercialController@downloadCdr');

/**
 * Quotations
 */
Route::get('/commercial.quotations', 'CommercialController@quotations');
Route::get('/commercial.quotations.create', 'CommercialController@quotation');
Route::get('/commercial.quotations.delete', 'CommercialController@deleteQuotation');
Route::get('/commercial.quotations.show.pdf/{id}', 'CommercialController@showPdfQuotation');
Route::get('/commercial.quotations.download.pdf/{id}', 'CommercialController@downloadPdfQuotation');
Route::post('/commercial.quotations.send', 'CommercialController@sendQuotation');
Route::get('/commercial.quotations.edit/{idQuotation}', 'CommercialController@quotationEdit');

/**
 * Routes WareHouses
 */
Route::get('/warehouse.list', 'WarehouseController@warehouses');
Route::get('/warehouse.products', 'WarehouseController@products');
Route::get('/warehouse.categories', 'WarehouseController@categories');
Route::get('/warehouse.transfers', 'WarehouseController@transfers');
Route::get('/warehouse.reports', 'ReportController@warehouses');

Route::get('/warehouse.dt.categories', 'WarehouseController@dt_categories');
Route::post('/warehouse.category.save', 'WarehouseController@saveCategory');
Route::post('/warehouse.category.prepare', 'WarehouseController@getCategory');
Route::get('/warehouse.category.all', 'WarehouseController@getAllCategories');
Route::get('/warehouse.products.exports', 'WarehouseController@export')->name('products.export');
Route::post('/warehouse.classification.save', 'WarehouseController@saveClassification');
Route::get('/warehouse.classification.all', 'WarehouseController@getAllClassifications');

Route::get('/warehouse/kits', 'Logistic\KitsController@index');
Route::get('/warehouse/kits/dt', 'Logistic\KitsController@dt');
Route::post('/warehouse/kits/store', 'Logistic\KitsController@store');
Route::post('/warehouse/kits/prepare', 'Logistic\KitsController@prepare');
Route::post('/warehouse/kits/generate', 'Logistic\KitsController@generate');
Route::get('/warehouse/kits/get-items', 'Logistic\KitsController@getItems');

/**
 * Routes Accounting
 */
// Route::get('/accounting.vouchers', 'AccountingController@vouchers');
// Route::get('/accounting.contingencies', 'AccountingController@contingencies');
// Route::get('/accounting.guides', 'AccountingController@guides');
// Route::get('/accounting.inconsistencies', 'AccountingController@inconsistencies');
// Route::get('/accounting.cancellations', 'AccountingController@cancellations');

/**
 * Routes Configurations
 */
Route::get('/configuration.company', 'ConfigurationController@company');
Route::get('/configuration.company/get-exchangerate', 'ExchangeRateController@getExchange');
Route::get('/get-exchangerate/by-date/{date}', 'ExchangeRateController@getExchangeByDate');
Route::get('/configuration.appearance', 'ConfigurationController@appearance');
Route::get('/configuration/invoices', 'ConfigurationController@invoices');
Route::post('/configuration/saveOne', 'ConfigurationController@saveOne')->name('saveConfigurationOne');
Route::post('/configuration/saveTwo', 'ConfigurationController@saveTwo')->name('saveConfigurationTwo');
Route::post('/configuration/saveThree', 'ConfigurationController@saveThree')->name('saveConfigurationThree');
Route::post('/configuration/saveImage', 'ConfigurationController@saveImage')->name('saveImage');
Route::delete('deleteBankAccount', 'ConfigurationController@deleteBankAccount')->name('deleteBankAccount');
Route::delete('deletePaymentMethod', 'ConfigurationController@deletePaymentMethod')->name('deletePaymentMethod');

Route::post('/configuration/store/api-sunat', 'ConfigurationController@storeSunatCredentialsApi');
Route::get('/reference-guide/consult-api-sunat/{id}', 'ReferenceGuideController@consultToApi');
Route::post('/reference-guide/get-sale-detail', 'ReferenceGuideController@getSaleDetail');


/**
 * Routes Analytics
 */

Route::get('/analytics', 'AnalyticsController@report')->name('analytics');
Route::get('/analytics.reports', 'AnalyticsController@report')->name('reports');
Route::post('/analytics.reports.generate', 'AnalyticsController@getReport');
Route::post('/analytics.reports.generate.table', 'AnalyticsController@getReportTable');
Route::get('/analytics.reports.generate.pdf', 'AnalyticsController@getReportPDF');
Route::get('/analytics.reports.generate.excel', 'AnalyticsController@getReportExcel');
Route::post('/analytics.reports.index', 'AnalyticsController@getReportIndex');
Route::get('/analytics.reports.test', 'AnalyticsController@test');


//Route::get('/analytics/analytics', [AnalyticsController::class, 'filtro']);
Route::get('/analytics/analytics', 'AnalyticsController@filtro')->name('filtro');
Route::post('/filtro', 'AnalyticsController@filtro')->name('filtro');

/**
 * Configuration Headquarters
 */
Route::get('/configuration.headquarters', 'ConfigurationController@headquarters')->name('configuration.headquarters');
Route::get('/configuration/headquarters/edit/{id}', 'ConfigurationController@editHeadQuarter')->name('configuration.headquarters.edit');
Route::get('/configuration.dt.headquarter', 'ConfigurationController@dt_headquarters')->name('dt_headquarters');
Route::get('addHeadquarter', 'ConfigurationController@addHeadquarter')->name('addHeadquarter');
Route::get('deleteCorrelative', 'ConfigurationController@deleteCorrelative')->name('deleteCorrelative');
Route::post('saveHeadQuarter', 'ConfigurationController@saveHeadQuarter')->name('saveHeadQuarter');
Route::put('updateHeadQuarter', 'ConfigurationController@updateHeadQuarter')->name('updateHeadQuarter');

/**
 * Configuration Users
 */
Route::get('/configuration.users', 'ConfigurationController@users')->name('users');
Route::get('/configuration.users.create', 'ConfigurationController@newUser')->name('configuration.users.create');
Route::get('/configuration/users/edit/{user}', 'ConfigurationController@editUser');
Route::get('dt_users', 'ConfigurationController@dt_users')->name('dt_users');
Route::post('create_user', 'ConfigurationController@createUser')->name('create_user');
Route::post('update_user/{id}', 'ConfigurationController@updateUser')->name('update_user');
Route::put('/configuration/users/updateStatus', 'ConfigurationController@updateStatusUser')->name('updateStatusUser');
Route::get('/configuration/users/download', 'ConfigurationController@userExport')->name('downloadUsers');

/**
 * Configuration Invoices
 */
Route::put('/configuration/save/sol', 'ConfigurationController@saveSol')->name('saveDataSol');


/**
 * Crud Área Comercial
 */
Route::post('/commercial.customer.create', 'CommercialController@createCustomer');
Route::post('/commercial.customer.update', 'CommercialController@updateCustomer');
Route::get('/commercial.customer.delete', 'CommercialController@deleteCustomer');
Route::post('/commercial/customer/getcode', 'CommercialController@getLastCustomerCode');
Route::post('/commercial.quotations.create', 'CommercialController@createQuotation');
Route::post('/commercial.quotations.edit', 'CommercialController@editQuotation');
//Route::post('/commercial.sales.create', 'CommercialController@createSale');
Route::post('/commercial.quotations.convert', 'CommercialController@convertQuotation');
Route::post('/commercial.quotations.products', 'AjaxController@getProducts');
Route::post('/commercial.sales.create', 'CommercialController@createSale');
Route::post('/commercial.sales.update', 'CommercialController@updateSale');
Route::post('/commercial.correlatives.create', 'CommercialController@createCorrelative');
Route::get('/commercial.customer.all/{type?}', 'CommercialController@getCustomers');

Route::get('/commercial.sales.notes.list', 'CommercialController@noteindex')->name('noteList');
Route::get('/commercial.sales.notes.get', 'CommercialController@dt_notes');
Route::get('/commercial.sales.notes.debit.get', 'CommercialController@dt_notesDebit');

Route::post('/commercial.customer.prepare', 'CommercialController@getCustomer');
Route::get('/commercial.correlative.prepare', 'CommercialController@getCorrelative');

Route::post('/commercial.sales.send/{id}', 'CommercialController@sendSaleClient');
Route::post('/commercial.notes.send/{id}', 'CommercialController@sendNoteClient');
Route::post('/commercial.notes.debit.send/{id}', 'CommercialController@sendNoteDebitClient');

/**
 * Routes for DataTables
 */
Route::get('/commercial.dt.quotations', 'CommercialController@dt_quotation');
Route::get('/commercial.dt.sales', 'CommercialController@dt_sales');
Route::get('/commercial.dt.sales.2/{type}', 'CommercialController@dt_sales_2');
Route::get('/commercial.dt.customers', 'CommercialController@dt_customers');
Route::get('/commercial.dt.correlatives', 'CommercialController@dt_correlatives');

/**
 * Send to Sunat
 */
Route::post('/commercial.sales.sunat.send/{id}/{type?}', 'CommercialController@enviarSoloSunat');
Route::get('/commercial.sales.print', 'SunatController@printBoucher');


/**
 * Routes Companies
 */
Route::get('/configuration.company', 'ConfigurationController@company');

/**
 * Routes Warehouses
 */
Route::get('/warehouse.list', 'WarehouseController@warehouseList');
Route::get('/commercial.dt.warehouses', 'WarehouseController@dt_warehouses');
Route::post('/warehouse.save', 'WarehouseController@saveWarehouse');
Route::get('/warehouse.prepare', 'WarehouseController@getWareHouse');
Route::get('searchProductByCodeBar', 'AjaxController@searchProductByCodeBar')->name('searchProductByCodeBar');
/**
 * Route Products
 */
Route::get('/warehouse.products', 'WarehouseController@productList');
Route::get('/warehouse.dt.products', 'WarehouseController@dt_products');
Route::post('/warehouse.product.save', 'WarehouseController@saveProduct');
Route::get('/warehouse.product.delete', 'WarehouseController@deleteProduct');
Route::post('/warehouse.products.delete', 'WarehouseController@deleteProducts');
Route::get('/warehouse.product.prepare', 'WarehouseController@getProduct');
Route::post('/warehouse.product.status.update', 'WarehouseController@updateStatus');
Route::post('/warehouse.products.status.update','WarehouseController@updateStatusProducts');
Route::get('/warehouse/products/catalog','Logistic\CatalogController@generateCatalog');

Route::post('/product/price/list/delete', 'WarehouseController@deleteProductPriceList');

Route::post('/import/products', 'WarehouseController@importProducts')->name('import.products');
Route::get('/pos/search/product', 'AjaxController@searchProduct');
Route::get('/product/price/list/get', 'AjaxController@getProductPriceList');

/**
 * Customers
 */
Route::post('/commercial.import.customers', 'CommercialController@importCustomers')->name('import.customers');
Route::get('/commercial.export.customers', 'CommercialController@exportCustomers')->name('export.customers');
Route::get('/commercial.export.template', 'CommercialController@exportCustomersTemplate')->name('export.customers.template');
Route::get('/warehouse.export.template', 'WarehouseController@exportProductsTemplate')->name('export.products.template');

/**
 * Routes Logistic
 */

Route::get('/logistic.productions', 'LogisticController@productions');
Route::get('/logistic.productions', 'ReportController@purchases');


Route::get('/logistic.report.purchases', 'ReportController@reportPurchase');
Route::post('/logistic.report.purchases.get', 'ReportController@getLogisticReport');
Route::get('/logistic.reports.purchases.generate.excel', 'ReportController@generateExcelReportExcel');
Route::get('/logistic.reports.purchases.generate.pdf', 'ReportController@generatePDFReportPDF');



/**
 * Providers
 */
Route::get('/logistic.providers', 'LogisticController@providers');
Route::post('/logistic.providers/get-provider-code', 'LogisticController@getLastProviderCode');
Route::post('/logistic.import.providers', 'LogisticController@importProviders')->name('import.providers');
Route::get('/logistic.export.providers', 'LogisticController@exportProviders')->name('export.providers');
Route::get('/logistic/download/templateProviders', 'LogisticController@exportProvidersTemplate');
Route::get('/logistic.export.template.provider', 'LogisticController@exportProvidersTemplate')->name('export.providers.template');
Route::get('/logistic.dt.providers', 'LogisticController@dt_providers');
Route::post('/logistic.provider.create','LogisticController@createProviders');
Route::get('/logistic.provider.get','LogisticController@getProviders')->name('provider.get');
Route::post('/logistic.provider.update','LogisticController@createProviders');
Route::get('provider.detroy','LogisticController@deleteProviders')->name('provider.detroy');

Route::post('logistic.providers', 'LogisticController@saveProvider')->name('saveProvider');
Route::get('getAllProviders','LogisticController@getAllProviders')->name('getAllProviders');
Route::post('saveShopping', 'LogisticController@saveShopping')->name('saveShopping');

/**
 * Purchases
 */



/**
 * Routes Brands
 */
Route::post('/logistic.brand.save', 'LogisticController@saveBrand');
Route::get('/logistic.brand.get', 'LogisticController@getBrand');

Route::get('/nuevo','LogisticController@providers');

/**
 * Routes consult CDR
 */
Route::post('/commercial.sales.consultcdr', 'CommercialController@consultcdr');


/**
 * Routes Roles
 */
Route::get('/roles', 'RolesController@index')->name('roles.index');
Route::get('/roles/get', 'RolesController@getRoles')->name('roles.get');
Route::get('/roles/{role}/edit', 'RolesController@edit')->name('roles.edit');
Route::get('/roles/create', 'RolesController@create')->name('roles.create');
Route::post('/roles/store', 'RolesController@store')->name('roles.store');
Route::put('/roles/{role}', 'RolesController@update')->name('roles.update');
Route::delete('/roles/{role}', 'RolesController@destroy')->name('roles.delete');
Route::get('/roles/prepare', 'RolesController@prepare');
Route::post('/sales/getcorrelative/{serialnumber}/{type}', 'CommercialController@getCorrelativeS');

Route::get('/commercial.reports', 'ReportController@report')->name('reports');
Route::post('/commercial.reports.generate', 'ReportController@getReport');
Route::post('/commercial.reports.generate.table', 'ReportController@getReportTable');
Route::get('/commercial.reports.generate.pdf', 'ReportController@getReportPDF');
Route::get('/commercial.reports.generate.excel', 'ReportController@getReportExcel');
Route::post('/commercial.reports.index', 'ReportController@getReportIndex');
Route::get('/commercial.reports.test', 'ReportController@test');

/**
 * Route Ajax
 */
Route::get('allCoins', 'AjaxController@getCoins')->name('allCoins');
Route::get('allBankAccountType', 'AjaxController@getBankAccountTypes')->name('allBankAccountType');
Route::get('allVoucherTypes', 'AjaxController@getTypeVouchers')->name('allVoucherTypes');

/**
 * Helpers
 */
Route::post('/certificate/convert/save', 'HelperController@convertAndCertificate')->name('convertAndCertificate');


/**
 * Sale
 */
Route::get('/commercial.sale.show.pdf/{correlative}/{serial_number}/{type_voucher_code}', 'CommercialController@showPdfSale');

/**
 * Requirements
 */
Route::get('requirements', 'RequirementController@index')->name('requirements');
Route::get('addRequirements','RequirementController@create')->name('addRequirements');
Route::post('/requirements/products','RequirementController@getProducts');
Route::post('/requirements/categories','RequirementController@getCategories');
Route::post('/requirements/store','RequirementController@store');
Route::get('/requirements/dt','RequirementController@dt_requirements');
Route::get('/requirements/edit/{serie}/{correlative}','RequirementController@edit');
Route::post('/requirements/update','RequirementController@update');
Route::get('/requirements/pdf/{id}','RequirementController@showPDF');
Route::get('/requirements/export/','RequirementController@exportRequirement')->name('requirement.export');

/**
 * Purchase Eletronic
 */
 Route::get('/logistic.purchase','LogisticController@purchases');
 Route::get('/logistic.physicalRecord', 'LogisticController@physicalRecord')->name('physicalRecord');
 Route::get('/logistic.purchases', 'LogisticController@purchases')->name('logistic.purchases');
 Route::get('/logistic.purchases/edit/{id}', 'LogisticController@editPurchase');
 Route::post('/logistic.purchases/update', 'LogisticController@updatePurchase');
 Route::post('/logistic.purchase.xml', 'LogisticController@purchaseElec');
 Route::post('/logistic.purchase.xml.provider', 'LogisticController@registerProviderXML');
 Route::post('/logistic.purchase.xml.product', 'LogisticController@registerProductXML');
 Route::get('/logistic.purchase.get.provider', 'LogisticController@getProvider');
 Route::get('/logistic.purchase.dt', 'LogisticController@dt_shopping');
 Route::get('/logistic.purchase.pdf/{id}', 'LogisticController@pdf_shopping');

 Route::post('/logistic.purchase.sendRequirements', 'LogisticController@sendRequirements');
 Route::get('/logistic.purchase.excel', 'LogisticController@excelPurchases')->name('purchase.excel');
 Route::post('/logistic.purchases/delete', 'LogisticController@deletePurchase');
 Route::post('/logistic.purchases.getTotalSale', 'LogisticController@getTotalShoppings');

 Route::post('/logistic.purchases.register-sire', 'LogisticController@registerSirePurchase');

 Route::get('/recibos-honorarios', 'ReceiptsFeesController@index');
 Route::get('/recibos-honorarios/dt', 'ReceiptsFeesController@dt_shopping');
 Route::get('/recibos-honorarios/crear', 'ReceiptsFeesController@create');
 Route::get('/recibos-honorarios/editar/{s}', 'ReceiptsFeesController@edit');
 Route::post('/recibos-honorarios/store', 'ReceiptsFeesController@store');
 Route::post('/recibos-honorarios/update', 'ReceiptsFeesController@update');
 Route::post('/recibos-honorarios/delete', 'ReceiptsFeesController@delete');
 Route::get('/recibos-honorarios/export', 'ReceiptsFeesController@export');
 Route::get('/recibos-honorarios/show/{id}', 'ReceiptsFeesController@show');

 Route::post('/logistic.recibos-honorarios.register-sire', 'LogisticController@registerSireReceipts');


 Route::get('/logistic.purchase.show/{serie}/{correlative}', 'LogisticController@showPurchase')->name('purchase.show');

 Route::get('/logistic.providers.proposals', 'LogisticController@showProposals')->name('proposal.get');
 Route::post('/logistic.providers.proposals.get', 'LogisticController@getProposals');
 Route::post('/logistic.providers.proposals.generateoc', 'LogisticController@createOc');

 Route::get('/quotation/set/{ruc}/{requirement}','UnLoginController@setQuotation')->name('setQuotation')->middleware('signed');
 Route::get('/quotation/set/finish','UnLoginController@finish')->name('finish');
 Route::post('/logistic/purchase/quotation/set/store','UnLoginController@store')->name('store.quotation');

 Route::get('/logistic.order.purchase', 'LogisticController@indexOC')->name('order.purchase');
 Route::get('/logistic.order.purchase.get', 'LogisticController@dt_purchaseOrders');
 Route::get('/logistic.order.purchase.delete', 'LogisticController@deleteOC');
 Route::post('/logistic.order.purchase.send', 'LogisticController@sendOC');
 Route::get('/logistic.order.purchase.edit/{serie}/{correlative}','LogisticController@editOC');
 Route::post('/logistic.order.purchase.update','LogisticController@updateOC');
 Route::get('/logistic.order.purchase.excel','LogisticController@excelOc')->name('purchaseorder.excel');

 Route::post('/get-credit-note-returned','CommercialController@getCreditNoteReturned');


 /**
  * Notes
  */
  Route::get('/commercial/sale/note/{id}/{type}', 'CommercialController@createNote');
  Route::get('/commercial.sale.note.debit/{id}/{type}', 'CommercialController@createNoteDebit');
 Route::post('/commercial/sale/note/create', 'CommercialController@saveNote')->name('saveNote');
 Route::post('/commercial/sale/note/debit/create', 'CommercialController@saveNoteDebit')->name('saveNoteDebit');
 Route::post('/commercial/sale/note/get', 'CommercialController@getNote')->name('getNote');
 Route::get('/commercial.sale.note.pdf/{id}', 'CommercialController@pdfNote')->name('pdfNote');
Route::get('/commercial.sale.note.debit.pdf/{id}', 'CommercialController@pdfNoteDebit')->name('pdfNoteDebit');
Route::post('/commercial/sale/note/debit/get', 'CommercialController@getDebitNote')->name('getDebitNote');
 Route::get('/commercial.sale.note.debit.pdf/{id}', 'CommercialController@pdfNoteDebit')->name('pdfNoteDebit');

 Route::post('/commercial/note/send/sunat/{id}/{type}/{opc}', 'SunatController@sendNote');
/**
 * Accounting
 */

 Route::get('/account/notes/credit', 'AccountController@indexCredit');
 Route::get('/account/notes/debit', 'AccountController@indexDebit');


 /**
  * Buscar Comprobantes
  */
  Route::get('/buscar-comprobante/find', 'BuscarComprobanteController@search')->name('buscar-comprobante.find');
  Route::get('/buscar-comprobante/{ruc?}', 'BuscarComprobanteController@index')->name('buscar.comprobante');

  Route::get('/comprobante/{$id}/download', 'BuscarComprobanteController@showPdfSale')->name('buscarcomprobante.pdf');

  /**
   * Route CostCenters
   *
   */
  Route::get('/logistic.costcenter', 'CostCenterController@index');
  Route::get('/logistic.costcenter.dt', 'CostCenterController@dt_costcenter');
  Route::post('/logistic.costcenter.save', 'CostCenterController@saveCenter');
  Route::post('/logistic.costcenter.get', 'CostCenterController@getCenters');
  Route::post('/logistic.costcenter.get2', 'CostCenterController@getCenter');
  Route::post('/logistic.costcenter.delete', 'CostCenterController@deleteCenters');

    Route::post('/commercial.quotations.totals', 'CommercialController@getTotalsQuotations');

  /**
   * Low
   */
  Route::post('/commercial/send/low/communication', 'CommercialController@sendLowCommunication');
  Route::post('/commercial/send/low/communication/only/{id}', 'CommercialController@sendLowCommunicationOnly');
  Route::get('/commercial/lows', 'CommercialController@lowCommunications');
  Route::get('/commercial/low/dt', 'CommercialController@dtLows');
  Route::get('/commercial/low/show/pdf/{id}', 'CommercialController@showPdfLow');

  Route::post('/logitic.purchase.update', 'LogisticController@updateShopping');

  /**
   * MANAGE AREA
   */
  Route::get('/manage', 'ManageController@index')->name('mange.index');
  Route::get('/manage/company', 'Manage\CompanyController@index')->name('mange.company');
  Route::get('/manage/company/dt', 'Manage\CompanyController@dt_clients')->name('mange.dt_clients');
  Route::post('/manage/company/new/company', 'Manage\CompanyController@storeCompany')->name('mange.new_company');

  Route::get('/manage/monitor', 'Manage\MonitorController@index')->name('manage.monitor');
  Route::get('/manage/monitor/dt', 'Manage\MonitorController@dt')->name('manage.monitor.dt');

  Route::get('/manage/cpe', 'Manage\MonitorController@cpes')->name('manage.cpe');
  Route::get('/manage/cpe/dt', 'Manage\MonitorController@cpedt');

  Route::get('/manage/api', 'Manage\MonitorController@api')->name('manage.api');
  Route::get('/manage/api/dt', 'Manage\MonitorController@apiDt');

  Route::post('/manage/api/create', 'Api\ClientTokenController@generateToken');
  Route::get('/manage/api/tokens', 'Manage\MonitorController@indexTokens')->name('manage.api.tokens');
  Route::get('/manage/api/tokens/dt', 'Manage\MonitorController@dtTokens');
  Route::post('/manage/api/tokens/change-status', 'Manage\MonitorController@tokenChangeStatus');
  Route::post('/manage/api/tokens/delete', 'Manage\MonitorController@tokenDelete');

  Route::post('/manage/monitor/consutl-sunat-status/{sale}', 'Commands\ConsutlCPEController@consultManual');
  Route::post('/manage/monitor/consutl-sunat-status/by-date/{date}', 'Manage\ConsulCPEController@consultByDate');

Route::get('/manage/consult-integration-invoice/{date}', 'Commands\SendInvoiceController@consultCDRInvoice');

  Route::get('/manage/company/revertir', 'ManageController@revertir')->name('mange.revertir');
  Route::get('/manage/company/{ruc}', 'ManageController@supplant')->name('mange.supplant');


  /**
   * Inventarios
   */

   Route::get('/transfer', 'TransferController@index');
   Route::get('/transfer/dt', 'TransferController@dt_transfer');
   Route::post('/transfer/disabled', 'TransferController@disabled');
   Route::post('/transfer/get-data-duplicate', 'TransferController@getDataForDuplicate');
   Route::post('/transfer/store', 'TransferController@store');
   Route::get('/transfer/get-warehouse-transfer', 'TransferController@getWarehousesTransfer');
   Route::get('/transfer/get-products-by-warehouse', 'TransferController@getProductsByWarehouse');
   Route::get('/transfer/show-pdf/{id}', 'TransferController@showPdf');

   Route::get('/inventory', 'InventoryController@index');
   Route::post('/inventory/admission/store', 'InventoryController@storeAdmission');
   Route::post('/inventory/admission/update', 'InventoryController@updateAdmission');
   Route::get('/inventory/dt_inventory', 'InventoryController@dt_inventory');
   Route::get('/inventory/generate/pdf', 'InventoryController@getPDFInventary');
   Route::get('/inventory/generate/excel', 'InventoryController@getEXCELInventary');
   Route::get('/inventory/generate/pdf/{id}/shopping', 'InventoryController@generateShoppingPDF');
   Route::get('/inventory/generate/pdf/{id}/shopping', 'InventoryController@generateShoppingPDF');
   Route::get('/inventory/edit/{serie}/{correlative}', 'InventoryController@edit');
   Route::get('/inventory/{serie}/{correlative}', 'InventoryController@newAdmission');

   /**
    * KARDEX
    */

    Route::get('/kardex', 'KardexController@index')->name('kardex.index');
    Route::post('/kardex/generate', 'KardexController@generate')->name('kardex.generate');
    Route::get('/kardex/generate/excel', 'KardexController@generateExcel')->name('kardex.generateExcel');
    Route::get('/kardex/generate/pdf', 'KardexController@generatePDF')->name('kardex.generatePDF');
    Route::post('/kardex/addmovement', 'KardexController@addMovement');

    Route::get('/kardex-valorizado', 'KardexController@indexValorize')->name('kardex.index.valorize');
    Route::post('/kardex-valorizado/generate', 'KardexController@generateValorize')->name('kardex.generate.valorize');
    Route::get('/kardex-valorizado/generate/excel', 'KardexController@generateValorizeExcel')->name('kardex.generateExcel.valorize');
    Route::get('/kardex-valorizado/generate/pdf', 'KardexController@generateValorizePDF')->name('kardex.generatePDF.valorize');
    /*
    KARDEX - FISICO
    */

    Route::get('/kardex-fisic', 'KardexController@indexFisic')->name('kardex.index.fisic');
    Route::post('/kardex-fisic/generate', 'KardexController@generateFisic')->name('kardex.generate.fisic');
    Route::get('/kardex-fisic/generate/excel', 'KardexController@generateFisicExcel')->name('kardex.generateExcel.fisic');
    Route::get('/kardex-fisic/generate/pdf', 'KardexController@generateFisicPDF')->name('kardex.generatePDF.fisic');

    /**
     * Reference Guides
     */
    Route::get('/reference-guide', 'ReferenceGuideController@index');
    Route::get('/reference-guide/dt', 'ReferenceGuideController@dt_guide');
    Route::post('/reference-guide/store', 'ReferenceGuideController@store');
    Route::get('/reference-guide/create/from-transfer', 'ReferenceGuideController@createTransfer');
    Route::get('/reference-guide/show/pdf/{id}', 'ReferenceGuideController@showPDF');
    Route::post('/reference-guide/getcorrelative/{serie}', 'ReferenceGuideController@getCorrelative');
    Route::get('/reference-guide/create/{serie?}/{correlative?}', 'ReferenceGuideController@create')->name('referenceguide.create');
    Route::post('/commercial/references/sunat/send/{id}', 'SunatController@sendReferralGuide');

    /**
     * Summary
     */
    Route::get('/commercial.summary', 'CommercialController@summary')->name('generateSummary');
    Route::get('/commercial/summary/send/{dacommercial/summary/sendte}', 'CommercialController@sendSummary');

   /*
   * Production
   */

   Route::post('/production', 'ProductionController@production');
   /**
    * Change local
    */
    Route::put('/change-local', 'ChangeHeadquarterController@change');


    /**
     * ICONS DASHBOARD
     */
    Route::post('/dashboard-icon/store', 'DashboardController@store')->name('dashboard.store');

    /**
     * Spot
     */
    Route::get('/retentions', 'SpotController@retentions');
    Route::get('/retention/create', 'SpotController@createRetention');
    Route::post('/retention/store', 'SpotController@storeRetention');
    Route::get('/retention/pdf/{id}', 'SpotController@showPdfRetention');
    Route::post('/retention/send/sunat/{id}','SunatController@sendRetention');
    Route::get('/spot/retention/dt', 'SpotController@dt_retention');

    Route::get('/perceptions', 'SpotController@perceptions');
    Route::get('/perception/create', 'SpotController@createPerception');
    Route::post('/perception/store', 'SpotController@storePerception');
    Route::get('/perception/pdf/{id}', 'SpotController@showPdfPerception');
    Route::post('/perception/send/sunat/{id}','SunatController@sendPerception');
    Route::get('/spot/perception/dt', 'SpotController@dt_perception');


    Route::get('/ajax/sales/{type}', 'AjaxController@getSales');

    Route::post('/disable/voucher/{id}/{type}', 'CommercialController@disableVoucher');

    Route::post('/configuration.theme.store', 'ThemeController@store')->name('theme.store');


    /**
     * Conta Sys
     */
    Route::get('/report/sales', 'ReportController@reportSales');
    Route::get('/report/sales/download/{since?}/{until?}/{type_voucher?}', 'ReportController@downloadReport');
    Route::get('/commercial/dt/sales/report', 'CommercialController@dt_sales_report');

    Route::get('/commercial/summary/pdf/{id}', 'CommercialController@showSummaryPdf');
    Route::get('/summary/get/not/send/{date}', 'CommercialController@getSummaryNotSend');
    Route::post('/commercial/summary/send/{id}/{condition?}', 'CommercialController@sendSummaryO');
    
    Route::get('/commercial/cashes', 'CashesController@index')->name('cashes.index');
    Route::post('/commercial/cashes/store', 'CashesController@storeCash')->name('cashes.store');
    Route::get('/commercial/cashes/dt', 'CashesController@dt_cashes')->name('cashes.dt');
    Route::post('/commercial/cashes/open', 'CashesController@openCash')->name('cashes.open');
    Route::post('/commercial/cashes/close', 'CashesController@closeCash')->name('cashes.close');
    Route::post('/commercial/cashes/store/movement', 'CashesController@storeMovement')->name('cashes.movement');
    Route::get('/commercial/cashes/movements', 'CashesController@movementIndex')->name('movement.index');
    Route::post('/commercial/cashes/movements/generate', 'CashesController@movementGenerate')->name('movement.generate');
    Route::get('/commercial/cashes/movements/generate/excel', 'CashesController@movementGenerateExcel')->name('movement.generateExcel');
    Route::get('/commercial/cashes/movements/generate/pdf', 'CashesController@movementGeneratePDF')->name('movement.generatePDF');
    
    Route::get('/commercial/cashes/cierres', 'CashesController@liquidations')->name('cashes.closings');
    Route::get('/commercial/cashes/cierres/dt', 'CashesController@dt_liquidations');
    Route::get('/commercial/cashes/cierres/pdf/{id}', 'CashesController@showLiquidationsPdf');


    Route::get('/pos', 'PosController@index')->name('pos.index');
    Route::post('/pos/store', 'PosController@storeSale')->name('pos.store');
    Route::get('/pos/pdf/{id}', 'PosController@showPdfSale')->name('pos.pdf');

    Route::get('/finances/payment-providers', 'CreditsProviderController@index');
    Route::get('/finances/payment-providers/dt', 'CreditsProviderController@dt_creditsProviders');
    Route::post('/finances/payment-providers/get-payments', 'CreditsProviderController@getPayment');
    Route::post('/finances/payment-providers/store-payments', 'CreditsProviderController@storePayment');
    Route::get('/finances/payment-providers/excel/export', 'CreditsProviderController@exporCredit');
    Route::get('/finances/payment-providers/pdf/export', 'CreditsProviderController@exporCreditPdf');

    Route::post('/logistic.purchase/update-method-payment', 'LogisticController@updateMethodPaymentShopping');


    Route::get('/inventario/real', 'InventoryController@inventarioReal');
    Route::get('/dtInventarioReal', 'InventoryController@dtInventarioReal');

    // Route::get('/registeremailtest', 'TestsController@registerEmail');


    /**
     * PRECIO DE LISTA
     */
    Route::get('/price-list', 'LogisticController@priceListIndex');
    Route::get('/price-list/dt', 'LogisticController@priceListDt');
    Route::post('/price-list/save', 'LogisticController@priceListSave');
    Route::post('/price-list/get', 'LogisticController@priceListGet');

    // IMPUESTOS

    Route::get('/impuestos', 'TaxController@index');
    Route::post('/impuestos/save', 'TaxController@saveTax');
    Route::post('/impuestos/get', 'TaxController@getTax');
    Route::get('/impuestos/dt', 'TaxController@dt_taxes');


    // CREDITS
    Route::post('/finances/credits/getpayments', 'CreditController@getPayment');
    Route::post('/finances/credits/getcredit', 'CreditController@getCredit');
    Route::post('/finances/credits/payment/store', 'CreditController@storePayment');

    Route::post('/finances/credits/provider/getcredit', 'CreditsProviderController@getCredit');
    Route::post('/finances/credits/provider/payment/store', 'CreditsProviderController@storePayment');
    Route::post('/finances/credits/provider/getpayments', 'CreditsProviderController@getPayment');


    // ACCOUNTING
    Route::get('/accounting.purchase-2-13-6','AccountingController@indexPurchaseNewVersion');
    Route::post('/accounting/purchase-2-13-6/preview/generate', 'AccountingController@generatePreviewNewVersion');
    Route::get('/accounting/purchase-2-13-6/generate', 'AccountingController@generateExcelNewVersion');

    Route::get('/accounting.purchase','AccountingController@indexPurchase');
    Route::post('/accounting/purchase/preview/generate', 'AccountingController@generatePreview');
    Route::get('/accounting/purchase/generate', 'AccountingController@generateExcel');
    
    Route::get('/accounting.sales','AccountingController@indexSale');
    Route::get('/accounting/sale/generate', 'AccountingController@generateSaleInterfaz');
    Route::get('/accounting.sales-2-13-6','AccountingController@indexSaleNewVersion');
    Route::get('/accounting/sale-2-13-6/generate', 'AccountingController@generateSaleInterfazNewVersion');

    Route::get('/accounting/finances/generate', 'AccountingController@generateFinancesInterfaz');
    Route::get('/accounting/finances/purchase/generate', 'AccountingController@generateFinancesPurchaseInterfaz');

    Route::get('/accounting.accountsreceivable', 'AccountingController@indexAccountsReceivable');
    Route::get('/accounting.accountspedingpurchase', 'AccountingController@indexAccountsPendingPurchase');

    Route::get('/accounting.products', 'AccountingController@products');
    Route::get('/accounting.products-dt', 'AccountingController@dt_products');
    Route::post('/accounting.products-store', 'AccountingController@store');

    Route::get('/accounting.configuration', 'AccountingController@configuration');
    Route::post('/accounting.configuration.store', 'AccountingController@configurationStore');

    Route::get('/accounting/receips-fees', 'AccountingController@indexReceipsFees');
    Route::get('/accounting/receips-fees/generate', 'AccountingController@generateReceipsFees');

    Route::get('/accounting/movimientos-bancos', 'BankMovementsController@index');
    Route::post('/accounting/movimientos-bancos/import', 'BankMovementsController@import')->name('bank-movements.import');
    Route::post('/accounting/movimientos-bancos/preview/generate', 'BankMovementsController@getData');
    Route::post('/accounting/movimientos-bancos/store', 'BankMovementsController@store');

    Route::post('/accounting/movimientos-bancos/getpayments', 'BankMovementsController@getPayments');
    Route::post('/accounting/movimientos-bancos/shopping/getpayments', 'BankMovementsController@getPaymentsShopping');

    Route::post('/home/report', 'HomeController@generateSaleMonth');
    Route::post('/home/income', 'HomeController@generateIncome');
    Route::post('/home/spending', 'HomeController@generateSpending');

    Route::post('/validate/user', 'ValidateUserController@validatePin');


    Route::post('/account-bank/update', 'AccountBankController@update')->name('updateAccountBank');


    Route::get('/enviar/resumen/{date}', 'CommercialController@enviarResumen');

    Route::get('/notifications', 'NotificationController@emailExpiredSales');
    Route::get('/notifications/overdue', 'NotificationController@OverdueSales');

    Route::get('/reporte-externo/{client}/catalogo', 'Logistic\CatalogController@externalReport');

    Route::get('/reporte/diario', 'Reports\DailyReportcontroller@index');
    Route::post('/reporte/diario/generate', 'Reports\DailyReportcontroller@generate');
    Route::get('/reporte/diario/excel', 'Reports\DailyReportcontroller@excel');

    Route::get('/reporte/stock-almacen', 'Reports\StockWarehouseReportcontroller@index');
    Route::post('/reporte/stock-almacen/generate', 'Reports\StockWarehouseReportcontroller@generate');
    Route::get('/reporte/stock-almacen/excel', 'Reports\StockWarehouseReportcontroller@excel');

    Route::get('/reporte/ingresos', 'Reports\IncomeReportcontroller@index');
    Route::post('/reporte/ingresos/generate', 'Reports\IncomeReportcontroller@generate');
    Route::get('/reporte/ingresos/excel', 'Reports\IncomeReportcontroller@excel');

    Route::get('/test/fix-kardex-costs','TestsController@fixKardexCost');
    Route::get('/test/generate-summary/{date}','CommercialController@sendSummary');

    Route::post('/commercial.sales/change-payment', 'Commercial\SalesController@changePayment');

Route::post('/commercial.sales.consult.cdr/{sale}/{type}/{client?}', 'CommercialController@consultaCDR');