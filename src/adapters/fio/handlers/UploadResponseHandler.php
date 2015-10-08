<?php

namespace dlds\banking\adapters\fio\handlers;

class UploadResponseHandler {

    public static function parseXmlResponse($response)
    {
        $xml = simplexml_load_string($xml);

        $transactions = array();
        $transactions['status'] = (string) $xml->result->status;
        if (isset($xml->result->message))
        {
            $transactions['message'] = (string) $xml->result->message;
        }
        if (isset($xml->ordersDetails))
        {
            foreach ($xml->ordersDetails->children() as $ch)
            {
                $ch2 = $ch->messages->children();
                $transactions['transactions'][] = array('status' => (string) $ch2[0]->attributes()->status, 'message' => (string) $ch2[0]);
            }
        }

        if ($this->exceptionOnError and $transactions['status'] == 'error')
        {
            throw new FioApiException('Error in transaction(s). See $e->getTransactions();', 3, $transactions);
        }
        if ($this->exceptionOnWarning and $transactions['status'] == 'warning')
        {
            throw new FioApiException('Warning in transaction(s). All transactions have been uploaded! See $e->getTransactions();', 4, $transactions);
        }

        return $transactions;
    }
}