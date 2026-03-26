<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\web;

/**
 * Provides the base asset registration target for the Yii Framework JavaScript layer.
 *
 * This class is intentionally empty. Install `yii2-framework/jquery` and register
 * {@see \yii\jquery\Bootstrap} to activate jQuery support, which overrides this bundle
 * via `AssetManager::$bundles` to load `yii.js` with a jQuery dependency.
 *
 * @since 2.0
 */
class YiiAsset extends AssetBundle
{
    public $sourcePath = null;
    public $js = [];
    public $depends = [];
}
