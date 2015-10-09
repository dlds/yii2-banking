<?php

namespace dlds\banking\interfaces;

interface AccountInterface {

    /**
     * Retrieves account number
     */
    public function getAccountNumber();

    /**
     * Retrieves bank code
     */
    public function getBankCode();

    /**
     * Retrieves account currency
     */
    public function getCurrency();

    /**
     * Retrieves account IBAN
     */
    public function getIban();

    /**
     * Retrieves account BIC
     */
    public function getBic();
}