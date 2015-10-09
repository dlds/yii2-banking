<?php

namespace dlds\banking\adapters\fio;

use dlds\banking\Banking;
use dlds\banking\adapters\fio\handlers\api\ApiUploadHandler;
use dlds\banking\adapters\fio\components\transactions\lists\TransactionUploadList;
use dlds\banking\interfaces\transactions\TransactionInterface;
use dlds\banking\interfaces\transactions\TransactionalRecordInterface;
use dlds\banking\interfaces\transactions\lists\TransactionDownloadListInterface;
use dlds\banking\interfaces\transactions\lists\TransactionUploadListInterface;

class FioAdapter extends \yii\base\Object implements \dlds\banking\interfaces\AdapterInterface {

    /**
     * @var string given api token used to communicate with bank api
     */
    public $token;

    /**
     * @var boolean
     */
    public $test = false;

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
     * @param TransactionDownloadListInterface $list given list
     * @param TransactionalRecordInterface $record given model
     * to be used as active record template
     */
    public function enrollIncomings(TransactionDownloadListInterface $list, TransactionalRecordInterface $record)
    {
        $transactions = $list->getTransactions();

        $result = Banking::ENROLL_NONE;

        if ($transactions)
        {
            foreach ($transactions as $transaction)
            {
                if ($transaction instanceof TransactionInterface)
                {
                    $model = $this->initTransactionalRecord(clone $record, $transaction);

                    if ($model->getTransactionAmount() > 0 && !$record->findByTransactionId($model->getTransactionId()))
                    {
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
     * @return \dlds\banking\interfaces\TransactionDownloadListInterface list
     */
    public function downloadTransactions()
    {
        return ApiHandler::instance($this->token)->downloadNew();
    }

    /**
     * Download all transaction since given datetime
     * @param \DateTime $datetime given date time
     * @return \dlds\banking\interfaces\TransactionDownloadListInterface list
     */
    public function downloadTransactionsSince(\DateTime $datetime)
    {
        return ApiHandler::instance($this->token)->downloadSince($datetime);
    }

    /**
     * Download all transaction in given range
     * @param \DateTime $datetime given date time
     * @return \dlds\banking\interfaces\TransactionDownloadListInterface list
     */
    public function downloadTransactionsFromTo(\DateTime $from, \DateTime $to)
    {
        return ApiHandler::instance($this->token)->downloadFromTo($from, $to);
    }

    /**
     * Uploads transactions list onto bank server to be processed
     * @param array $models given models to be enrolled
     * @return boolen TRUE on success, FALSE on failure
     */
    public function uploadTransactions(array $models)
    {
        $list = $this->createUploadList($models);

        if ($list && $list->hasTransactions())
        {
            $instruction = ApiUploadHandler::instance($this->token)->uploadList($list);

            if (false !== $instruction)
            {
                $this->setTransactionalRecordsAsProcessed($models, $instruction);

                return Banking::ENROLL_ALL;
            }

            return Banking::ENROLL_PARTIAL;
        }

        return Banking::ENROLL_NONE;
    }

    /**
     * Creates transactions list
     * @param array $models given models to be added into list
     * @retun array $list
     */
    public function createUploadList(array $models)
    {
        return $this->initTransactionUploadList(new TransactionUploadList, $models);
    }

    /**
     * Creates transactional object from given transaction
     * @param TransactionalRecordInterface $record given model to be used
     * @param TransactionInterface $transaction given transaction to be used as initializer
     * @return TransactionalRecordInterface local template object
     */
    private function initTransactionalRecord(TransactionalRecordInterface $record, TransactionInterface $transaction)
    {
        $record->setTransactionAmount($transaction->getAmount());
        $record->setTransactionVariableSymbol($transaction->getVariableSymbol());
        $record->setTransactionPerformingDateTime($transaction->getDate());
        $record->setTransactionComment($transaction->getComment());
        $record->setTransactionId($transaction->getId());
        $record->setTransactionSenderAccountNum($transaction->getSenderAccountNumber());
        $record->setTransactionSenderBankCode($transaction->getSenderBankCode());

        return $record;
    }

    /**
     * Initializes given list by given models
     * @param TransactionUploadList $list given list to be used
     * @param array $models given models
     * @return TransactionUploadList initialized list
     */
    private function initTransactionUploadList(TransactionUploadListInterface $list, array $models)
    {
        foreach ($models as $model)
        {
            /** @var $model TransactionalRecordInterface */
            if ($model instanceof TransactionalRecordInterface)
            {
                // TODO: check if transaction would be domestic or not
                $transaction = new components\transactions\DomesticTransaction();

                //$transaction->setAccountFrom($model->getTransactionSenderAccountNum());
                $transaction->setAccountFrom('2700587809');

                $transaction->setCurrency(components\transactions\DomesticTransaction::CURRENCY_CZ);

                if ($this->test)
                {
                    $transaction->setAmount(1);
                }
                else
                {
                    $transaction->setAmount($model->getTransactionAmount());
                }

                //$transaction->setAccountTo($model->getTransactionRecipientAccountNum());
                $transaction->setAccountTo(2600742963);

                $transaction->setBankCode($model->getTransactionRecipientBankCode());
                //$transaction->setVariableSymbol($model->getTransactionVariableSymbol());
                $transaction->setDate(new \DateTime('NOW'));
                //$transaction->setPaymentType(components\transactions\DomesticTransaction::TYPE_STANDARD);

                if ($model->setTransactionAsReadyToPay() && $model->save())
                {
                    $list->addTransaction($transaction);
                }
            }
        }

        return $list;
    }

    /**
     * Sets given transactional records as processed
     * @param array $records
     * @param int $instruction bank instruction id
     */
    private function setTransactionalRecordsAsProcessed(array $records, $instruction)
    {
        foreach ($records as $record)
        {
            if ($record instanceof TransactionalRecordInterface)
            {
                $record->setTransactionAsProcessed($instruction) && $record->save();
            }
        }
    }
}