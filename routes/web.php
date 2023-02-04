<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    /*return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);*/
    return redirect(route('login'));
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/profiles', function () {
    return Inertia::render('Profiles');
})->middleware(['auth', 'verified'])->name('profiles');

Route::get('/platforms', function () {
    return Inertia::render('Platforms');
})->middleware(['auth', 'verified'])->name('platforms');

Route::get('/tools', function () {
    return Inertia::render('Tools');
})->middleware(['auth', 'verified'])->name('tools');

require __DIR__.'/auth.php';
