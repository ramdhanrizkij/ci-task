<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', function(){
    return redirect()->to('/task');
});

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
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}


$routes->get('update-status/{id}','TaskController::updateStatus');
$routes->group('task', function($routes){
    $routes->get('(:num)/update-status', 'TaskController::updateStatus/$1');
    $routes->get('/', 'TaskController::index', ['as' => 'index']);
    $routes->get('(:num)', 'TaskController::getById/$1', ['as' => 'task.getbyid']);
    $routes->put('(:num)', 'TaskController::update/$1', ['as' => 'task.update']);
    $routes->post('/','TaskController::create',['as'=>'task.create']);
    $routes->delete('(:num)','TaskController::delete/$1',['as'=>'task.delete']);
    $routes->post('datatable','TaskController::datatable',['as'=>'datatable']);
});