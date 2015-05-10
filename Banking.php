<?php
/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2014 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 */

namespace dlds\banking;

use yii\helpers\ArrayHelper;
use dlds\banking\interfaces\ApiBankAdapterInterface;

/**
 * This is the main class of the dlds\mlm component that should be registered as an application component.
 *
 * @author Jiri Svoboda <jiri.svobodao@dlds.cz>
 * @package mlm
 */
class Banking extends \yii\base\Component {

    /**
     * @var array adapters
     */
    public $adapters;

    /**
     * Retrieves appropriate adapter based on give adapter key
     */
    public function getAdapter($key)
    {
        $adapter = ArrayHelper::getValue($this->adapters, $key);

        if ($adapter)
        {
            var_dump($adapter);
        }

        return false;
    }

    /**
     * Downloads transactions list from banks using module adapters
     * @return type
     */
    public function downloadTransactionsList($adapter)
    {
        $transactions = new adapters\fio\TransactionList;

        foreach ($this->adapters as $key => $adapter)
        {
            if ($adapter instanceof ApiBankAdapterInterface)
            {
                $transactions[$key] = $adapter->downloadTransactionsList();
            }
        }

        return $transactions;
    }
}