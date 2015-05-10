<?php

namespace dlds\banking\adapters\fio;

use dlds\banking\Banking;
use dlds\banking\interfaces\TransactionInterface;
use dlds\banking\interfaces\TransactionListInterface;
use dlds\banking\interfaces\TransactionTemplateInterface;
use dlds\banking\adapters\fio\handlers\ApiHandler;

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
     * Enrolls all incoming transactions in given list into DB
     * @param TransactionListInterface $list given list
     * @param TransactionTemplateInterface $template given model
     * to be used as active record template
     */
    public function enrollIncomings(TransactionListInterface $list, TransactionTemplateInterface $template)
    {
        $transactions = $list->getTransactions();

        $result = Banking::ENROLL_NONE;

        if ($transactions)
        {
            foreach ($transactions as $transaction)
            {
                if ($transaction instanceof TransactionInterface)
                {
                    $amount = $transaction->getAmount();

                    if ($amount > 0)
                    {
                        $model = clone $template;
                        $model->setTransactionAmount($amount);
                        $model->setTransactionVariableSymbol($transaction->getVariableSymbol());
                        $model->setTransactionPerformingDateTime($transaction->getDate());
                        $model->setTransactionComment($transaction->getComment());
                        $model->setTransactionId($transaction->getId());
                        $model->setTransactionSenderAccountNum($transaction->getSenderAccountNumber());
                        $model->setTransactionSenderBankCode($transaction->getSenderBankCode());

                        if ($model->save())
                        {
                            $result = (Banking::ENROLL_NONE === $result) ? Banking::ENROLL_ALL : $result;
                        }
                        else
                        {
                            $result = (Banking::ENROLL_NONE === $result) ? Banking::ENROLL_PARTIAL : $result;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Downloads new transactions from bank server
     * automatically detecs undownloded transactions
     * by set stop on bank server
     * @return \dlds\banking\interfaces\TransactionListInterface list
     */
    public function downloadTransactions()
    {
        return ApiHandler::instance($this->token)->downloadNew();
    }

    /**
     * Download all transaction since given datetime
     * @param \DateTime $datetime given date time
     * @return \dlds\banking\interfaces\TransactionListInterface list
     */
    public function downloadTransactionsSince(\DateTime $datetime)
    {
        return ApiHandler::instance($this->token)->downloadSince($datetime);
    }

    /**
     * Download all transaction in given range
     * @param \DateTime $datetime given date time
     * @return \dlds\banking\interfaces\TransactionListInterface list
     */
    public function downloadTransactionsFromTo(\DateTime $from, \DateTime $to)
    {
        return ApiHandler::instance($this->token)->downloadFromTo($from, $to);
    }

    /**
     * Uploads transactions list onto bank server to be processed
     * @param TransactionListInterface $list given list
     * @return boolen TRUE on success, FALSE on failure
     */
    public function uploadTransactionsList(\dlds\banking\interfaces\TransactionListInterface $list)
    {
        return ApiHandler::instance($this->token)->uploadList($list);
    }
}