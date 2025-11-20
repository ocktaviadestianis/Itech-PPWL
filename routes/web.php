<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Admin
Route::get('/dashboard', function () {
    return view('home');
})->middleware(['auth', 'verified', 'admin'])->name('dashboard');

// User
Route::get('/', function () {
    return view('auth.login');
})->name('home');
Route::get('register', function () {
    return view('auth.register');
})->name('register');
// Route::get('/', function () {
//     return view('welcome');
// })->name('home');
Route::middleware('auth')->group(function () {

    Route::resource('/category', CategoryController::class);
    Route::resource('/products', ProductController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
