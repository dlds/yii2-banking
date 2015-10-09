<?php

namespace dlds\banking\adapters\fio\handlers\api;

use dlds\banking\adapters\fio\builders\UrlBuilder;

abstract class ApiHandler {

    /**
     * Currencies
     */
    const CURRENCY_CZ = 'CZK';
    const CURRENCY_EUR = 'EUR';

    /**
     * @var \dlds\banking\adapters\fio\builders\UrlBuilder
     * builder used to create api urls
     */
    protected $urlBuilder;

    /**
     * Held instances based on API token
     * @var array held instances
     */
    protected static $_instances = [];

    /**
     * Private constructor for ensuring only one instance at runtime
     * @param string $token
     */
    protected function __construct($token)
    {
        $this->urlBuilder = new UrlBuilder($token, __DIR__.'/../../keys/certificate.pem');
    }
}