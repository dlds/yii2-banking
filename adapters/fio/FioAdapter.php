<?php

namespace dlds\banking\adapters\fio;

use dlds\banking\adapters\fio\handlers\TransactionHandler;

class FioAdapter extends \yii\base\Object implements \dlds\banking\interfaces\AdapterInterface {

    /**
     * @var string given api token used to communicate with bank api
     */
    public $token;

    /**
     * Adapter init method, checks if api token is provided
     * @throws Exception throwed when token is not provided
     */
    public function init()
    {
        if (!$this->token)
        {
            throw new Exception('API Token is required in FioAdapter config');
        }
    }

    /**
     * Downloads new transactions from bank server
     * automatically detecs undownloded transactions
     * by set stop on bank server
     * @return \dlds\banking\interfaces\TransactionListInterface list
     */
    public function downloadTransactions()
    {
        return TransactionHandler::instance($this->token)->downloadNew();
    }

    /**
     * Download all transaction since given datetime
     * @param \DateTime $datetime given date time
     * @return \dlds\banking\interfaces\TransactionListInterface list
     */
    public function downloadTransactionsSince(\DateTime $datetime)
    {
        return TransactionHandler::instance($this->token)->downloadSince($datetime);
    }

    /**
     * Uploads transactions list onto bank server to be processed
     * @param TransactionListInterface $list given list
     * @return boolen TRUE on success, FALSE on failure
     */
    public function uploadTransactionsList(\dlds\banking\interfaces\TransactionListInterface $list)
    {
        return TransactionHandler::instance($this->token)->uploadList($list);
    }
}