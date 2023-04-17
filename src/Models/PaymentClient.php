<?php

namespace Apachish\PaymentGateway\Models;

class PaymentClient
{
    private $gateway;

    public $orderId;
    public $amount;
    public $localDate;
    public $localTime;
    public $additionalData;
    public $callBackUrl;
    public $payerId;

    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }


    public function setConfig($data)
    {
        return $this->gateway->setConfig($data);
    }


    public function proceedToPay()
    {
        return $this->gateway->createTransaction($this);
    }
}