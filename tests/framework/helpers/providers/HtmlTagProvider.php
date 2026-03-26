<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yiiunit\framework\helpers\providers;

/**
 * Data provider for {@see \yiiunit\framework\helpers\HtmlTagTest} test cases.
 *
 * Provides representative input/output pairs for HTML tag generation methods.
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */
final class HtmlTagProvider
{
    /**
     * @phpstan-return array<string, array{string, string|bool|null, string, array<string, mixed>}>
     */
    public static function tag(): array
    {
        return [
            'div with content' => [
                '<div>content</div>',
                'div',
                'content',
                [],
            ],
            'empty span' => [
                '<span></span>',
                'span',
                '',
                [],
            ],
            'false tag name returns content only' => [
                'test',
                false,
                'test',
                [],
            ],
            'input with encoded value' => [
                '<input type="text" name="test" value="&lt;&gt;">',
                'input',
                '',
                [
                    'type' => 'text',
                    'name' => 'test',
                    'value' => '<>',
                ],
            ],
            'null tag name returns content only' => [
                'test',
                null,
                'test',
                [],
            ],
            'self-closing br' => [
                '<br>',
                'br',
                '',
                [],
            ],
            'span with disabled attribute' => [
                '<span disabled></span>',
                'span',
                '',
                ['disabled' => true],
            ],
        ];
    }
}
