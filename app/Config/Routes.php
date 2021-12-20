<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('ClientController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'ClientController::index');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

/*
 * Myth:Auth routes file.
 */
$routes->group('', function ($routes) {
    // Login/out
    $routes->get('login', 'AuthController::login', ['as' => 'login']);
    $routes->post('login', 'AuthController::attemptLogin');
    $routes->get('logout', 'AuthController::logout');

    // Registration
    $routes->get('register', 'AuthController::register', ['as' => 'register']);
    $routes->post('register', 'AuthController::attemptRegister');

    // Activation
    $routes->get('activate-account', 'AuthController::activateAccount', ['as' => 'activate-account']);
    $routes->get('resend-activate-account', 'AuthController::resendActivateAccount', ['as' => 'resend-activate-account']);

    // Forgot/Resets
    $routes->get('forgot', 'AuthController::forgotPassword', ['as' => 'forgot']);
    $routes->post('forgot', 'AuthController::attemptForgot');
    $routes->get('reset-password', 'AuthController::resetPassword', ['as' => 'reset-password']);
    $routes->post('reset-password', 'AuthController::attemptReset');

    $routes->get('reset-email-has-been-sent', 'AuthController::resetSent');
});

$routes->get('admin/onboarding', 'AdminController::onboarding', ['filter' => 'login']);
$routes->post('admin/onboarding-setup', 'AdminController::onboardingSetup', ['filter' => 'login']);

$routes->group('admin', ['filter' => 'admin'], function($routes) {
    $routes->get('', 'AdminController::home');
    $routes->get('products', 'AdminController::products');
    $routes->get('categories', 'AdminController::categories');
    $routes->get('settings', 'AdminController::settings');
    $routes->get('help', 'AdminController::help');
    
    $routes->get('add-product', 'AdminController::addProduct');
    $routes->post('add-product', 'AdminController::attemptAddProduct');
    
    // $routes->put('products/(:num)', 'AdminController::updateProduct/$1', ['as' => 'update-stock']);
    $routes->get('products/search', 'AdminController::findProducts', ['as' => 'search-products']);
    $routes->put('products/(:num)', 'AdminController::updateProduct/$1', ['as' => 'update-product']);
    $routes->post('products/update-photos', 'AdminController::updatePhotos', ['as' => 'update-photos']);
    $routes->delete('products/(:num)', 'AdminController::deleteProduct/$1', ['as' => 'delete-product']);
    $routes->delete('products', 'AdminController::deleteProducts', ['as' => 'products-bulk-delete']);
    
    $routes->get('add-category', 'AdminController::addCategory');
    $routes->post('add-category', 'AdminController::attemptAddCategory');
    
    $routes->get('categories/search', 'AdminController::findCategories', ['as' => 'search-categories']);
    $routes->delete('categories/(:num)', 'AdminController::deleteCategory/$1', ['as' => 'delete-category']);
    $routes->delete('categories', 'AdminController::deleteCategories', ['as' => 'categories-bulk-delete']);

    $routes->put('settings/store-information', 'AdminController::updateStore', ['as' => 'update-store']);
    $routes->put('settings/message-template', 'AdminController::updateStore', ['as' => 'update-message']);
    $routes->put('settings/social-profiles', 'AdminController::updateStore', ['as' => 'update-profiles']);
});

$routes->get('photos/(:alpha)/(:segment)', 'ContentController::photos/$1/$2', ['as' => 'content-photos']);

$routes->get('product/(:segment)', 'ClientController::product/$1', ['as' => 'product-page']);
$routes->get('category/(:segment)', 'ClientController::category/$1', ['as' => 'category-page']);