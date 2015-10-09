<?php

namespace dlds\banking\adapters\fio\components\transactions;

use dlds\banking\adapters\fio\components\Transaction;

class DomesticTransaction extends Transaction {

    public function __construct()
    {
        $this->params = array_fill_keys(array('accountFrom', 'currency', 'amount', 'accountTo', 'bankCode', 'ks', 'vs', 'ss', 'date', 'messageForRecipient', 'comment', 'paymentReason', 'paymentType'), null);
    }

    /**
     * Set bank code of a sender account
     * @param int $value Bank code
     * @return \FioApi\DomesticTransaction
     */
    public function setAccountFrom($value)
    {
        $this->params['accountFrom'] = $value;
        return parent::setAccountFrom($value);
    }

    /**
     * Set currency
     * @param string $value currency
     * @return \FioApi\DomesticTransaction
     */
    public function setCurrency($value)
    {
        $this->params['currency'] = $value;
        return parent::setCurrency($value);
    }

    /**
     * Set amount
     * @param float $value amount
     * @return \FioApi\DomesticTransaction
     */
    public function setAmount($value)
    {
        $this->params['amount'] = $value;
        return parent::setAmount($value);
    }

    /**
     * Set account to
     * @param int $value account to
     * @return \FioApi\DomesticTransaction
     */
    public function setAccountTo($value)
    {
        $this->params['accountTo'] = $value;
        return parent::setAccountTo($value);
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
     * @param \DateTime $value
     * @return \FioApi\Transaction
     */
    public function setDate(\DateTime $value)
    {
        $value->setTimezone(new \DateTimeZone(self::TIMEZONE));
        $this->params['date'] = $value->format('Y-m-d');
        return parent::setDate($value);
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