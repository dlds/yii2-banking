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
     * @return \dlds\payment\services\payment\AdapterInterface
     * or FALSE if addapter does not exist
     */
    public function getAdapter($key)
    {
        $adapter = ArrayHelper::getValue($this->adapters, $key);

        if ($adapter)
        {
            return \Yii::createObject($adapter);
        }

        return false;
    }
}