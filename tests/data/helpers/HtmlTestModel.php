<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yiiunit\data\helpers;

use yii\base\DynamicModel;

/**
 * Stub model for testing Html helper.
 *
 * @property string $name
 * @property string $title
 * @property string $alias
 * @property array $types
 * @property string $description
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 2.2
 */
final class HtmlTestModel extends DynamicModel
{
    public function init(): void
    {
        $attributes = [
            'name',
            'title',
            'alias',
            'types',
            'description',
            'radio',
            'checkbox'
        ];

        foreach ($attributes as $attribute) {
            $this->defineAttribute($attribute);
        }
    }

    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 100],
            ['title', 'string', 'length' => 10],
            ['alias', 'string', 'length' => [0, 20]],
            ['description', 'string', 'max' => 500],
            [['radio', 'checkbox'], 'boolean'],
        ];
    }

    public function customError()
    {
        return 'this is custom error message';
    }
}
