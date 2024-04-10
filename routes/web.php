<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', 'App\Http\Controllers\HomeController@index');
Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home');
Route::get('/config', 'App\Http\Controllers\ConfigController@index')->name('config');
Route::put('/config/update/{id}', 'App\Http\Controllers\ConfigController@update')->name('config.update');

Route::group(['namespace' => 'App\Http\Controllers\Profile'], function (){
	Route::get('/profile', 'ProfileController@index')->name('profile');
	Route::put('/profile/update/profile/{id}', 'ProfileController@updateProfile')->name('profile.update.profile');
	Route::put('/profile/update/password/{id}', 'ProfileController@updatePassword')->name('profile.update.password');
	Route::put('/profile/update/avatar/{id}', 'ProfileController@updateAvatar')->name('profile.update.avatar');
});

Route::group(['namespace' => 'App\Http\Controllers\Error'], function (){
	Route::get('/unauthorized', 'ErrorController@unauthorized')->name('unauthorized');
});

Route::group(['namespace' => 'App\Http\Controllers'], function (){
	
	Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
	//Users
	Route::get('user', 'UserController@index')->name('user');
	Route::get('user/create', 'UserController@create')->name('user.create');
	Route::post('user/store', 'UserController@store')->name('user.store');
	Route::get('user/edit/{id}', 'UserController@edit')->name('user.edit');
	Route::put('user/update/{id}', 'UserController@update')->name('user.update');
	Route::get('user/edit/password/{id}', 'UserController@editPassword')->name('user.edit.password');
	Route::put('user/update/password/{id}', 'UserController@updatePassword')->name('user.update.password');
	Route::get('user/show/{id}', 'UserController@show')->name('user.show');
	Route::get('user/destroy/{id}', 'UserController@destroy')->name('user.destroy');
	// Roles
	Route::get('role', 'RoleController@index')->name('role');
	Route::get('role/create', 'RoleController@create')->name('role.create');
	Route::post('role/store', 'RoleController@store')->name('role.store');
	Route::get('role/edit/{id}', 'RoleController@edit')->name('role.edit');
	Route::put('role/update/{id}', 'RoleController@update')->name('role.update');
	Route::get('role/show/{id}', 'RoleController@show')->name('role.show');
	Route::get('role/destroy/{id}', 'RoleController@destroy')->name('role.destroy');


	Route::get('articulo', 'ArticuloController@index')->name('articulo');
	Route::get('articulo/create', 'ArticuloController@create')->name('articulo-create');
	Route::post('articulo', 'ArticuloController@store')->name('articulo.store');
	Route::get('articulo/{articulo}/pdf', 'ArticuloController@pdf')->name('articulo.pdf');
	Route::get('articulo/{articulo}', 'ArticuloController@show')->name('artiiculo-show');
	Route::get('articulo/{articulo}/edit', 'ArticuloController@edit')->name('articulo.edit');
	Route::put('articulo/{articulo}/update', 'ArticuloController@update')->name('articulo.update');





	Route::get('cliente', 'ClienteController@index')->name('cliente');
	Route::get('cliente/create', 'ClienteController@create')->name('cliente-create');
	Route::post('cliente', 'ClienteController@store')->name('cliente.store');
    Route::get('ajax/department', 'ClienteController@ajax_department')->name('ajax.get_deparment');


    Route::get('ajax/purchases_providers', 'ProveedorController@ajax_providers')->name('ajax.providers');
    Route::get('ajax/purchases_providers_details', 'ProveedorController@ajax_providers_purchases')->name('ajax.providers-purchases');
	Route::get('provider', 'ProveedorController@index')->name('provider');
	Route::get('provider/create', 'ProveedorController@create')->name('provider-create');
	Route::get('provider/{provider_id}/edit', 'ProveedorController@edit')->name('provider-create');
	Route::post('provider', 'ProveedorController@store')->name('provider.store');
	Route::put('provider/{provider_id}', 'ProveedorController@update')->name('provider.update');
	Route::get('provider-xls', 'ProveedorController@export_xls')->name('provider.export-xls');

	Route::get('raw-materials', 'RawMaterialsController@index')->name('raw-materials');
	Route::get('raw-materials/create', 'RawMaterialsController@create')->name('raw-materials-create');
	Route::get('raw-materials/{materiap}', 'RawMaterialsController@show')->name('raw-materials-show');
	Route::post('raw-materials', 'RawMaterialsController@store')->name('raw-materials.store');

    Route::get('brand', 'BrandController@index')->name('brand');
	Route::get('brand/create', 'BrandController@create')->name('brand-create');
	Route::post('brand', 'BrandController@store')->name('brand.store');
	Route::get('brand/{brands}/edit', 'BrandController@edit')->name('brand.edit');
	Route::put('brand/{brands}/update', 'BrandController@update')->name('brand.update');

	Route::get('production-stage', 'ProductionStageController@index')->name('production-stage');
	Route::get('production-stage/create', 'ProductionStageController@create')->name('production-stage-create');
	Route::post('production-stage', 'ProductionStageController@store')->name('production-stage.store');
	Route::get('production-stage/{stages}/edit', 'ProductionStageController@edit')->name('production-stage.edit');
	Route::put('production-stage/{stages}/update', 'ProductionStageController@update')->name('production-stage.update');

	Route::get('production-quality', 'ProductionQualityController@index')->name('production-quality');
	Route::get('production-quality/create', 'ProductionQualityController@create')->name('production-quality-create');
	Route::post('production-quality', 'ProductionQualityController@store')->name('production-quality.store');
	Route::get('production-quality/{qualitys}/edit', 'ProductionQualityController@edit')->name('production-quality.edit');
	Route::put('production-quality/{qualitys}/update', 'ProductionQualityController@update')->name('production-quality.update');

	Route::get('nationalities', 'NationalitiesController@index')->name('nationalities');
	Route::get('nationalities/create', 'NationalitiesController@create')->name('nationalities-create');
	Route::post('nationalities', 'NationalitiesController@store')->name('nationalities.store');


    Route::get('wish-purchase', 'WishPurchaseController@index')->name('wish-purchase');
	Route::get('wish-purchase/create', 'WishPurchaseController@create')->name('wish-purchase-create');
	Route::get('wish-purchase/{wish_purchase}', 'WishPurchaseController@show')->name('wish-purchase-create');
	Route::get('wish-purchase/{wish_purchase}/charge-purchase-budgets', 'WishPurchaseController@charge_purchase_budgets')->name('wish-purchases.charge-budgets');
    Route::post('wish-purchase-budgets/{wish_purchase}/charge-purchase-budgets', 'WishPurchaseController@charge_purchase_budgets_store')->name('wish-purchases.charge_purchase_budgets_store');
	Route::get('wish-purchase/{wish_purchase}/confirm-purchase-budgets', 'WishPurchaseController@confirm_purchase_budgets')->name('wish-purchases.charge-budgets');
    Route::get('wish-purchase-budgets/{purchase_budget}/confirm-purchase-budgets', 'WishPurchaseController@confirm_purchase_budgets_store')->name('wish-purchases.confirm_purchase_budgets_store');
    Route::get('wish-purchase-budgets/{wish_purchase}/wish-purchase-budgets-approved', 'WishPurchaseController@wish_purchase_budgets_approved')->name('wish-purchases.budgets_approved');
	Route::post('wish-purchase', 'WishPurchaseController@store')->name('wish-purchase.store');
	Route::get('wish-purchase/{wish_purchase}/edit', 'WishPurchaseController@edit')->name('wish-purchase.edit');
	Route::put('wish-purchase/{wish_purchase}/update', 'WishPurchaseController@update')->name('wish-purchase.update');
	Route::get('wish-purchase/{restocking}/pdf', 'WishPurchaseController@pdf')->name('wish-purchases.pdf');
	Route::get('show-multiple/wish-purchase', 'WishPurchaseController@show_multiple')->name('wish-purchases.show_multiple');
	Route::post('show-multiple/wish-purchase', 'WishPurchaseController@show_multiple_submit')->name('wish-purchases.show_multiple_submit');
	Route::get('show-multiple/transfer-create', 'WishPurchaseControllerController@transfer_create')->name('wish-purchases.transfer_create');



	Route::get('purchase-order', 'PurchaseOrderController@index')->name('purchase-order');
	Route::get('purchase-order/create', 'PurchaseOrderController@create')->name('purchase-order.create');
	Route::get('purchase-order/{purchase_order}', 'PurchaseOrderController@show')->name('purchase-order-create');
    Route::post('purchase-orders', 'PurchaseOrderController@store')->name('purchase-order.store');
    // Route::get('ajax/purchases_providers', 'ProveedorController@ajax_providers')->name('ajax.providers');
	Route::get('purchase-order/{purchase_order}/edit', 'PurchaseOrderController@edit')->name('purchase-order.edit');
	Route::put('purchase-order/{purchase_order}/update', 'PurchaseOrderController@update')->name('purchase-order.update');

	Route::get('purchase-movement', 'PurchaseMovementsController@index')->name('purchase-movement');
	Route::get('purchase-movement/create', 'PurchaseMovementsController@create')->name('purchase-movement-create');
	Route::get('purchase-movement/{purchase_movement}', 'PurchaseMovementsController@show')->name('purchase-movement-show');
	Route::post('purchase-movement', 'PurchaseMovementsController@store')->name('purchase-movement-store');
	Route::get('ajax/purchases-movements', 'PurchaseMovementsController@ajax_purchases_movements')->name('ajax.purchases-movements');
	Route::get('ajax/purchases-products-movements', 'PurchaseMovementsController@ajax_purchases_products_movements')->name('ajax.purchases-products-movements');
	Route::get('search/provider-stamped', 'WishPurchaseController@searchProviderStamped')->name('provider-stamped.search');
    Route::get('ajax/purchases_products_last', 'ArticuloController@ajax_purchases_last')->name('ajax.products-purchases-last');
	Route::get('purchase-movement/{purchase_movement}/edit', 'PurchaseMovementsController@edit')->name('purchase-movement.edit');
	Route::put('purchase-movement/{purchase_movement}/update', 'PurchaseMovementsController@update')->name('purchase-movement.update');

	Route::get('purchase', 'PurchaseController@index')->name('purchase');
	Route::get('purchase/create', 'PurchaseController@create')->name('purchase-create');
	Route::get('purchase/{purchase}', 'PurchaseController@show')->name('purchase-show');
	Route::post('purchase', 'PurchaseController@store')->name('purchase.store');
	Route::get('purchase/{purchase}/pdf', 'PurchaseController@pdf')->name('purchases.pdf');
	Route::get('purchase/{purchase}/edit', 'PurchaseController@edit')->name('purchase.edit');
	Route::put('purchase/{purchase}/update', 'PurchaseController@update')->name('purchase.update');

    Route::get('ajax/purchases_products_orders', 'RawMaterialsController@ajax_purchases_orders')->name('ajax.products-purchases-orders');
	Route::get('ajax/raw-material', 'RawMaterialsController@ajax_purchases_products')->name('ajax.purchases-products');
	Route::get('ajax/purchases/note-credits', 'PurchaseController@ajax_purchases_note_credits')->name('ajax.invoices-purchases');

	Route::get('inventories', 'PurchasesProductInventoriesController@index')->name('inventories');
    Route::get('inventories/create', 'PurchasesProductInventoriesController@create')->name('inventories.create');
    Route::post('inventories', 'PurchasesProductInventoriesController@store')->name('inventories.store');
    Route::get('inventories/{purchases_product_inventory}', 'PurchasesProductInventoriesController@show')->name('inventories.show');
    Route::get('inventories/{purchases_product_inventory}/pdf', 'PurchasesProductInventoriesController@pdf')->name('inventories.pdf');
	Route::get('inventories/{purchases_product_inventory}/confirm-inventory', 'PurchasesProductInventoriesController@confirm_inventory')->name('inventories.confirm-inventory');
	Route::get('reports/stock-product-purchases', 'ReportsController@stock_product_purchases_report')->name('reports.stock-product-purchases');
	Route::get('reports/stock-product-purchases-xls', 'ReportsController@stock_product_purchases_report_excel')->name('reports.stock-product-purchases-xls');
	Route::get('reports/purchases_report', 'ReportsController@purchases_report')->name('reports.purchases_report');
	Route::get('reports/purchases_report/pdf', 'ReportsController@purchases_report_pdf')->name('reports.purchases_report.pdf');

	Route::get('wish-production', 'WishProductionController@index')->name('wish-production');
	Route::get('wish-production/create', 'WishProductionController@create')->name('wish-production.create');
	Route::get('wish-production/{wish_production}', 'WishProductionController@show')->name('wish-production-show');
	Route::post('wish-production', 'WishProductionController@store')->name('wish-production.store');
    Route::get('ajax/clients', 'ClienteController@ajax_clients')->name('ajax.clients');
	Route::get('wish-production/{wish_production}/edit', 'WishProductionController@edit')->name('wish-production.edit');
	Route::put('wish-production/{wish_production}/update', 'WishProductionController@update')->name('wish-production.update');

	Route::get('budget-production', 'BudgetProductionController@index')->name('budget-production');
	Route::get('budget-production/create', 'BudgetProductionController@create')->name('budget-production-create');
	Route::get('budget-production/{budget_production}', 'BudgetProductionController@show')->name('budget-production-show');
	Route::post('budget-production', 'BudgetProductionController@store')->name('budget-production-store');
	Route::get('ajax/budget-production', 'BudgetProductionController@ajax_budget_production')->name('ajax.budget-production');
	Route::get('ajax/articulo', 'ArticuloController@ajax_articulo')->name('ajax.articulo');
	Route::get('budget-production/{budget_production}/edit', 'BudgetProductionController@edit')->name('budget-production.edit');
	Route::put('budget-production/{budget_production}/update', 'BudgetProductionController@update')->name('budget-production.update');

	Route::get('production-order', 'ProductionOrderController@index')->name('production-order');
	Route::get('production-order/create', 'ProductionOrderController@create')->name('production-order-create');
	Route::get('production-order/{production_order}', 'ProductionOrderController@show')->name('production-order.show');
	Route::post('production-order', 'ProductionOrderController@store')->name('production-order-store');
	Route::get('ajax/order-production', 'ProductionOrderController@ajax_order_production')->name('ajax.order-production');
	Route::get('ajax/modal-material', 'ProductionOrderController@ajax_modal_material')->name('ajax.modal-material');
	Route::get('production-order/{production_order}/edit', 'ProductionOrderController@edit')->name('production-order.edit');
	Route::put('production-order/{production_order}/update', 'ProductionOrderController@update')->name('production-order.update');

	Route::get('production-control', 'ProductionControlController@index')->name('production-control');
	Route::get('production-control/create', 'ProductionControlController@create')->name('production-control-create');
	Route::get('production-control/{control}', 'ProductionControlController@show')->name('production-control-show');
	Route::post('production-control', 'ProductionControlController@store')->name('production-control-store');
	Route::get('ajax/control-production', 'ProductionControlController@ajax_control_production')->name('ajax.control-production');
	Route::get('ajax/update-control', 'ProductionControlController@ajax_update_control')->name('ajax.update-control');

	
	Route::get('production-control-quality', 'ProductionControlQualityController@index')->name('production-control-quality');
	Route::get('production-control-quality/create', 'ProductionControlQualityController@create')->name('production-control-quality-create');
	Route::get('production-control-quality/{control}', 'ProductionControlQualityController@show')->name('production-control-quality-show');
	Route::post('production-control-quality', 'ProductionControlQualityController@store')->name('production-control-quality-store');
	Route::get('ajax/control-calidad', 'ProductionControlQualityController@ajax_control_calidad')->name('ajax.control-calidad');
	Route::get('ajax/update-control', 'ProductionControlQualityController@ajax_update_control')->name('ajax.update-control');

	Route::get('losses', 'LossesController@index')->name('losses');
	Route::get('losses/create', 'LossesController@create')->name('losses-create');
	Route::get('losses/{losses}', 'LossesController@show')->name('losses-show');
	Route::post('losses', 'LossesController@store')->name('losses-store');
	// Route::get('ajax/control-calidad', 'LossesController@ajax_control_calidad')->name('ajax.control-calidad');
	Route::get('ajax/update-control', 'LossesController@ajax_update_control')->name('ajax.update-control');

	Route::get('production-cost', 'ProductionCostController@index')->name('production-cost');
	Route::get('production-cost/{production_cost}', 'ProductionCostController@show')->name('production-cost-show');
	
	Route::get('calendar', 'CalendarController@index')->name('calendar');
	Route::get('calendar/create', 'CalendarController@create')->name('calendar.create');
	Route::get('ajax/calendar', 'CalendarController@ajax_calendar')->name('ajax.calendar-events');
	Route::post('calendar-store', 'CalendarController@store')->name('calendar.store');

	Route::get('doctors-schedule', 'DoctorScheduleController@index')->name('doctors-schedule.index');
    Route::post('doctors-schedule', 'DoctorScheduleController@store')->name('doctors-schedule.store');
    Route::get('doctors-schedule/create', 'DoctorScheduleController@create')->name('doctors-schedule.create');
    Route::get('doctors-schedule/{doctor_schedule}/edit', 'DoctorScheduleController@edit')->name('doctors-schedule.edit');
    Route::put('doctors-schedule/{doctor_schedule}', 'DoctorScheduleController@update')->name('doctors-schedule.update');
    Route::get('doctors-schedule/xlsx', 'DoctorScheduleController@xlsx')->name('doctors-schedule.xlsx');

	Route::get('opportunity', 'OpportunityController@index')->name('opportunity.index');
	Route::get('opportunity/create', 'OpportunityController@create')->name('opportunity.create');
	Route::post('opportunity', 'OpportunityController@store')->name('opportunity.store');
	Route::get('opportunity/{opportunity}', 'OpportunityController@show')->name('opportunity.show');


	Route::get('opportunity-seller', 'OpportunitySellerController@index')->name('opportunity-seller.index');
    Route::get('opportunity-seller/create', 'OpportunitySellerController@create')->name('opportunity-seller.create');
    Route::post('opportunity-seller', 'OpportunitySellerController@store')->name('opportunity-seller.store');
	Route::post('opportunity-trackings/{opportunity}', 'OpportunityController@tracking_store')->name('opportunity-trackings.store');


});
