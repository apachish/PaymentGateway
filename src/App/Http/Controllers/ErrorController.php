<?php

declare(strict_types=1);

namespace Apachish\PaymentGateway\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ErrorController extends Controller
{
    public function getError(Request $request,$driver,$code)
    {
        return  view('gateway_payment::error', [
            'layout'    => env("LAYOUT_PAYMENT"),
            'code'    => $code,
            'message'       => config("errors_gateway_payment.".$driver.".error_".$code),
        ]);


    }
}
