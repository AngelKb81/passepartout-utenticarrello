<?php

use Illuminate\Support\Facades\Route;

// Tutte le routes sono gestite da Vue Router (SPA)
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
