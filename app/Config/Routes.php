<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

/* ==============================================
   🔹 AUTH ROUTES
============================================== */
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');
$routes->get('signup', 'Auth::signup');
$routes->post('signup', 'Auth::attemptSignup');

/* ==============================================
   🔹 DASHBOARD (All Logged-in Users)
============================================== */
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);
$routes->get('dashboard/getServerTime', 'Dashboard::getServerTime');

/* ==============================================
   🔹 ADMIN MANAGEMENT
============================================== */
$routes->group('admin', ['filter' => 'authrole:admin'], function ($routes) {
    $routes->get('users', 'Admin::users');
    $routes->post('users/approve/(:num)', 'Admin::approve/$1');
    $routes->post('users/reject/(:num)', 'Admin::reject/$1');
    $routes->post('users/role/(:num)', 'Admin::changeRole/$1');

    // 🔹 Admin Tools
    $routes->get('dashboard', 'Admin::dashboard');
    $routes->get('activitylog', 'ActivityLog::index');
});

/* ==============================================
   🔹 GRN MODULE (Admin + Warehouse)
============================================== */
$routes->group('grn', ['filter' => 'authrole:admin,warehouse'], function ($routes) {
    $routes->get('/', 'Grn::index');
    $routes->get('list', 'Grn::list');
    $routes->get('form', 'Grn::form');
    $routes->get('form/(:num)', 'Grn::form/$1');
    $routes->post('save', 'Grn::save');
    $routes->post('save/(:num)', 'Grn::save/$1');
    $routes->get('view/(:num)', 'Grn::view/$1');
    $routes->get('info/(:num)', 'PurchaseOrders::info/$1');

});

/* ==============================================
   🔹 QC MODULE (Admin + QC)
============================================== */
$routes->group('qc', ['filter' => 'authrole:admin,qc'], function ($routes) {
    $routes->get('/', 'QcResults::grnList');
    $routes->get('dashboard', 'QcResults::dashboard');
    $routes->get('view/(:num)', 'QcResults::view/$1');
    $routes->get('test/(:num)', 'QcResults::test/$1');
    $routes->get('edit/(:num)', 'QcResults::edit/$1');

    $routes->post('updateQc', 'QcResults::updateQc');             
    $routes->post('updateQcAjax', 'QcResults::updateQcAjax');     
    $routes->post('updateSingle/(:num)', 'QcResults::updateSingle/$1');
});


/* ==============================================
   🔹 SUPPLIERS (Admin)
============================================== */
$routes->group('suppliers', ['filter' => 'authrole:admin'], function ($routes) {
    $routes->get('/', 'Supplier::index');
    $routes->get('list', 'Supplier::list');
    $routes->get('form', 'Supplier::form');
    $routes->get('form/(:num)', 'Supplier::form/$1');
    $routes->post('save', 'Supplier::save');
    $routes->post('save_ajax', 'Supplier::save_ajax');
    $routes->post('save/(:num)', 'Supplier::save/$1');
    $routes->get('view/(:num)', 'Supplier::view/$1');
    $routes->get('delete/(:num)', 'Supplier::delete/$1');
});

/* ==============================================
   🔹 ITEMS (Admin)
============================================== */
$routes->group('items', ['filter' => 'authrole:admin'], function ($routes) {
    $routes->get('/', 'Items::index');
    $routes->get('list', 'Items::list');
    $routes->get('form', 'Items::form');
    $routes->get('form/(:num)', 'Items::form/$1');
    $routes->post('save', 'Items::save');
    $routes->post('save_ajax', 'Items::save_ajax');
    $routes->get('view/(:num)', 'Items::view/$1');
    $routes->get('delete/(:num)', 'Items::delete/$1');
});
$routes->get('items/info/(:num)', 'Items::info/$1');
$routes->get('items/bySupplier/(:num)', 'Items::getBySupplier/$1');

/* ==============================================
   🔹 UNITS (Admin)
============================================== */
$routes->group('units', ['filter' => 'authrole:admin'], function ($routes) {
    $routes->get('/', 'Units::index');
    $routes->get('list', 'Units::list');
    $routes->get('form', 'Units::form');
    $routes->get('form/(:num)', 'Units::form/$1');
    $routes->post('save', 'Units::save');
    $routes->post('save_ajax', 'Units::save_ajax');
    $routes->get('view/(:num)', 'Units::view/$1');
    $routes->get('delete/(:num)', 'Units::delete/$1');
});

/* ==============================================
   🔹 DEPARTMENTS (Admin)
============================================== */
$routes->group('departments', ['filter' => 'authrole:admin'], function ($routes) {
    $routes->get('/', 'Departments::index');
    $routes->get('list', 'Departments::list');
    $routes->get('form', 'Departments::form');
    $routes->get('form/(:num)', 'Departments::form/$1');
    $routes->post('save', 'Departments::save');
    $routes->get('view/(:num)', 'Departments::view/$1');
    $routes->get('delete/(:num)', 'Departments::delete/$1');
});

/* ==============================================
   🔹 WAREHOUSE MODULE (Merged Store + Warehouse)
============================================== */
$routes->group('warehouse', ['filter' => 'authrole:admin,warehouse'], function ($routes) {
    $routes->get('/', 'Warehouse::index');
    $routes->get('dashboard', 'Warehouse::dashboard');
    $routes->get('accept/(:num)', 'Warehouse::accept/$1');
    $routes->get('reject/(:num)', 'Warehouse::reject/$1');
    $routes->get('view/(:num)', 'Warehouse::view/$1');
    $routes->post('addMovement', 'Warehouse::addMovement');
});

/* ==============================================
   🔹 WAREHOUSE ISSUE (ex-Store Issue)
============================================== */
$routes->group('warehouseissue', ['filter' => 'authrole:admin,warehouse'], function ($routes) {
    $routes->get('/', 'WarehouseIssue::index');
    $routes->get('issue/(:num)', 'WarehouseIssue::issue/$1');
    $routes->post('saveIssue', 'WarehouseIssue::saveIssue');
    $routes->get('movements', 'WarehouseIssue::movements');
});

/* ==============================================
   🔹 PRODUCTION MODULE
============================================== */
$routes->group('production', ['filter' => 'authrole:admin,production'], function ($routes) {
    $routes->get('/', 'Production::index');
    $routes->get('dashboard', 'Production::dashboard');
    $routes->get('form', 'Production::form');
    $routes->get('form/(:num)', 'Production::form/$1');
    $routes->post('save', 'Production::save');
    $routes->get('view/(:num)', 'Production::view/$1');
});

/* ==============================================
   🔹 MRS MODULE (Production Requests)
============================================== */
$routes->group('mrs', ['filter' => 'authrole:admin,production'], function ($routes) {
    $routes->get('/', 'Mrs::index');
    $routes->get('list', 'Mrs::list');
    $routes->get('form', 'Mrs::form');
    $routes->get('form/(:num)', 'Mrs::form/$1');
    $routes->post('save', 'Mrs::save');
    $routes->get('view/(:num)', 'Mrs::view/$1');
    $routes->post('approve/(:num)', 'Mrs::approve/$1');
    $routes->post('reject/(:num)', 'Mrs::reject/$1');
});

/* ==============================================
   🔹 PROCUREMENT (PURCHASE ORDERS)
============================================== */
$routes->group('purchaseorders', ['filter' => 'authrole:admin,procurement,warehouse'], function ($routes) {
    // ✅ Main CRUD
    $routes->get('/', 'PurchaseOrders::index');
    $routes->get('list', 'PurchaseOrders::list');
    $routes->get('form', 'PurchaseOrders::form');
    $routes->get('form/(:num)', 'PurchaseOrders::form/$1');
    $routes->post('store', 'PurchaseOrders::store');
    $routes->get('view/(:num)', 'PurchaseOrders::view/$1');
    $routes->get('delete/(:num)', 'PurchaseOrders::delete/$1');

    // ✅ Status Actions
    $routes->post('approve/(:num)', 'PurchaseOrders::approve/$1');
    $routes->post('cancel/(:num)', 'PurchaseOrders::cancel/$1');

    // ✅ Dashboard + Info
    $routes->get('stats', 'PurchaseOrders::stats');
    $routes->get('info/(:num)', 'PurchaseOrders::info/$1');

    // ✅ AJAX: Inline modal saving
    $routes->post('saveSupplierAjax', 'PurchaseOrders::saveSupplierAjax');
    $routes->post('saveItemAjax', 'PurchaseOrders::saveItemAjax');
    $routes->post('saveUnitAjax', 'PurchaseOrders::saveUnitAjax');

    // ✅ AJAX: Select2 + Item/Supplier autofill
    $routes->get('searchItemsAjax', 'PurchaseOrders::searchItemsAjax');   // ✅ Works already
    $routes->get('searchSuppliersAjax', 'PurchaseOrders::searchSuppliersAjax'); // ✅ Corrected
    $routes->post('getItemInfo', 'PurchaseOrders::getItemInfo');
    $routes->post('getItemByCode', 'PurchaseOrders::getItemByCode');

});



/* ==============================================
   🔹 ROLE DASHBOARDS
============================================== */
$routes->get('admin/dashboard', 'Admin::dashboard');
$routes->get('warehouse/dashboard', 'Warehouse::dashboard');
$routes->get('qc/dashboard', 'QcResults::dashboard');
$routes->get('production/dashboard', 'Production::dashboard');
$routes->get('procurement/dashboard', 'Procurement::dashboard');

/* ==============================================
   🔹 QUICK SHORTCUTS
============================================== */
$routes->get('purchase/create', 'PurchaseOrders::form');
$routes->post('purchase/store', 'PurchaseOrders::store');
$routes->get('suppliers/add', 'Supplier::form');
$routes->post('suppliers/save', 'Supplier::save');
$routes->get('mrs/create', 'Mrs::form');
$routes->post('mrs/save', 'Mrs::save');

/* ==============================================
   🔹 PROFILE & MISC
============================================== */
$routes->get('profile', 'Profile::index');
$routes->post('profile/update', 'Profile::update');

$routes->get('test/time', function() {
    $db = \Config\Database::connect();
    $result = $db->query("SELECT NOW() AS mysql_time")->getRow();
    echo "<b>PHP timezone:</b> " . date_default_timezone_get() . "<br>";
    echo "<b>PHP Time:</b> " . date('Y-m-d H:i:s') . "<br>";
    echo "<b>MySQL Time:</b> " . $result->mysql_time;
});

/* ==============================================
   🔹 ACCESS DENIED PAGE
============================================== */
$routes->get('access-denied', function() {
    echo view('errors/custom/access_denied');
});

$routes->get('/admin/check-signups', 'Admin::checkSignups');

$routes->get('dashboard/warehouse', 'Dashboard::index', ['filter' => 'authrole:admin,warehouse']);


$routes->group('arn', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'Arn::index');             // redirect to list
    $routes->get('list', 'Arn::list');           // main list
    $routes->get('create', 'Arn::create');
    $routes->get('edit/(:num)', 'Arn::edit/$1');
    $routes->post('save', 'Arn::save');
    $routes->get('view/(:num)', 'Arn::view/$1');
    $routes->get('delete/(:num)', 'Arn::delete/$1');

    // 🧩 NEW: Bulk AJAX ARN creation (used by form auto mode)
    $routes->post('save_ajax', 'Arn::save_ajax');
});

/* ==============================================
   🔹 GRN CATEGORY (Admin)
============================================== */
$routes->group('grn-category', ['filter' => 'authrole:admin'], function ($routes) {
    $routes->get('/', 'GrnCategory::index');
    $routes->get('list', 'GrnCategory::list');
    $routes->get('form', 'GrnCategory::form');
    $routes->get('form/(:num)', 'GrnCategory::form/$1');
    $routes->post('save', 'GrnCategory::save');
    $routes->post('save/(:num)', 'GrnCategory::save/$1');
    $routes->get('view/(:num)', 'GrnCategory::view/$1');
    $routes->get('delete/(:num)', 'GrnCategory::delete/$1');
});

/* ==============================================
   🔹 MANUFACTURER MASTER (Admin)
============================================== */
$routes->group('manufacturers', ['filter' => 'authrole:admin'], function ($routes) {
    $routes->get('/', 'Manufacturer::index');
    $routes->get('list', 'Manufacturer::list');
    $routes->get('form', 'Manufacturer::form');
    $routes->get('form/(:num)', 'Manufacturer::form/$1');
    $routes->post('save', 'Manufacturer::save');
    $routes->post('save/(:num)', 'Manufacturer::save/$1');
    $routes->get('view/(:num)', 'Manufacturer::view/$1');
    $routes->get('delete/(:num)', 'Manufacturer::delete/$1');
});

/* ==============================================
   🔹 MANUFACTURER TERMS MASTER (Admin)
============================================== */
$routes->group('manufacturer-terms', ['filter' => 'authrole:admin'], function ($routes) {
    $routes->get('/', 'ManufacturerTerms::index');
    $routes->get('list', 'ManufacturerTerms::list');
    $routes->get('form', 'ManufacturerTerms::form');
    $routes->get('form/(:num)', 'ManufacturerTerms::form/$1');
    $routes->post('save', 'ManufacturerTerms::save');
    $routes->post('save/(:num)', 'ManufacturerTerms::save/$1');
    $routes->get('view/(:num)', 'ManufacturerTerms::view/$1');
    $routes->get('delete/(:num)', 'ManufacturerTerms::delete/$1');
});

/* ==============================================
   🔹 QC PARAMETERS MASTER (Admin)
============================================== */
$routes->group('qc-parameters', ['filter' => 'authrole:admin'], function ($routes) {
    $routes->get('/', 'QcParameters::index');
    $routes->get('list', 'QcParameters::list');
    $routes->get('form', 'QcParameters::form');
    $routes->get('form/(:num)', 'QcParameters::form/$1');
    $routes->post('save', 'QcParameters::save');
    $routes->post('save/(:num)', 'QcParameters::save/$1');
    $routes->get('view/(:num)', 'QcParameters::view/$1');
    $routes->get('delete/(:num)', 'QcParameters::delete/$1');
});

/* ==============================================
   🔹 REJECTION REASONS MASTER (Admin)
============================================== */
$routes->group('rejection-reasons', ['filter' => 'authrole:admin'], function ($routes) {
    $routes->get('/', 'RejectionReason::index');
    $routes->get('list', 'RejectionReason::list');
    $routes->get('form', 'RejectionReason::form');
    $routes->get('form/(:num)', 'RejectionReason::form/$1');
    $routes->post('save', 'RejectionReason::save');
    $routes->post('save/(:num)', 'RejectionReason::save/$1');
    $routes->get('view/(:num)', 'RejectionReason::view/$1');
    $routes->get('delete/(:num)', 'RejectionReason::delete/$1');
});

/* ==============================================
   🔹 RETURN REASONS MASTER (Admin)
============================================== */
$routes->group('return-reasons', ['filter' => 'authrole:admin'], function ($routes) {
    $routes->get('/', 'ReturnReason::index');
    $routes->get('list', 'ReturnReason::list');
    $routes->get('form', 'ReturnReason::form');
    $routes->get('form/(:num)', 'ReturnReason::form/$1');
    $routes->post('save', 'ReturnReason::save');
    $routes->post('save/(:num)', 'ReturnReason::save/$1');
    $routes->get('view/(:num)', 'ReturnReason::view/$1');
    $routes->get('delete/(:num)', 'ReturnReason::delete/$1');
});

/* ==============================================
   🔹 SHELF LIFE MASTER (Admin)
============================================== */
$routes->group('shelf-life', ['filter' => 'authrole:admin'], function ($routes) {
    $routes->get('/', 'ShelfLife::index');
    $routes->get('list', 'ShelfLife::list');
    $routes->get('form', 'ShelfLife::form');
    $routes->get('form/(:num)', 'ShelfLife::form/$1');
    $routes->post('save', 'ShelfLife::save');
    $routes->post('save/(:num)', 'ShelfLife::save/$1');
    $routes->get('view/(:num)', 'ShelfLife::view/$1');
    $routes->get('delete/(:num)', 'ShelfLife::delete/$1');
});

/* ==============================================
   🔹 BATCH SERIES MASTER (Admin)
============================================== */
$routes->group('batch-series', ['filter' => 'authrole:admin'], function ($routes) {
    $routes->get('/', 'BatchSeries::index');
    $routes->get('list', 'BatchSeries::list');
    $routes->get('form', 'BatchSeries::form');
    $routes->get('form/(:num)', 'BatchSeries::form/$1');
    $routes->post('save', 'BatchSeries::save');
    $routes->post('save/(:num)', 'BatchSeries::save/$1');
    $routes->get('view/(:num)', 'BatchSeries::view/$1');
    $routes->get('delete/(:num)', 'BatchSeries::delete/$1');
});

/* ==============================================
   🔹 STORAGE CONDITIONS MASTER (Admin)
============================================== */
$routes->group('storage-conditions', ['filter' => 'authrole:admin'], function ($routes) {
    $routes->get('/', 'StorageCondition::index');
    $routes->get('list', 'StorageCondition::list');
    $routes->get('form', 'StorageCondition::form');
    $routes->get('form/(:num)', 'StorageCondition::form/$1');
    $routes->post('save', 'StorageCondition::save');
    $routes->post('save/(:num)', 'StorageCondition::save/$1');
    $routes->get('view/(:num)', 'StorageCondition::view/$1');
    $routes->get('delete/(:num)', 'StorageCondition::delete/$1');
});

/* ==============================================
   🔹 LOCATIONS MODULE (Admin)
============================================== */
$routes->group('locations', ['filter' => 'authrole:admin'], function ($routes) {

    // redirect /locations → /locations/list
    $routes->get('/', 'Locations::index');

    // list
    $routes->get('list', 'Locations::list');

    // add / edit form
    $routes->get('form', 'Locations::form');
    $routes->get('form/(:num)', 'Locations::form/$1');

    // save (create + update)
    $routes->post('save', 'Locations::save');
    $routes->post('save/(:num)', 'Locations::save/$1');

    // view
    $routes->get('view/(:num)', 'Locations::view/$1');

    // delete
    $routes->get('delete/(:num)', 'Locations::delete/$1');
});

$routes->group('locations', function($routes) {
    $routes->get('/', 'Locations::index');
    $routes->get('list', 'Locations::list');
    $routes->get('add', 'Locations::form');
    $routes->get('edit/(:num)', 'Locations::form/$1');
    $routes->post('save', 'Locations::save');
    $routes->post('save/(:num)', 'Locations::save/$1');
    $routes->get('view/(:num)', 'Locations::view/$1');
    $routes->get('delete/(:num)', 'Locations::delete/$1');
});

$routes->group('storage-locations', function($routes) {
    $routes->get('/', 'StorageLocations::index');
    $routes->get('list', 'StorageLocations::list');
    $routes->get('add', 'StorageLocations::form');
    $routes->get('edit/(:num)', 'StorageLocations::form/$1');
    $routes->post('save', 'StorageLocations::save');
    $routes->post('save/(:num)', 'StorageLocations::save/$1');
    $routes->get('delete/(:num)', 'StorageLocations::delete/$1');
});

$routes->group('storage_conditions', function($routes) {
    $routes->get('/', 'StorageConditions::index');
    $routes->get('list', 'StorageConditions::list');
    $routes->get('add', 'StorageConditions::form');
    $routes->get('edit/(:num)', 'StorageConditions::form/$1');
    $routes->get('view/(:num)', 'StorageConditions::view/$1');
    $routes->post('save', 'StorageConditions::save');
    $routes->post('save/(:num)', 'StorageConditions::save/$1');
    $routes->get('delete/(:num)', 'StorageConditions::delete/$1');
});
