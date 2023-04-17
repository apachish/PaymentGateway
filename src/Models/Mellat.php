<?php

namespace Apachish\PaymentGateway\Models;



use Apachish\PaymentGateway\App\Adapter\AdapterAbstract;
use Apachish\PaymentGateway\App\Adapter\AdapterInterface;
use Illuminate\Support\Facades\Log;
use Exception;

class Mellat   extends AdapterAbstract implements Gateway
{


    protected $WSDL     = 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl';
    protected $endPoint = 'https://bpm.shaparak.ir/pgwchannel/startpay.mellat';

    protected $testWSDL     = 'https://sandbox.banktest.ir/mellat/bpm.shaparak.ir/pgwchannel/services/pgw?wsdl';
    protected $testEndPoint = 'https://sandbox.banktest.ir/mellat/bpm.shaparak.ir/pgwchannel/startpay.mellat';

    protected $reverseSupport = true;

    protected $terminal_id;
    protected $username;
    protected $password;

    public function setConfig($data)
    {
        
        $this->terminal_id		=  data_get($data,"terminal_id");					// Terminal ID
        $this->username		=  data_get($data,"username");					// user name
        $this->password		=  data_get($data,"password");					// password
  
    }
    /**
     * @return array
     * @throws Exception
     * @throws \PhpMonsters\Larapay\Adapter\Exception
     */
    public function createTransaction($data)
    {


        $sendParams = [
            'terminalId'     => intval($this->terminal_id),
            'userName'       => $this->username,
            'userPassword'   => $this->password,
            'orderId'        => intval(data_get($data,'orderId')),
            'amount'         => intval(data_get($data,'amount')),
            'localDate'      => data_get($data,'localDate' ,date('Ymd')),
            'localTime'      => data_get($data,'localTime' , date('His')),
            'additionalData' => data_get($data,'additionalData'),
            'callBackUrl'    => data_get($data,'callBackUrl'),
            'payerId'        => intval(data_get($data,'payerId')),
        ];

        try {
            $soapClient = $this->getSoapClient();

            Log::debug('bpPayRequest call', $sendParams);

            $response = $soapClient->bpPayRequest($sendParams);

            if (isset($response->return)) {
                Log::info('bpPayRequest response', ['return' => $response->return]);

                $response = explode(',', $response->return);

                if ($response[0] == 0) {
                    $this->getTransaction()->setGatewayToken(strval($response[1])); // update transaction reference id

                    return $response[1];
                } else {
                    return redirect(route("gateway_payment.error",["driver"=>"mellat","code"=>$response[0]]));

                   
                }
            } else {
                throw new Exception('gateway_payment::gateway_payment.invalid_response');
            }
        } catch (\SoapFault $e) {
            throw new Exception('SoapFault: ' . $e->getMessage() . ' #' . $e->getCode(), $e->getCode());
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function generateForm(): string
    {
        $refId = $this->requestToken();

        $form = view('gateway_payment::mellat-form', [
            'endPoint'    => $this->getEndPoint(),
            'refId'       => $refId,
            'submitLabel' => !empty($this->submit_label) ? $this->submit_label : trans("gateway_payment::larapay.goto_gate"),
            'autoSubmit'  => boolval($this->auto_submit),
        ]);

        return $form->__toString();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function formParams(): array
    {
        $refId = $this->requestToken();

        return  [
            'endPoint'    => $this->getEndPoint(),
            'refId'       => $refId,
        ];
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \PhpMonsters\Larapay\Adapter\Exception
     */
    protected function verifyTransaction()
    {
        if ($this->getTransaction()->checkForVerify() === false) {
            throw new Exception('larapay::larapay.could_not_verify_payment');
        }

        $this->checkRequiredParameters([
            'terminal_id',
            'username',
            'password',
            'RefId',
            'ResCode',
            'SaleOrderId',
            'SaleReferenceId',
            'CardHolderInfo',
            'CardHolderPan',
        ]);

        $sendParams = [
            'terminalId'      => intval($this->terminal_id),
            'userName'        => $this->username,
            'userPassword'    => $this->password,
            'orderId'         => intval($this->SaleOrderId), // same as SaleOrderId
            'saleOrderId'     => intval($this->SaleOrderId),
            'saleReferenceId' => intval($this->SaleReferenceId),
        ];

        $this->getTransaction()->setCardNumber(strval($this->CardHolderInfo));

        try {
            $soapClient = $this->getSoapClient();

            XLog::debug('bpVerifyRequest call', $sendParams);

            //$response   = $soapClient->__soapCall('bpVerifyRequest', $sendParams);
            $response = $soapClient->bpVerifyRequest($sendParams);

            if (isset($response->return)) {
                XLog::info('bpVerifyRequest response', ['return' => $response->return]);

                if ($response->return != '0') {
                    throw new Exception($response->return);
                } else {
                    $this->getTransaction()->setVerified();

                    return true;
                }
            } else {
                throw new Exception('larapay::larapay.invalid_response');
            }

        } catch (SoapFault $e) {

            throw new Exception('SoapFault: ' . $e->getMessage() . ' #' . $e->getCode(), $e->getCode());
        }
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \PhpMonsters\Larapay\Adapter\Exception
     */
    public function inquiryTransaction()
    {
        if ($this->getTransaction()->checkForInquiry() === false) {
            throw new Exception('larapay::larapay.could_not_inquiry_payment');
        }

        $this->checkRequiredParameters([
            'terminal_id',
            'terminal_user',
            'terminal_pass',
            'RefId',
            'ResCode',
            'SaleOrderId',
            'SaleReferenceId',
            'CardHolderInfo',
        ]);

        $sendParams = [
            'terminalId'      => intval($this->terminal_id),
            'userName'        => $this->username,
            'userPassword'    => $this->password,
            'orderId'         => intval($this->SaleOrderId), // same as SaleOrderId
            'saleOrderId'     => intval($this->SaleOrderId),
            'saleReferenceId' => intval($this->SaleReferenceId),
        ];

        $this->getTransaction()->setCardNumber(strval($this->CardHolderInfo));

        try {
            $soapClient = $this->getSoapClient();

            XLog::debug('bpInquiryRequest call', $sendParams);
            //$response   = $soapClient->__soapCall('bpInquiryRequest', $sendParams);
            $response = $soapClient->bpInquiryRequest($sendParams);

            if (isset($response->return)) {
                XLog::info('bpInquiryRequest response', ['return' => $response->return]);
                if ($response->return != '0') {
                    throw new Exception($response->return);
                } else {
                    $this->getTransaction()->setVerified();

                    return true;
                }
            } else {
                throw new Exception('larapay::larapay.invalid_response');
            }

        } catch (SoapFault $e) {

            throw new Exception('SoapFault: ' . $e->getMessage() . ' #' . $e->getCode(), $e->getCode());
        }
    }

    /**
     * Send settle request
     *
     * @return bool
     *
     * @throws Exception
     * @throws \PhpMonsters\Larapay\Adapter\Exception
     */
    protected function settleTransaction()
    {
        if ($this->getTransaction()->checkForAfterVerify() === false) {
            throw new Exception('larapay::larapay.could_not_settle_payment');
        }

        $this->checkRequiredParameters([
            'terminal_id',
            'username',
            'password',
            'RefId',
            'ResCode',
            'SaleOrderId',
            'SaleReferenceId',
            'CardHolderInfo',
        ]);

        $sendParams = [
            'terminalId'      => intval($this->terminal_id),
            'userName'        => $this->username,
            'userPassword'    => $this->password,
            'orderId'         => intval($this->SaleOrderId), // same as orderId
            'saleOrderId'     => intval($this->SaleOrderId),
            'saleReferenceId' => intval($this->SaleReferenceId),
        ];

        try {
            $soapClient = $this->getSoapClient();

            XLog::debug('bpSettleRequest call', $sendParams);
            //$response = $soapClient->__soapCall('bpSettleRequest', $sendParams);
            $response = $soapClient->bpSettleRequest($sendParams);

            if (isset($response->return)) {
                XLog::info('bpSettleRequest response', ['return' => $response->return]);

                if ($response->return == '0' || $response->return == '45') {
                    $this->getTransaction()->setAfterVerified();

                    return true;
                } else {
                    throw new Exception($response->return);
                }
            } else {
                throw new Exception('larapay::larapay.invalid_response');
            }

        } catch (\SoapFault $e) {
            throw new Exception('SoapFault: ' . $e->getMessage() . ' #' . $e->getCode(), $e->getCode());
        }

    }

    /**
     * @return bool
     * @throws Exception
     * @throws \PhpMonsters\Larapay\Adapter\Exception
     */
    protected function reverseTransaction(): bool
    {
        if ($this->reverseSupport === false || $this->getTransaction()->checkForReverse() === false) {
            throw new Exception('larapay::larapay.could_not_reverse_payment');
        }

        $this->checkRequiredParameters([
            'terminal_id',
            'username',
            'password',
            'RefId',
            'ResCode',
            'SaleOrderId',
            'SaleReferenceId',
            'CardHolderInfo',
        ]);

        $sendParams = [
            'terminalId'      => intval($this->terminal_id),
            'userName'        => $this->username,
            'userPassword'    => $this->password,
            'orderId'         => intval($this->SaleOrderId), // same as orderId
            'saleOrderId'     => intval($this->SaleOrderId),
            'saleReferenceId' => intval($this->SaleReferenceId),
        ];

        try {
            $soapClient = $this->getSoapClient();

            XLog::debug('bpReversalRequest call', $sendParams);
            //$response = $soapClient->__soapCall('bpReversalRequest', $sendParams);
            $response = $soapClient->bpReversalRequest($sendParams);

            XLog::info('bpReversalRequest response', ['return' => $response->return]);

            if (isset($response->return)) {
                if ($response->return == '0' || $response->return == '45') {
                    $this->getTransaction()->setRefunded();

                    return true;
                } else {
                    throw new Exception($response->return);
                }
            } else {
                throw new Exception('larapay::larapay.invalid_response');
            }

        } catch (SoapFault $e) {
            throw new Exception('SoapFault: ' . $e->getMessage() . ' #' . $e->getCode(), $e->getCode());
        }
    }


    /**
     * @return bool
     */
    public function canContinueWithCallbackParameters(): bool
    {
        if ($this->ResCode === "0" || $this->ResCode === 0) {
            return true;
        }

        return false;
    }

    public function getGatewayReferenceId(): string
    {
        $this->checkRequiredParameters([
            'RefId',
        ]);

        return strval($this->RefId);
    }

    public function afterVerify(): bool
    {
        return $this->settleTransaction();
    }
}
