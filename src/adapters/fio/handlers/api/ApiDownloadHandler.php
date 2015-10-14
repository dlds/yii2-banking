<?php

namespace dlds\banking\adapters\fio\handlers\api;

use dlds\banking\adapters\fio\components\transactions\lists\TransactionDownloadList;
use dlds\banking\adapters\fio\exceptions\InternalErrorException;
use dlds\banking\adapters\fio\exceptions\TooGreedyException;
use dlds\banking\adapters\fio\handlers\api\ApiHandler;
use GuzzleHttp\Client as Guzzle;

class ApiDownloadHandler extends ApiHandler {

    const CURRENCY_CZ = 'CZK';

    /**
     * @var \GuzzleHttp\Client client used to communication
     */
    protected $client;

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
     * Inits instance
     */
    public function init()
    {
        // empty
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
        return $this->download($from, $to);
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
            $response = $client->get($url, ['verify' => $this->urlBuilder->getCertificate()]);
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

        return TransactionDownloadList::create($response->json([
                    'object' => true,
                    'big_int_strings' => true,
                ])->accountStatement);
    }
}