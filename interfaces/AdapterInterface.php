<?php

namespace dlds\banking\interfaces;

interface AdapterInterface {

    public function downloadTransactionsLast();
    public function downloadTransactionsSince($datetime);

    public function import();
}