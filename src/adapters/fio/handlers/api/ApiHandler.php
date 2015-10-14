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
     * @var string path to temporary dir to store xml file
     */
    protected $tempDir;

    /**
     * @var boolean whether or not to throw exception on error
     */
    protected $exceptionOnError = false;

    /**
     * @var boolean whether or not to throw exception on warning
     */
    protected $exceptionOnWarning = false;

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