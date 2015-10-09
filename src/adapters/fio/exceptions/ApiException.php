<?php

namespace dlds\banking\adapters\fio\exceptions;

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
class ApiException extends \Exception {

    private $transactions;

    public function __construct($message, $code = 0, $transactions = null)
    {
        $this->transactions = $transactions;
        parent::__construct($message, $code);
    }

    /**
     * Use this method to get more info about exception
     * when you catch FioApiException error code 3 or 4
     * (error or warning in transaction upload).
     * @return array
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
}