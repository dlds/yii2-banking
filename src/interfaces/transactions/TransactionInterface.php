<?php

namespace dlds\banking\interfaces\transactions;

interface TransactionInterface {

    /**
     * Retrieves transaction unique ID
     */
    public function getId();

    /**
     * Retrieves transaction performing date
     */
    public function getDate();

    /**
     * Retrieves transaction amount
     */
    public function getAmount();

    /**
     * Retrieves transaction currency
     */
    public function getCurrency();

    /**
     * Retrieves transaction sender account number
     */
    public function getSenderAccountNumber();

    /**
     * Retrieves transaction sender bank code
     */
    public function getSenderBankCode();

    /**
     * Retrieves transctions sender bank name
     */
    public function getSenderBankName();

    /**
     * Retrieves transaction constant symbol
     */
    public function getConstantSymbol();

    /**
     * Retrieves transaction variable symbol
     */
    public function getVariableSymbol();

    /**
     * Retrieves transaction specific symbol
     */
    public function getSpecificSymbol();

    /**
     * Retrieves transaction user identity info
     */
    public function getUserIdentity();

    /**
     * Retrieves transaction user message
     */
    public function getUserMessage();

    /**
     * Retrieves transaction type
     */
    public function getTransactionType();

    /**
     * Retrieves identity the transaction was performed by
     */
    public function getPerformedBy();

    /**
     * Retrieves transaction comment
     */
    public function getComment();

    /**
     * Retrieves transaction order id
     */
    public function getPaymentOrderId();
}