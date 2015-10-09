<?php

namespace dlds\banking\interfaces\transactions\lists;

interface TransactionDownloadListInterface extends TransactionListInterface {

    /**
     * Creates transaction list from given stdClass
     */
    public static function create(\stdClass $data);

    /**
     * Retrieves account opening balance
     */
    public function getOpeningBalance();

    /**
     * Retrieves account closing balance
     */
    public function getClosingBalance();

    /**
     * Retrieves the oldest date of transaction in list
     */
    public function getDateStart();

    /**
     * Retrieves the newst date of transaction in list
     */
    public function getDateEnd();

    /**
     * Retrieves the oldest id of transaction in list
     */
    public function getIdFrom();

    /**
     * Retrieves the newest id of transaction in list
     */
    public function getIdTo();

    /**
     * Retrieves the id of last downloaded transaction
     */
    public function getIdLastDownload();

    /**
     * Retrieves account info the transactions were downloaded from
     */
    public function getAccount();
}