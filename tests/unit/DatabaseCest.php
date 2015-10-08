<?php
use \UnitTester;

class DatabaseCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function tryToTest(UnitTester $I)
    {
        $connection = \Yii::$app->getDb();

        $I->assertNotEmpty($connection);
    }
}