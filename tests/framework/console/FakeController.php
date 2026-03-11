<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yiiunit\framework\console;

use yii\console\Controller;
use yii\console\Response;

/**
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 2.0
 */
class FakeController extends Controller
{
    public $test;

    public $testArray = [];

    public $alias;

    private static $_wasActionIndexCalled = false;

    public static function getWasActionIndexCalled()
    {
        $wasCalled = self::$_wasActionIndexCalled;
        self::$_wasActionIndexCalled = false;

        return $wasCalled;
    }

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'test',
            'testArray',
            'alias',
        ]);
    }

    public function optionAliases()
    {
        return [
            't' => 'test',
            'ta' => 'testArray',
            'a' => 'alias',
        ];
    }

    public function actionIndex(): void
    {
        self::$_wasActionIndexCalled = true;
    }

    public function actionAksi1($fromParam, $other = 'default')
    {
        return [$fromParam, $other];
    }

    /**
     * @param string $value the string value
     * @return array
     */
    public function actionAksi2(array $values, $value)
    {
        return [$values, $value];
    }

    public function actionAksi3($available, $missing)
    {
    }

    public function actionAksi4()
    {
        return $this->test;
    }

    public function actionAksi5()
    {
        return $this->alias;
    }

    public function actionAksi6()
    {
        return $this->testArray;
    }

    public function actionTrimargs($param1 = null)
    {
        return func_get_args();
    }

    public function actionStatus($status = 0)
    {
        return $status;
    }

    public function actionResponse($status = 0)
    {
        $response = new Response();
        $response->exitStatus = (int) $status;
        return $response;
    }

    public function actionVariadic($foo, $bar, ...$baz)
    {
        return [$foo, $bar, $baz];
    }

    public function actionUnionType(int|string $param)
    {
        return $param;
    }

    public function actionIntersectionType(\Countable&\Iterator $param)
    {
        return $param;
    }

    public function actionDnfType((\Countable&\Iterator)|null $param = null)
    {
        return $param;
    }

    public function actionNullableType(?int $param = null)
    {
        return $param;
    }

    public function actionBuiltinTypes(string $name, int $count, float $rate, bool $active)
    {
        return [$name, $count, $rate, $active];
    }

    public function actionClassType(\stdClass $obj)
    {
        return $obj;
    }

    public function actionMixedType(mixed $param)
    {
        return $param;
    }

    public function actionNoTypeWithDefault($param = 42)
    {
        return $param;
    }

    public function actionComplexDnfType((\Countable&\Iterator)|string $param)
    {
        return $param;
    }
}
