<?php

namespace dlds\banking\adapters\fio\exceptions;

use dlds\banking\interfaces\BankingExceptionInterface;

class MissingTokenException extends \UnexpectedValueException implements BankingExceptionInterface {

}