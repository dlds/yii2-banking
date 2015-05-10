<?php

namespace dlds\banking\interfaces;

use dlds\banking\interfaces\TransactionListInterface;
use dlds\banking\interfaces\TransactionTemplateInterface;

interface AdapterInterface {

    /**
     * Enrolls all incoming transactions in given list into DB
     * @param TransactionListInterface $list given list
     * @param TransactionTemplateInterface $template given model
     * to be used as active record template
     */
    public function enrollIncomings(TransactionListInterface $list, TransactionTemplateInterface $template);

    /**
     * Downloads new transactions from bank server
     * automatically detecs undownloded transactions
     * by set stop on bank server
     * @return \dlds\banking\interfaces\TransactionListInterface list
     */
    public function downloadTransactions();

    /**
     * Download all transaction since given datetime
     * @param \DateTime $datetime given date time
     * @return \dlds\banking\interfaces\TransactionListInterface list
     */
    public function downloadTransactionsSince(\DateTime $datetime);

    /**
     * Uploads transactions list onto bank server to be processed
     * @param TransactionListInterface $list given list
     * @return boolen TRUE on success, FALSE on failure
     */
    public function uploadTransactionsList(TransactionListInterface $list);
}