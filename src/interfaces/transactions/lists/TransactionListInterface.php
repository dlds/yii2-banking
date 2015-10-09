<?php

namespace dlds\banking\interfaces\transactions\lists;

use dlds\banking\interfaces\transactions\TransactionInterface;

interface TransactionListInterface {

    /**
     * Indicates if list holds at least one transaction
     */
    public function hasTransactions();

    /**
     * Retrieves all transactions in list
     */
    public function getTransactions();

    /**
     * Adds new transaction
     * @param $transaction given transaction
     */
    public function addTransaction(TransactionInterface $transaction);
}