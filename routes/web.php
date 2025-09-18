<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Student routes (accessible to all authenticated users)
    Route::get('/student',function () { 
        return view('dashboard');
    })->name('dashboard');

    // Instructor-only routes
    Route::get('/instructor/courses', function () {
        return view('dashboard');
    })->middleware('role:instructor')->name('dashboard');


    // Admin-only routes
    Route::get('admin/users', function () { 
        return view('dashboard');
    })->middleware('role:admin')->name('dashboard');
});

require __DIR__.'/auth.php';