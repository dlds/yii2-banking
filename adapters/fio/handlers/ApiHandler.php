<?php

namespace dlds\banking\adapters\fio\handlers;

use dlds\banking\adapters\fio\components\TransactionList;
use dlds\banking\adapters\fio\exceptions\InternalErrorException;
use dlds\banking\adapters\fio\exceptions\TooGreedyException;
use dlds\banking\adapters\fio\builders\UrlBuilder;
use GuzzleHttp\Client as Guzzle;

class ApiHandler {

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
}