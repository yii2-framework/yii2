<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yiiunit\data\helpers;

use yii\helpers\Html;

/**
 * Stub class for testing Html helper.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 2.2
 */
final class MyHtml extends Html
{
    /**
     * {@inheritdoc}
     */
    protected static function setActivePlaceholder($model, $attribute, &$options = [])
    {
        if (isset($options['placeholder']) && $options['placeholder'] === true) {
            $attribute = static::getAttributeName($attribute);
            $options['placeholder'] = 'My placeholder: ' . $model->getAttributeLabel($attribute);
        }
    }
}
