<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

Route::middleware('web')
    ->prefix('payment/gateway')
    ->namespace('Apachish\PaymentGateway\App\Http\Controllers')
    ->group(function () {

        Route::get("error/{driver}/{code}","ErrorController@getError")->name("gateway_payment.error");
    });
