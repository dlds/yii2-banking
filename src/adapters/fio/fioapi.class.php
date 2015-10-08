<?php
namespace FioApi;
/**
 * PHP classes which helps you to connect with FIO Bank.
 * This class is NOT freely distributed software!
 *  - You are not allowed to distribute this class.
 *  - You are allowed to use it only if you have bought licence.
 * @author Václav Černík <info@onlinesoft.cz>
 */

/**
 * Error codes:
 *  - 1: Incorrect PATH to certificate.
 *  - 2: No server response. There is probably incorrect certificate.
 *  - 3: Error in transaction(s). See $e->getTransactions();
 *  - 4: Warning in transaction(s). See $e->getTransactions();
 *  - 5: setArraySimple(): Param is not array.
 *  - 6: setArraySimple(): You can use only values, which are predefined (see documentation or top of FioApiDownload class).
 *  - 409: You can download or upload transactions with same token only every 30 seconds.
 *  - 404: The requested resource is not available. Maybe some problem with Internet banking.
 *  - 500: Internal Error. There is probably a wrong token.
 */
class FioApiException extends \Exception {

    private $transactions;

    public function __construct($message, $code = 0, $transactions = null) {
        $this->transactions = $transactions;
        parent::__construct($message, $code);
    }

    /**
     * Use this method to get more info about exception
     * when you catch FioApiException error code 3 or 4
     * (error or warning in transaction upload).
     * @return array
     */
    public function getTransactions() {
        return $this->transactions;
    }

}

/**
 * FIO API - parent of FioApiDownload and FioApiUpload
 */
abstract class FioApi {

    const API_URL = "https://www.fio.cz/ib_api/rest/";
    const TIMEZONE = "Europe/Prague";

    protected $token, $certificate = false;

    /**
     * Set FIO token, set PATH to certificate (optional).
     * @param string $token FIO TOKEN
     * @param string $certificate PATH to certificate (downloaded file).
     */
    public function __construct($token, $certificate = false) {
        $this->token = $token;
        if ($certificate) {
            $this->setCertificate($certificate);
        }
    }

    /**
     * @param string $certificate
     * @return \FioApi\FioApi
     * @throws FioApiException
     */
    protected function _setCertificate($certificate) {
        if (!file_exists($certificate)) {
            throw new FioApiException('Incorrect PATH to certificate.', 1);
        }
        $this->certificate = $certificate;
        return $this;
    }
    
    /**
     * @param string $url
     * @return curl
     */
    protected function curlInit($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        if ($this->certificate) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
            curl_setopt($curl, CURLOPT_CAINFO, $this->certificate);
        } else {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        }
        return $curl;
    }

    /**
     * @param curl $curl
     * @throws FioApiException
     */
    protected function curlHttpCode($curl) {
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpcode == 409) {
            throw new FioApiException('You can download or upload transactions with same token only every 30 seconds.', 409);
        } elseif ($httpcode == 404) {
            throw new FioApiException('The requested resource is not available. Maybe some problem with Internet banking.', 404);
        } elseif ($httpcode == 500) {
            throw new FioApiException('Internal Error. There is probably a wrong token.', 500);
        }
    }

}
