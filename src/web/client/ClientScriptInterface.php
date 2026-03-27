<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\web\client;

use yii\base\BaseObject;
use yii\web\View;

/**
 * Defines the contract for client-side widget script strategies.
 *
 * Implementations provide JavaScript initialization code and client options for a specific JS framework (for example,
 * jQuery).
 *
 * This allows decoupling widgets from any particular client-side library.
 *
 * @template T of BaseObject
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 2.2
 */
interface ClientScriptInterface
{
    /**
     * Returns the client-side options for the given widget.
     *
     * @param T $widget The widget instance.
     * @param array $params Additional parameters.
     *
     * @return array The client-side options.
     */
    public function getClientOptions(BaseObject $widget, array $params = []): array;

    /**
     * Registers the JavaScript code needed for the widget.
     *
     * @param T $widget The widget instance.
     * @param View $view The view object.
     * @param array $params Additional parameters.
     */
    public function register(BaseObject $widget, View $view, array $params = []): void;
}
