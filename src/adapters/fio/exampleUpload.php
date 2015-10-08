<?php
header('Content-Type: text/html; charset=utf-8');

require_once 'fioapi.class.php';
require_once 'fioapiupload.class.php';


try {
    $fioapi = new FioApi\FioApiUpload('TOKEN', __DIR__.'/certificate.pem');
    
    $transaction=new FioApi\DomesticTransaction();
    $transaction->setAccountFrom(2900072500)
                ->setAccountTo(2900072746)
                ->setBankCode(2010)
                ->setAmount(200)
                ->setCurrency('CZK')
                ->setDate(new Datetime());
    
    $result=$fioapi->addTransaction($transaction)->upload();
 
    print_r($result);
   
} catch (FioApi\FioApiException $e) {
    echo 'Exception '.$e->getCode().': '.$e->getMessage();
    print_r($e->getTransactions());
}

?>