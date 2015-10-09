<?php

namespace dlds\banking\adapters\fio\handlers\api;

use dlds\banking\interfaces\TransactionUploadListInterface;
use dlds\banking\adapters\fio\components\TransactionList;
use dlds\banking\adapters\fio\exceptions\InternalErrorException;
use dlds\banking\adapters\fio\exceptions\TooGreedyException;
use dlds\banking\adapters\fio\builders\UrlBuilder;
use GuzzleHttp\Client as Guzzle;

class ApiDownloadHandler {

    const CURRENCY_CZ = 'CZK';

    /**
     * @var \dlds\banking\adapters\fio\builders\UrlBuilder
     * builder used to create api urls
     */
    protected $urlBuilder;

    /**
     * @var \GuzzleHttp\Client client used to communication
     */
    protected $client;

    /**
     * @var string path to communication certificate
     */
    protected $certificatePath;

    /**
     * @var string temp dir path
     */
    protected $tempDir;

    /**
     * @var boolean wheter or not to throw exception on error
     */
    protected $exceptionOnError = true;

    /**
     * @var boolean wheter or not to throw exception on warning
     */
    protected $exceptionOnWarning = false;

    /**
     * Held instances based on API token
     * @var array held instances
     */
    private static $_instances = [];

    /**
     * Private constructor for ensuring only one instance at runtime
     * @param string $token
     */
    private function __construct($token)
    {
        $this->urlBuilder = new UrlBuilder($token);
    }

    /**
     * Singleton method for getting instance
     * @param string $token
     */
    public static function instance($token)
    {
        if (!isset(self::$_instances[$token]))
        {
            self::$_instances[$token] = new self($token);
        }

        return self::$_instances[$token];
    }

    /**
     * @param string $path
     */
    public function setCertificatePath($path)
    {
        $this->certificatePath = $path;
    }

    public function getCertificatePath()
    {
        if ($this->certificatePath)
        {
            return $this->certificatePath;
        }

        //Key downloaded from https://www.geotrust.com/resources/root-certificates/
        return __DIR__.'/../keys/Equifax_Secure_Certificate_Authority.pem';
    }

    /**
     * @return Guzzle
     */
    public function getClient()
    {
        if (!$this->client)
        {
            $this->client = new Guzzle();
        }
        return $this->client;
    }

    /**
     * Uploads xml file into bank account
     * @param TransactionUploadListInterface $list given list
     */
    public function uploadList(TransactionUploadListInterface $list)
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><Import xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://www.fio.cz/schema/importIB.xsd"></Import>');
        $orders = $xml->addChild('Orders');

        foreach ($list->getTransactions() as $transaction)
        {
            var_dump($transaction);
            die();
            if ($transaction instanceof \dlds\banking\adapters\fio\components\transactions\DomesticTransaction)
            {
                $transactionxml = $orders->addChild('DomesticTransaction');
            }
            elseif ($transaction instanceof \dlds\banking\adapters\fio\components\transactions\ForeignTransaction)
            {
                die('eesss');
                $transactionxml = $orders->addChild('ForeignTransaction');
            }
            elseif ($transaction instanceof \dlds\banking\adapters\fio\components\transactions\Target2Transaction)
            {
                $transactionxml = $orders->addChild('T2Transaction');
            }

            foreach ($transaction->getParams() as $key => $value)
            {
                if ($value != null)
                {
                    $transactionxml->addChild($key, $value);
                }
            }
        }

        $this->setTempDir(sys_get_temp_dir());

        $tmpfname = tempnam($this->tempDir, "FIO");
        file_put_contents($tmpfname, $xml->asXML());

        if (version_compare(PHP_VERSION, '5.5', '>='))
        {
            $file = new \CURLFile($tmpfname, 'text/xml');
        }
        else
        {
            $file = '@'.$tmpfname;
        }

        $url = $this->urlBuilder->buildUpload();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        if ($this->certificatePath)
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
            curl_setopt($curl, CURLOPT_CAINFO, $this->certificatePath);
        }
        else
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        }

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, array(
            'type' => 'xml',
            'token' => $this->urlBuilder->getToken(),
            'lng' => 'cs',
            'file' => $file
        ));

        $result = curl_exec($curl);

        echo $result;
        var_dump(curl_error($curl));
        die();

        unlink($filepath);

        $this->curlHttpCode($curl);

        return $result;
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
     * Downloads all new transactions from bank server
     * @return TransactionList list of transactions
     */
    public function downloadNew()
    {
        return $this->download(false, false);
    }

    /**
     * Downloads all transactions since given DateTime from bank server
     * @param \DateTime $since given DateTime
     * @return TransactionList list of transactions
     */
    public function downloadSince(\DateTime $since)
    {
        return $this->download($since, new \DateTime());
    }

    /**
     * Download all transaction in given datetiem range "from" "to"
     * @param \DateTime $from given "from" bound
     * @param \DateTime $to given "to" bound
     * @return TransactionList transactions list
     */
    public function downloadFromTo(\DateTime $from, \DateTime $to)
    {
        return $this->download($since, $to);
    }

    /**
     * Callback to process download from bank server
     * @param mixed $from DateTime representing "from" bound or false if not set
     * @param mixed $to DateTime representing "to" bound or false if not set
     * @return TransactionList transactions list
     * @throws TooGreedyException
     * @throws InternalErrorException
     * @throws \GuzzleHttp\Exception\BadResponseException
     */
    private function download($from, $to)
    {
        $client = $this->getClient();

        if (false === $from)
        {
            $url = $this->urlBuilder->buildLast();
        }
        else
        {
            $url = $this->urlBuilder->buildPeriods($from, $to);
        }

        try
        {
            $response = $client->get($url, ['verify' => $this->getCertificatePath()]);
        }
        catch (\GuzzleHttp\Exception\BadResponseException $e)
        {
            if ($e->getCode() == 409)
            {
                throw new TooGreedyException('You can use one token for API call every 30 seconds', $e->getCode(), $e);
            }
            if ($e->getCode() == 500)
            {
                throw new InternalErrorException('Server returned 500 Internal Error (probably invalid token?)', $e->getCode(), $e);
            }
            throw $e;
        }

        return TransactionList::create($response->json([
                    'object' => true,
                    'big_int_strings' => true,
                ])->accountStatement);
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
}