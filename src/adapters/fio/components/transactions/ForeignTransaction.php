<?php

namespace dlds\banking\adapters\fio\components\transactions;

use dlds\banking\adapters\fio\components\Transaction;

class ForeignTransaction extends Transaction {

    public function __construct()
    {
        $this->params = array_fill_keys(array('accountFrom', 'currency', 'amount', 'accountTo', 'bic', 'date', 'comment', 'benefName', 'benefStreet', 'benefCity', 'benefCountry', 'remittanceInfo1', 'remittanceInfo2', 'remittanceInfo3', 'remittanceInfo4', 'detailsOfCharges', 'paymentReason'), null);
    }

    /**
     * Set your account number
     * @param int $value Your account number
     * @return \FioApi\ForeignTransaction
     */
    public function setAccountFrom($value)
    {
        return $this->_setAccountFrom($value);
    }

    /**
     * Set currency of transaction
     * @param string $value Currency code (standard ISO 4217; for example CZK)
     * @return \FioApi\ForeignTransaction
     */
    public function setCurrency($value)
    {
        return $this->_setCurrency($value);
    }

    /**
     * Set amount
     * @param float $value Amount
     * @return \FioApi\ForeignTransaction
     */
    public function setAmount($value)
    {
        return $this->_setAmount($value);
    }

    /**
     * Set number of a beneficiary's account.
     * @param string/int $value Beneficiary's account number
     * @return \FioApi\ForeignTransaction
     */
    public function setAccountTo($value)
    {
        return $this->_setAccountTo($value);
    }

    /**
     * Set date of transaction
     * @param \DateTime $value Date of transaction (today or in a future)
     * @return \FioApi\ForeignTransaction
     */
    public function setDate($value)
    {
        return $this->_setDate($value);
    }

    /**
     * Set your comment of this transaction.
     * @param string $value Your comment
     * @return \FioApi\ForeignTransaction
     */
    public function setComment($value)
    {
        return $this->_setComment($value);
    }

    /**
     * Set payment reason.
     * Check it in FIO API manual: http://www.fio.cz/docs/cz/API_Bankovnictvi.pdf
     * @param int $value Payment reason
     * @return \FioApi\ForeignTransaction
     */
    public function setPaymentReason($value)
    {
        return $this->_setPaymentReason($value);
    }

    /**
     * Set Bank Identifier Code
     * @param string $value BIC (standard ISO 9362)
     * @return \FioApi\ForeignTransaction
     */
    public function setBic($value)
    {
        $this->params['bic'] = $value;
        return $this;
    }

    /**
     * Set beneficiary's name.
     * @param string $value Beneficiary's name
     * @return \FioApi\ForeignTransaction
     */
    public function setBenefName($value)
    {
        $this->params['benefName'] = $value;
        return $this;
    }

    /**
     * Set beneficiary's street.
     * @param string $value Beneficiary's street
     * @return \FioApi\ForeignTransaction
     */
    public function setBenefStreet($value)
    {
        $this->params['benefStreet'] = $value;
        return $this;
    }

    /**
     * Set beneficiary's city.
     * @param string $value Beneficiary's city
     * @return \FioApi\ForeignTransaction
     */
    public function setBenefCity($value)
    {
        $this->params['benefCity'] = $value;
        return $this;
    }

    /**
     * Set beneficiary's country.
     * @param string $value Beneficiary's country
     * @return \FioApi\ForeignTransaction
     */
    public function setBenefCountry($value)
    {
        $this->params['benefCountry'] = $value;
        return $this;
    }

    /**
     * Set information for beneficiary.
     * @param string $value Information for beneficiary
     * @return \FioApi\ForeignTransaction
     */
    public function setRemittanceInfo1($value)
    {
        $this->params['remittanceInfo1'] = $value;
        return $this;
    }

    /**
     * Set information for beneficiary.
     * @param string $value Information for beneficiary
     * @return \FioApi\ForeignTransaction
     */
    public function setRemittanceInfo2($value)
    {
        $this->params['remittanceInfo2'] = $value;
        return $this;
    }

    /**
     * Set information for beneficiary.
     * @param string $value Information for beneficiary
     * @return \FioApi\ForeignTransaction
     */
    public function setRemittanceInfo3($value)
    {
        $this->params['remittanceInfo3'] = $value;
        return $this;
    }

    /**
     * Set information for beneficiary.
     * @param string $value Information for beneficiary
     * @return \FioApi\ForeignTransaction
     */
    public function setRemittanceInfo4($value)
    {
        $this->params['remittanceInfo4'] = $value;
        return $this;
    }

    /**
     * Set details of charges:
     *  - 470501 - all you
     *  - 470502 - all beneficiary
     *  - 470503 - everyone
     * @param int $value Selected number
     * @return \FioApi\ForeignTransaction
     */
    public function setDetailsOfCharges($value)
    {
        $this->params['detailsOfCharges'] = $value;
        return $this;
    }
}