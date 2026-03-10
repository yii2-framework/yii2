<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\data\base;

use yii\base\Component;
use yii\base\Event;

/**
 * @property mixed $Text
 * @property mixed $text
 * @property-read self $object
 * @property-read callable $execute
 * @property-read array<array-key, mixed> $items
 * @property-write mixed $writeOnly
 *
 * @mixin NewBehavior We use `mixin` here to avoid PHPStan errors when testing `attachBehavior`.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 2.2
 */
final class NewComponent extends Component
{
    private $_object = null;
    private $_text = 'default';
    private $_items = [];
    public $content;

    public $eventHandled = false;
    public $event;
    public $behaviorCalled = false;

    public function getText()
    {
        return $this->_text;
    }

    public function setText($value): void
    {
        $this->_text = $value;
    }

    public function getObject()
    {
        if (!$this->_object) {
            $this->_object = new self();
            $this->_object->_text = 'object text';
        }

        return $this->_object;
    }

    public function getExecute()
    {
        return function ($param) {
            return $param * 2;
        };
    }

    public function getItems()
    {
        return $this->_items;
    }

    public function myEventHandler($event): void
    {
        $this->eventHandled = true;
        $this->event = $event;
    }

    public function raiseEvent(): void
    {
        $this->trigger('click', new Event());
    }

    public function setWriteOnly()
    {
    }
}
