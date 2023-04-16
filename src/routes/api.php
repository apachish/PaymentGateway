<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

Route::middleware('api')
    ->prefix('api/v1')
    ->namespace('Apachish\Media\App\Http\Controllers')
    ->group(function () {

        Route::prefix('user')->group(function () {

        });
    });
