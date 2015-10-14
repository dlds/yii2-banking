<?php

namespace dlds\banking\interfaces\transactions;

interface TransactionalRecordInterface {

    /**
     * Retrieves transaction id
     * @return int id
     */
    public function getTransactionId();

    /**
     * Retrieves transaction amount
     * @return float amount
     */
    public function getTransactionAmount();

    /**
     * Retrieves transaction variable symbol
     * @return string variable symbol
     */
    public function getTransactionVariableSymbol();

    /**
     * Retrieves transaction performing date
     * @return int timestamp
     */
    public function getTransactionPerformingDateTime();

    /**
     * Retrieves transaction sender account num
     * @return int sender account number
     */
    public function getTransactionSenderAccountNum();

    /**
     * Retrieves transaction sender bank code
     * @return int sender account bank code
     */
    public function getTransactionSenderBankCode();

    /**
     * Retrieves transaction recipient account num
     * @return int recipient account number
     */
    public function getTransactionRecipientAccountNum();

    /**
     * Retrieves transaction recipient bank code
     * @return int recipient account bank code
     */
    public function getTransactionRecipientBankCode();

    /**
     * Retrieves transaction comment
     * @return string transaction comment
     */
    public function getTransactionComment();

    /**
     * Sets transaction id
     * @param int $id given id
     */
    public function setTransactionId($id);

    /**
     * Sets transaction amount
     * @param float $amount given amount
     */
    public function setTransactionAmount($amount);

    /**
     * Sets transaction performed date
     * @param int $timestamp given timestamp
     */
    public function setTransactionPerformingDateTime($timestamp);

    /**
     * Sets transaction variable symbol
     * @param string $symbol
     */
    public function setTransactionVariableSymbol($symbol);

    /**
     * Sets transaction sender account num
     * @param int $num account number
     */
    public function setTransactionSenderAccountNum($num);

    /**
     * Sets transaction sender bank code
     * @param int $code bank code
     */
    public function setTransactionSenderBankCode($code);

    /**
     * Sets transaction recipient account num
     * @param int $num account number
     */
    public function setTransactionRecipientAccountNum($num);

    /**
     * Sets transaction recipient bank code
     * @param int $code bank code
     */
    public function setTransactionRecipientBankCode($code);

    /**
     * Sets transaction comment
     * @param string $comment given comment
     */
    public function setTransactionComment($comment);

    /**
     * Sets transaction as ready to pay
     */
    public function setTransactionAsReadyToPay();

    /**
     * Sets transaction as processed
     * @param int $instruction bank instruction id
     */
    public function setTransactionAsProcessed($insruction);

    /**
     * Sets transaction as paid
     * @param int $timestamp given performing date
     */
    public function setTransactionAsPaid($timestamp);

    /**
     * Sets transaction as cancelled
     */
    public function setTransactionAsCancelled();

    /**
     * Finds transaction by transaction id
     * @param int $transactionId given transaction id
     * @return TransactionalInterface transaction
     */
    public function findByTransactionId($transactionId);
}