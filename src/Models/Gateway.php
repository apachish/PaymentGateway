<?php

namespace Apachish\PaymentGateway\Models;



interface Gateway
{
    public function createTransaction($data);
    public function setConfig($data);
}
