<?php

namespace dlds\banking\adapters\fio\components\transactions;

use dlds\banking\adapters\fio\components\Transaction;

class Target2Transaction extends Transaction {

    public function __construct()
    {
        $this->params = array_fill_keys(array('accountFrom', 'currency', 'amount', 'accountTo', 'ks', 'vs', 'ss', 'bic', 'date', 'comment', 'benefName', 'benefStreet', 'benefCity', 'benefCountry', 'remittanceInfo1', 'remittanceInfo2', 'remittanceInfo3', 'paymentReason', 'paymentType'), null);
    }

    /**
     * Set your account number
     * @param int $value Your account number
     * @return \FioApi\Target2Transaction
     */
    public function setAccountFrom($value)
    {
        return $this->_setAccountFrom($value);
    }

    /**
     * Set currency of transaction
     * @param string $value Currency code (standard ISO 4217; for example CZK)
     * @return \FioApi\Target2Transaction
     */
    public function setCurrency($value)
    {
        return $this->_setCurrency($value);
    }

    /**
     * Set amount.
     * @param float $value Amount
     * @return \FioApi\Target2Transaction
     */
    public function setAmount($value)
    {
        return $this->_setAmount($value);
    }

    /**
     * Set number of a beneficiary's account.
     * @param string/int $value Beneficiary's account number
     * @return \FioApi\Target2Transaction
     */
    public function setAccountTo($value)
    {
        return $this->_setAccountTo($value);
    }

    /**
     * Set date of transaction
     * @param \DateTime $value Date of transaction (today or in a future)
     * @return \FioApi\Target2Transaction
     */
    public function setDate($value)
    {
        return $this->_setDate($value);
    }

    /**
     * Set your comment of this transaction.
     * @param string $value Your Comment
     * @return \FioApi\Target2Transaction
     */
    public function setComment($value)
    {
        return $this->_setComment($value);
    }

    /**
     * Set payment reason.
     * Check it in FIO API manual: http://www.fio.cz/docs/cz/API_Bankovnictvi.pdf
     * @param int $value Payment reason
     * @return \FioApi\Target2Transaction
     */
    public function setPaymentReason($value)
    {
        return $this->_setPaymentReason($value);
    }

    /**
     * Set variable symbol.
     * @param int $value Variable symbol (for example 1234567890)
     * @return \FioApi\Target2Transaction
     */
    public function setVariableSymbol($value)
    {
        $this->params['vs'] = $value;
        return $this;
    }

    /**
     * Set constant symbol.
     * @param int $value Constant symbol (for example 0558)
     * @return \FioApi\Target2Transaction
     */
    public function setConstantSymbol($value)
    {
        $this->params['ks'] = $value;
        return $this;
    }

    /**
     * Set specific symbol.
     * @param int $value Specifi symbol (for example 1234567890)
     * @return \FioApi\Target2Transaction
     */
    public function setSpecificSymbol($value)
    {
        $this->params['ss'] = $value;
        return $this;
    }

    /**
     * Set Bank Identifier Code
     * @param string $value BIC (standard ISO 9362)
     * @return \FioApi\Target2Transaction
     */
    public function setBic($value)
    {
        $this->params['bic'] = $value;
        return $this;
    }

    /**
     * Set beneficiary's name.
     * @param string $value Beneficiary's name
     * @return \FioApi\Target2Transaction
     */
    public function setBenefName($value)
    {
        $this->params['benefName'] = $value;
        return $this;
    }

    /**
     * Set beneficiary's street.
     * @param string $value Beneficiary's street
     * @return \FioApi\Target2Transaction
     */
    public function setBenefStreet($value)
    {
        $this->params['benefStreet'] = $value;
        return $this;
    }

    /**
     * Set beneficiary's city.
     * @param string $value Beneficiary's city
     * @return \FioApi\Target2Transaction
     */
    public function setBenefCity($value)
    {
        $this->params['benefCity'] = $value;
        return $this;
    }

    /**
     * Set beneficiary's country.
     * @param string $value Beneficiary's country
     * @return \FioApi\Target2Transaction
     */
    public function setBenefCountry($value)
    {
        $this->params['benefCountry'] = $value;
        return $this;
    }

    /**
     * Set information for beneficiary.
     * @param string $value Information for beneficiary
     * @return \FioApi\Target2Transaction
     */
    public function setRemittanceInfo1($value)
    {
        $this->params['remittanceInfo1'] = $value;
        return $this;
    }

    /**
     * Set information for beneficiary.
     * @param string $value Information for beneficiary
     * @return \FioApi\Target2Transaction
     */
    public function setRemittanceInfo2($value)
    {
        $this->params['remittanceInfo2'] = $value;
        return $this;
    }

    /**
     * Set information for beneficiary.
     * @param string $value Information for beneficiary
     * @return \FioApi\Target2Transaction
     */
    public function setRemittanceInfo3($value)
    {
        $this->params['remittanceInfo3'] = $value;
        return $this;
    }

    /**
     * Set payment type:
     *  - 431008 - standard
     *  - 431009 - priority
     * @param int $value Payment type
     * @return \FioApi\Target2Transaction
     */
    public function setPaymentType($value)
    {
        $this->params['paymentType'] = $value;
        return $this;
    }
}