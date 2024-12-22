<?php
session_start();

// Autoload composer
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Helpers/calculateRiskLevel.php';

// Buat router (App\Router)
$router = new \App\Router();

// Definisikan route
$router->get('/', function() {
    if (isset($_SESSION['user_id'])) {
        // Sudah login -> redirect ke /dashboard
        header('Location: '.$_SESSION['base_uri'].'/dashboard');
        exit;
    } else {
        // Belum login -> redirect ke /login
        header('Location: '.$_SESSION['base_uri'].'/login');
        exit;
    }
});

$router->get('/dashboard', function() {
    // jalankan AuthMiddleware
    (new \App\Http\Middleware\AuthMiddleware())->handle();
 
    // kalau lolos, lanjut ke DashboardController
    (new \App\Http\Controllers\DashboardController())->index();
 });
 

$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@processLogin');
$router->get('/logout', 'AuthController@logout');

// Dashboard
$router->get('/dashboard', 'DashboardController@index');

// Risk Register
$router->get('/risk-register', 'RiskRegisterController@index');
$router->get('/risk-register/create', 'RiskRegisterController@create');
$router->post('/risk-register/store', 'RiskRegisterController@store');
$router->get('/risk-register/view', 'RiskRegisterController@view');
$router->get('/risk-register/edit', 'RiskRegisterController@edit');
$router->post('/risk-register/update', 'RiskRegisterController@update');
$router->get('/risk-register/delete', 'RiskRegisterController@delete');

// Treatments
$router->get('/treatments', 'TreatmentsController@index');
$router->get('/treatments/create', 'TreatmentsController@create'); 
$router->post('/treatments/store', 'TreatmentsController@store'); 
$router->get('/treatments/edit', 'TreatmentsController@edit'); 
$router->post('/treatments/update', 'TreatmentsController@update'); 
$router->post('/treatments/delete', 'TreatmentsController@delete'); 

// Reports
$router->get('/reports', 'ReportsController@index');
$router->get('/reports/detail', 'ReportsController@detail');
$router->get('/reports/export', 'ReportsController@export');

// Users
$router->get('/users', 'UsersController@index');
$router->get('/users/create', 'UsersController@create');
$router->post('/users/store', 'UsersController@store');
$router->get('/users/edit', 'UsersController@edit');
$router->post('/users/update', 'UsersController@update');
$router->get('/users/delete', 'UsersController@delete');

// Profile
$router->get('/profile', 'ProfileController@index');
$router->get('/profile/edit', 'ProfileController@edit');
$router->post('/profile/update', 'ProfileController@update');

// === Hitung baseUri secara dinamis ===
$scriptName = $_SERVER['SCRIPT_NAME'];                // e.g. /bbp/public/index.php
$scriptDir  = str_replace("/index.php", "", $scriptName); // e.g. /bbp/public

// Simpan ke session (atau define global)
$_SESSION['base_uri'] = $scriptDir; // e.g. "/bbp/public" atau "/myapp/public"

// Tangkap request PATH
$requestUri = $_SERVER['REQUEST_URI']; // e.g. /bbp/public/login
$method     = $_SERVER['REQUEST_METHOD'];

// Hilangkan query string
if (strpos($requestUri, '?') !== false) {
    $requestUri = explode('?', $requestUri)[0];
}

// Hilangkan $scriptDir
$path = str_replace($scriptDir, '', $requestUri);

// Kalau kosong jadikan "/"
if ($path === '') {
    $path = '/';
}

// Dispatch
$router->dispatch($method, $path);
