<?php

namespace dlds\banking\handlers;

use dlds\banking\interfaces\TransactionInterface;
use dlds\banking\interfaces\TransactionListInterface;

abstract class TransactionsListHandler {

    /**
     * Enrolls transaction list and write all transactions into DB
     * @param BankTransactionListInterface $list given list to be enrolled
     * @param BankTransactionInterface $model given transaction model to be used
     * for writing transaction into DB
     * @return
     */
    public static function enroll(TransactionListInterface $list, TransactionInterface $model)
    {
        foreach ($list->getTransactions() as $transaction)
        {
            $model = clone $template;

            if ($transaction->getAmount() > 0)
            {
                $model->amount = $transaction->getAmount();
                $model->symbol = $transaction->getVariableSymbol();

                $date = $transaction->getDate();

                if ($date instanceof \DateTime)
                {
                    $model->performed_at = $date->getTimestamp();
                }
                else
                {
                    $model->performed_at = time();
                }

                $model->transaction_id = $transaction->getId();
                $model->note = $transaction->getComment();

                if ($model->save())
                {
                    $count++;
                }
            }
        }
    }
}