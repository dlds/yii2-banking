<?php

namespace dlds\banking\interfaces;

interface TransactionTemplateInterface {

    /**
     * Retrieves transaction id
     */
    public function getTransactionId();

    /**
     * Retrieves transaction amount
     */
    public function getTransactionAmount();

    /**
     * Retrieves transaction variable symbol
     */
    public function getTransactionVariableSymbol();

    /**
     * Retrieves transaction performing date
     */
    public function getTransactionPerformingDateTime();

    /**
     * Retrieves transaction sender account num
     */
    public function getTransactionSenderAccountNum();

    /**
     * Retrieves transaction sender bank code
     */
    public function getTransactionSenderBankCode();

    /**
     * Retrieves transaction comment
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
     * @param float $amount given amount
     */
    public function setTransactionPerformingDateTime($date);

    /**
     * Sets transaction variable symbol
     * @param int $symbol
     */
    public function setTransactionVariableSymbol($symbol);

    /**
     * Sets transaction sender account num
     */
    public function setTransactionSenderAccountNum($num);

    /**
     * Sets transaction sender bank code
     */
    public function setTransactionSenderBankCode($code);

    /**
     * Sets transaction comment
     * @param string $comment given comment
     */
    public function setTransactionComment($comment);
}