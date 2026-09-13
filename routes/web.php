<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminController;

Route::get('/', [ContactController::class, 'index']);

Route::post('/contacts/confirm', [ContactController::class, 'confirm']);

Route::post('/contacts', [ContactController::class, 'store']);

Route::get('/thanks', [ContactController::class, 'thanks']);

Route::get('/admin', [AdminController::class, 'index'])
    ->middleware('auth');

Route::get('/admin/contacts/{contact}', [AdminController::class, 'show'])
    ->middleware('auth');

Route::delete('/admin/contacts/{contact}', [AdminController::class, 'destroy'])
    ->middleware('auth');

Route::get('/admin/tags/{tag}/edit', [AdminController::class, 'editTag'])
    ->middleware('auth');

Route::put('/admin/tags/{tag}', [AdminController::class, 'updateTag'])
    ->middleware('auth');

Route::post('/admin/tags', [AdminController::class, 'storeTag'])
    ->middleware('auth');

Route::delete('/admin/tags/{tag}', [AdminController::class, 'destroyTag'])
    ->middleware('auth');

Route::get('/contacts/export', [AdminController::class, 'export'])
    ->middleware('auth');