<?php

namespace Apachish\PaymentGateway\Models;



interface Gateway
{
    public function steConfig();
    
    public function sendGateway();
    
    public function callBack();
}
