<?php

namespace FioApi;

/**
 * FIO API - upload transactions
 * 
 * This class is NOT freely distributed software!
 *  - You are not allowed to distribute this class.
 *  - You are allowed to use it only if you have bought licence.
 * @author Václav Černík <info@onlinesoft.cz>
 */
class FioApiUpload extends FioApi {

    private $xml, $orders, $tempDir, $exceptionOnError = true, $exceptionOnWarning = false;

    /**
     * Set FIO token, set PATH to certificate (optional).
     * @param string $token FIO TOKEN
     * @param string $certificate PATH to certificate (downloaded file).
     */
    public function __construct($token, $certificate = false)
    {
        parent::__construct($token, $certificate);

        $this->xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><Import xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://www.fio.cz/schema/importIB.xsd"></Import>');
        $this->orders = $this->xml->addChild('Orders');

        $this->setTempDir(sys_get_temp_dir());
    }

    /**
     * Set SSL certificate. You can download it on URL
     * http://www.geotrust.com/resources/root-certificates/ (Root 1 - Equifax Secure Certificate Authority)
     * @param string $certificate PATH to certificate (downloaded file).
     * @return \FioApi\FioApiUpload
     * @throws \FioApi\FioApiException
     */
    public function setCertificate($certificate)
    {
        return $this->_setCertificate($certificate);
    }

    /**
     * Set temp dir
     * @param string $dir PATH of temp dir
     * @return \FioApi\FioApiUpload
     */
    public function setTempDir($dir)
    {
        $this->tempDir = $dir;
        return $this;
    }

    /**
     * Catch exceptions on error
     * @param bool $bool Set to false, if you don't want to catch exceptions on error. Default value is true.
     * @return \FioApi\FioApiUpload
     */
    public function setExceptionOnError($bool)
    {
        $this->exceptionOnError = (bool) $bool;
        return $this;
    }

    /**
     * Catch exception on warning
     * @param bool $bool Set to true, if you want to catch exceptions on warning. Default value is false.
     * @return \FioApi\FioApiUpload
     */
    public function setExceptionOnWarning($bool)
    {
        $this->exceptionOnWarning = (bool) $bool;
        return $this;
    }

    /**
     * Add created transaction
     * @param \FioApi\Transaction $transaction Created transaction (object)
     * @return \FioApi\FioApiUpload
     */
    public function addTransaction(Transaction $transaction)
    {
        if ($transaction instanceof DomesticTransaction)
        {
            $transactionxml = $this->orders->addChild('DomesticTransaction');
        }
        elseif ($transaction instanceof ForeignTransaction)
        {
            $transactionxml = $this->orders->addChild('ForeignTransaction');
        }
        elseif ($transaction instanceof Target2Transaction)
        {
            $transactionxml = $this->orders->addChild('T2Transaction');
        }

        foreach ($transaction->getParams() as $key => $value)
        {
            if ($value != null)
                $transactionxml->addChild($key, $value);
        }
        return $this;
    }

    /**
     * Upload your transactions!
     * @return string
     */
    public function upload()
    {
        $tmpfname = tempnam($this->tempDir, "FIO");
        file_put_contents($tmpfname, $this->xml->asXML());
        $xml = $this->launchUpload($tmpfname);
        return $this->parseXml($xml);
    }

    /**
     * @param string $xml
     * @return array
     * @throws \FioApi\FioApiException
     */
    private function parseXml($xml)
    {
        $xml = simplexml_load_string($xml);

        $transactions = array();
        $transactions['status'] = (string) $xml->result->status;
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
            throw new FioApiException('Error in transaction(s). See $e->getTransactions();', 3, $transactions);
        }
        if ($this->exceptionOnWarning and $transactions['status'] == 'warning')
        {
            throw new FioApiException('Warning in transaction(s). All transactions have been uploaded! See $e->getTransactions();', 4, $transactions);
        }

        return $transactions;
    }

    /**
     * @param string $tmpfname
     * @return string
     */
    private function launchUpload($tmpfname)
    {
        $url = self::API_URL.'import/';

        $curl = $this->curlInit($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, array(
            'type' => 'xml',
            'token' => $this->token,
            'lng' => 'en',
            'file' => '@'.$tmpfname
        ));
        $result = curl_exec($curl);

        unlink($tmpfname);

        $this->curlHttpCode($curl);
        return $result;
    }
}

abstract class Transaction {

    protected $params;

    
}

/**
 * FIO API - Create new domestic transaction.
 */
class DomesticTransaction extends Transaction {

    
}

class ForeignTransaction extends Transaction {

   
}

class Target2Transaction extends Transaction {

    
}