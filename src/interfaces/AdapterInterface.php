<?php

namespace dlds\banking\interfaces;

use dlds\banking\interfaces\transactions\TransactionalRecordInterface;
use dlds\banking\interfaces\transactions\lists\TransactionDownloadListInterface;

interface AdapterInterface {

    /**
     * Downloads new transactions from bank server
     * automatically detecs undownloded transactions
     * by set stop on bank server
     * @return TransactionDownloadListInterface list
     */
    public function downloadTransactions();

    /**
     * Download all transactions since given datetime
     * @param \DateTime $datetime given date time
     * @return TransactionDownloadListInterface list
     */
    public function downloadTransactionsSince(\DateTime $datetime);

    /**
     * Download all transactions in "from" - "to" bounds
     * @param \DateTime $from given date time from
     * @param \DateTime $to given date time to
     * @return TransactionDownloadListInterface list
     */
    public function downloadTransactionsFromTo(\DateTime $from, \DateTime $to);

    /**
     * Enrolls all incoming transactions in given list into DB
     * @param TransactionDownloadListInterface $list given list
     * @param TransactionalRecordInterface $record given model
     * to be used as active record template
     */
    public function enrollIncomings(TransactionDownloadListInterface $list, TransactionalRecordInterface $record);

    /**
     * Uploads transactions list onto bank server to be processed
     * @param array $models given models
     * @return boolen TRUE on success, FALSE on failure
     */
    public function uploadTransactions(array $models);
}