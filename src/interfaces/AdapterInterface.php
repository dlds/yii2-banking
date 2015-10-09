<?php

namespace dlds\banking\interfaces;

use dlds\banking\interfaces\transactions\TransactionalRecordInterface;
use dlds\banking\interfaces\transactions\lists\TransactionDownloadListInterface;

interface AdapterInterface {

    /**
     * Enrolls all incoming transactions in given list into DB
     * @param TransactionDownloadListInterface $list given list
     * @param TransactionalRecordInterface $record given model
     * to be used as active record template
     */
    public function enrollIncomings(TransactionDownloadListInterface $list, TransactionalRecordInterface $record);

    /**
     * Downloads new transactions from bank server
     * automatically detecs undownloded transactions
     * by set stop on bank server
     * @return TransactionDownloadListInterface list
     */
    public function downloadTransactions();

    /**
     * Download all transaction since given datetime
     * @param \DateTime $datetime given date time
     * @return TransactionDownloadListInterface list
     */
    public function downloadTransactionsSince(\DateTime $datetime);

    /**
     * Uploads transactions list onto bank server to be processed
     * @param array $models given models
     * @return boolen TRUE on success, FALSE on failure
     */
    public function uploadTransactions(array $models);
}