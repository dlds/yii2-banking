<?php

namespace dlds\banking\adapters\fio\handlers\api;

use yii\helpers\ArrayHelper;
use dlds\banking\adapters\fio\handlers\api\ApiHandler;
use dlds\banking\interfaces\transactions\TransactionInterface;
use dlds\banking\interfaces\transactions\lists\TransactionUploadListInterface;

class ApiUploadHandler extends ApiHandler {

    /**
     * Error codes
     */
    const ERROR_NONE = 0;
    const ERROR_MISTAKES = 2;
    const ERROR_VALUES = 2;
    const ERROR_SYNTAX = 11;
    const ERROR_NO_TRANSACTIONS = 12;
    const ERROR_TOO_LONG = 13;
    const ERROR_EMPTY_XML = 14;
    const ERROR_NO_RESPONSE = 99;

    /**
     * @var \SimpleXMLElement upload xml element
     */
    protected $xml;

    /**
     * @var \SimpleXMLElement orders xml element
     */
    protected $orders;

    /**
     * Curl settings
     */
    protected $curlType = 'xml';
    protected $curlLng = 'cs';

    /**
     * Singleton method for getting instance
     * @param string $token
     * @return ApiUploadHandler instance
     */
    public static function instance($token)
    {
        $key = sprintf('%s-%s', __CLASS__, $token);

        if (!isset(self::$_instances[$key]))
        {
            self::$_instances[$key] = new self($token);
        }

        self::$_instances[$key]->init();

        return self::$_instances[$key];
    }

    /**
     * Inits handler
     */
    public function init()
    {
        $this->xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><Import xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://www.fio.cz/schema/importIB.xsd"></Import>');

        $this->orders = $this->xml->addChild('Orders');

        $this->setTempDir(sys_get_temp_dir());
    }

    /**
     * Uploads xml file into bank account
     * @param TransactionUploadListInterface $list given list
     * @return mixed bank instruction ID when upload was succesfull otherwise false
     */
    public function uploadList(TransactionUploadListInterface $list)
    {
        foreach ($list->getTransactions() as $transaction)
        {
            $this->addTransactionToXml($transaction);
        }

        $request = $this->curlInit($this->urlBuilder->buildUpload());

        $response = $this->parseResponseXml($this->curlProcess($request));

        return $this->curlResult($response);
    }

    /**
     * Adds given transaction to current xml
     * @param TransactionInterface $transaction
     */
    protected function addTransactionToXml(TransactionInterface $transaction)
    {
        if ($transaction instanceof \dlds\banking\adapters\fio\components\transactions\DomesticTransaction)
        {
            $xmlElement = $this->orders->addChild('DomesticTransaction');
        }
        elseif ($transaction instanceof \dlds\banking\adapters\fio\components\transactions\ForeignTransaction)
        {
            $xmlElement = $this->orders->addChild('ForeignTransaction');
        }
        elseif ($transaction instanceof \dlds\banking\adapters\fio\components\transactions\Target2Transaction)
        {
            $xmlElement = $this->orders->addChild('T2Transaction');
        }

        foreach ($transaction->getParams() as $key => $value)
        {
            if ($value != null)
            {
                $xmlElement->addChild($key, $value);
            }
        }
    }

    /**
     * @param string $url
     * @return curl
     */
    protected function curlInit($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        if ($this->urlBuilder->getCertificate())
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
            curl_setopt($curl, CURLOPT_CAINFO, $this->urlBuilder->getCertificate());
        }
        else
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        }
        return $curl;
    }

    /**
     * Processes curl upload request
     */
    protected function curlProcess($curl)
    {
        $tmpfname = tempnam($this->tempDir, "FIO");
        file_put_contents($tmpfname, $this->xml->asXML());

        if (version_compare(PHP_VERSION, '5.5', '>='))
        {
            $file = new \CurlFile($tmpfname, 'text/xml');
        }
        else
        {
            $file = '@'.$tmpfname;
        }

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, array(
            'type' => $this->curlType,
            'token' => $this->urlBuilder->getToken(),
            'lng' => $this->curlLng,
            'file' => $file
        ));

        $result = curl_exec($curl);

        unlink($tmpfname);

        $this->curlHttpCode($curl);

        return $result;
    }

    /**
     * Handles curl response and make approptiate result
     * @param array parsed curl response into array
     * @return int result code
     */
    protected function curlResult(array $response)
    {
        // TODO: implement better logging result

        \Yii::info(var_export($response, true));

        $error = ArrayHelper::getValue($response, 'errorCode', self::ERROR_NO_RESPONSE);

        if (self::ERROR_NONE == $error)
        {
            return ArrayHelper::getValue($response, 'idInstruction', false);
        }

        return false;
    }

    /**
     * @param curl $curl
     * @throws FioApiException
     */
    protected function curlHttpCode($curl)
    {
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($httpcode == 409)
        {
            throw new \dlds\banking\adapters\fio\exceptions\ApiException('You can download or upload transactions with same token only every 30 seconds.', 409);
        }
        elseif ($httpcode == 404)
        {
            throw new \dlds\banking\adapters\fio\exceptions\ApiException('The requested resource is not available. Maybe some problem with Internet banking.', 404);
        }
        elseif ($httpcode == 500)
        {
            throw new \dlds\banking\adapters\fio\exceptions\ApiException('Internal Error. There is probably a wrong token.', 500);
        }
    }

    /**
     * @param string $xml
     * @return array
     * @throws \FioApi\FioApiException
     */
    private function parseResponseXml($response)
    {
        $xml = simplexml_load_string($response);

        if (!$xml)
        {
            throw new \dlds\banking\adapters\fio\exceptions\ApiException('Error - Invalid response');
        }

        $transactions = array();
        $transactions['status'] = (string) $xml->result->status;
        $transactions['idInstruction'] = (string) $xml->result->idInstruction;
        $transactions['errorCode'] = (string) $xml->result->errorCode;

        if (isset($xml->result->message))
        {
            $transactions['message'] = (string) $xml->result->message;
        }
        if (isset($xml->ordersDetails))
        {
            foreach ($xml->ordersDetails->children() as $ch)
            {
                $ch2 = $ch->messages->children();
                $transactions['transactions'][] = array('status' => (string) $ch2[0]->attributes()->status, 'message' => (string) $ch2[0]);
            }
        }

        if ($this->exceptionOnError and $transactions['status'] == 'error')
        {
            throw new \dlds\banking\adapters\fio\exceptions\ApiException('Error in transaction(s). See $e->getTransactions();', 3, $transactions);
        }
        if ($this->exceptionOnWarning and $transactions['status'] == 'warning')
        {
            throw new \dlds\banking\adapters\fio\exceptions\ApiException('Warning in transaction(s). All transactions have been uploaded! See $e->getTransactions();', 4, $transactions);
        }

        return $transactions;
    }
}