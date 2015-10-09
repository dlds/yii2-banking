<?php

namespace dlds\banking\adapters\fio\components\transactions\lists;

use dlds\banking\interfaces\transactions\TransactionInterface;
use dlds\banking\interfaces\transactions\lists\TransactionListInterface;

class TransactionList implements TransactionListInterface {

    /**
     * @var TransactionInterface[] held transactions
     */
    protected $transactions = [];

    /**
     * Indicates if list holds at least one transaction
     * @return boolean
     */
    public function hasTransactions()
    {
        return !empty($this->transactions);
    }

    /**
     * Adds new transactions to current array
     * @param TransactionInterface $transaction
     */
    public function addTransaction(TransactionInterface $transaction)
    {
        $this->transactions[] = $transaction;
    }

    /**
     * Retrieves all held transactions
     * @return Transaction[]
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
}