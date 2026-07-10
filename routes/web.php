<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - render Blade views (frontend), konsumsi data dari routes/api.php
| lewat fetch() di public/js/app.js.
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('shop.landing'))->name('landing');
Route::get('/cart', fn () => view('shop.cart'))->name('cart');
Route::get('/login', fn () => view('auth.login'))->name('login');
Route::get('/register', fn () => view('auth.register'))->name('register');

// Admin (frontend rendering saja; proteksi data sesungguhnya tetap di API lewat JWT+role middleware)
Route::get('/admin/dashboard', fn () => view('admin.dashboard'))->name('admin.dashboard');
Route::get('/admin/products', fn () => view('admin.products'))->name('admin.products');
