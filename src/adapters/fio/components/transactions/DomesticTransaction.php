<?php

namespace dlds\banking\adapters\fio\components\transactions;

use dlds\banking\adapters\fio\components\Transaction;

class DomesticTransaction extends Transaction {

    public function __construct()
    {
        $this->params = array_fill_keys(array('accountFrom', 'currency', 'amount', 'accountTo', 'bankCode', 'ks', 'vs', 'ss', 'date', 'messageForRecipient', 'comment', 'paymentReason', 'paymentType'), null);
    }

    /**
     * Set your account number
     * @param int $value Your account number
     * @return \FioApi\DomesticTransaction
     */
    public function setAccountFrom($value)
    {
        return $this->_setAccountFrom($value);
    }

    /**
     * Set currency of transaction
     * @param string $value Currency code (standard ISO 4217; for example CZK)
     * @return \FioApi\DomesticTransaction
     */
    public function setCurrency($value)
    {
        return $this->_setCurrency($value);
    }

    /**
     * Set amount.
     * @param float $value Amount
     * @return \FioApi\DomesticTransaction
     */
    public function setAmount($value)
    {
        return $this->_setAmount($value);
    }

    /**
     * Set number of a beneficiary's account.
     * @param string/int $value Beneficiary's account number
     * @return \FioApi\DomesticTransaction
     */
    public function setAccountTo($value)
    {
        return $this->_setAccountTo($value);
    }

    /**
     * Set date of transaction
     * @param \DateTime $value Date of transaction (today or in a future)
     * @return \FioApi\DomesticTransaction
     */
    public function setDate($value)
    {
        return $this->_setDate($value);
    }

    /**
     * Set your comment of this transaction.
     * @param string $value Your comment
     * @return \FioApi\DomesticTransaction
     */
    public function setComment($value)
    {
        return $this->_setComment($value);
    }

    /**
     * Set payment reason.
     * Check it in FIO API manual: http://www.fio.cz/docs/cz/API_Bankovnictvi.pdf
     * @param int $value Payment reason
     * @return \FioApi\DomesticTransaction
     */
    public function setPaymentReason($value)
    {
        return $this->_setPaymentReason($value);
    }

    /**
     * Set bank code of a beneficiary's account.
     * @param int $value Bank code of a beneficiary's account (for example: 2010)
     * @return \FioApi\DomesticTransaction
     */
    public function setBankCode($value)
    {
        $this->params['bankCode'] = $value;
        return $this;
    }

    /**
     * Set variable symbol
     * @param int $value Variable symbol (for example: 1234567890)
     * @return \FioApi\DomesticTransaction
     *
     */
    public function setVariableSymbol($value)
    {
        $this->params['vs'] = $value;
        return $this;
    }

    /**
     * Set constant symbol
     * @param int $value Constant symbol (for example: 0558)
     * @return \FioApi\DomesticTransaction
     */
    public function setConstantSymbol($value)
    {
        $this->params['ks'] = $value;
        return $this;
    }

    /**
     * Set specific symbol.
     * @param int $value Specific symbol (for example: 1234567890)
     * @return \FioApi\DomesticTransaction
     */
    public function setSpecificSymbol($value)
    {
        $this->params['ss'] = $value;
        return $this;
    }

    /**
     * Set message for beneficiary.
     * @param string $value Message
     * @return \FioApi\DomesticTransaction
     */
    public function setMessage($value)
    {
        $this->params['messageForRecipient'] = $value;
        return $this;
    }

    /**
     * Set payment type:
     *  - 431001 - standard
     *  - 431004 - accelerated
     *  - 431005 - priority
     *  - 431022 - collection order
     * @param int $value Payment type
     * @return \FioApi\DomesticTransaction
     */
    public function setPaymentType($value)
    {
        $this->params['paymentType'] = $value;
        return $this;
    }
}