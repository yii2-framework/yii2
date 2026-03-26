<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yiiunit\framework\validators;

use yii\validators\BooleanValidator;
use yii\web\View;
use yiiunit\data\validators\models\FakedValidationModel;
use yiiunit\TestCase;

/**
 * @group validators
 */
class BooleanValidatorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // destroy application, Validator must work without Yii::$app
        $this->destroyApplication();
    }

    public function testValidateValue(): void
    {
        $val = new BooleanValidator();
        $this->assertTrue($val->validate(true));
        $this->assertTrue($val->validate(false));
        $this->assertTrue($val->validate('0'));
        $this->assertTrue($val->validate('1'));
        $this->assertFalse($val->validate('5'));
        $this->assertFalse($val->validate(null));
        $this->assertFalse($val->validate([]));
        $val->strict = true;
        $this->assertTrue($val->validate('0'));
        $this->assertTrue($val->validate('1'));
        $this->assertFalse($val->validate(true));
        $this->assertFalse($val->validate(false));
        $val->trueValue = true;
        $val->falseValue = false;
        $this->assertFalse($val->validate('0'));
        $this->assertFalse($val->validate([]));
        $this->assertTrue($val->validate(true));
        $this->assertTrue($val->validate(false));
    }

    public function testValidateAttributeAndError(): void
    {
        $obj = new FakedValidationModel();
        $obj->attrA = true;
        $obj->attrB = '1';
        $obj->attrC = '0';
        $obj->attrD = [];
        $val = new BooleanValidator();
        $val->validateAttribute($obj, 'attrA');
        $this->assertFalse($obj->hasErrors('attrA'));
        $val->validateAttribute($obj, 'attrC');
        $this->assertFalse($obj->hasErrors('attrC'));
        $val->strict = true;
        $val->validateAttribute($obj, 'attrB');
        $this->assertFalse($obj->hasErrors('attrB'));
        $val->validateAttribute($obj, 'attrD');
        $this->assertTrue($obj->hasErrors('attrD'));
    }

    public function testClientValidateAttributeReturnsNullWithoutJquery(): void
    {
        // Without Yii::$app (no application context), no client script is initialized
        $validator = new BooleanValidator(
            [
                'trueValue' => true,
                'falseValue' => false,
                'strict' => true,
            ],
        );
        $obj = new FakedValidationModel();

        $obj->attrB = '1';

        $this->assertNull(
            $validator->clientValidateAttribute($obj, 'attrB', new ViewStub()),
            "Should return 'null' when jQuery is not available.",
        );
    }
}

class ViewStub extends View
{
    public function registerAssetBundle($name, $position = null)
    {
    }
}
