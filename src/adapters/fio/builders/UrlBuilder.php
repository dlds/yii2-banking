<?php

namespace dlds\banking\adapters\fio\builders;

use dlds\banking\adapters\fio\exceptions\MissingTokenException;

class UrlBuilder {

    const BASE_URL = 'https://www.fio.cz/ib_api/rest/';

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string path to communication certificate
     */
    protected $certificate;

    /**
     * @param string $token
     */
    public function __construct($token, $certificate)
    {
        $this->setToken($token);
        $this->setCertificate($certificate);
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        if (!$token)
        {
            throw new MissingTokenException('Token is required for ebanking API calls. You can get one at https://www.fio.cz/.');
        }

        $this->token = $token;
    }

    /**
     * Sets certificate file path
     * @param string $path
     */
    public function setCertificate($path)
    {
        $this->certificate = $path;
    }

    /**
     * @return string certificate file path
     */
    public function getCertificate()
    {
        return $this->certificate;
    }

    /**
     * @see http://www.fio.cz/docs/cz/API_Bankovnictvi.pdf section 5.2.3
     */
    public function buildLast()
    {
        return sprintf(self::BASE_URL.'last/%s/transactions.json', $this->getToken());
    }

    /**
     * @see http://www.fio.cz/docs/cz/API_Bankovnictvi.pdf section 5.2.1
     */
    public function buildPeriods(\DateTime $from, \DateTime $to)
    {
        return sprintf(
            self::BASE_URL.'periods/%s/%s/%s/transactions.json', $this->getToken(), $from->format('Y-m-d'), $to->format('Y-m-d')
        );
    }

    /**
     * @see http://www.fio.cz/docs/cz/API_Bankovnictvi.pdf section 6.1
     */
    public function buildUpload()
    {
        return self::BASE_URL.'import/';
    }
}