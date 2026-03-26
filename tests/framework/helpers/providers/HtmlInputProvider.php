<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yiiunit\framework\helpers\providers;

/**
 * Data provider for {@see \yiiunit\framework\helpers\HtmlInputTest} test cases.
 *
 * Provides representative input/output pairs for HTML input elements.
 *
 * @copyright Copyright (c) 2008 Yii Software LLC.
 * @license https://www.yiiframework.com/license/
 */
final class HtmlInputProvider
{
    /**
     * @phpstan-return array<string, array{string, string, string|null, array<string, mixed>}>
     */
    public static function textarea(): array
    {
        return [
            'double encode disabled' => [
                <<<HTML
                <textarea name="test">value&lt;&gt;</textarea>
                HTML,
                'test',
                'value&lt;&gt;',
                ['doubleEncode' => false],
            ],
            'double encoded value' => [
                <<<HTML
                <textarea name="test">value&amp;lt;&amp;gt;</textarea>
                HTML,
                'test',
                'value&lt;&gt;',
                [],
            ],
            'encoded value with class' => [
                <<<HTML
                <textarea class="t" name="test">value&lt;&gt;</textarea>
                HTML,
                'test',
                'value<>',
                ['class' => 't'],
            ],
            'null value' => [
                <<<HTML
                <textarea name="test"></textarea>
                HTML,
                'test',
                null,
                [],
            ],
        ];
    }

    /**
     * @phpstan-return array<string, array{string, string, bool, array<string, mixed>}>
     */
    public static function radio(): array
    {
        return [
            'checked with null value' => [
                <<<HTML
                <input type="radio" class="a" name="test" checked>
                HTML,
                'test',
                true,
                [
                    'class' => 'a',
                    'value' => null,
                ],
            ],
            'custom label options' => [
                <<<HTML
                <label class="bbb"><input type="radio" class="a" name="test" checked> ccc</label>
                HTML,
                'test',
                true,
                [
                    'class' => 'a',
                    'value' => null,
                    'label' => 'ccc',
                    'labelOptions' => ['class' => 'bbb'],
                ],
            ],
            'default options' => [
                <<<HTML
                <input type="radio" name="test" value="1">
                HTML,
                'test',
                false,
                [],
            ],
            'disabled state propagation' => [
                <<<HTML
                <input type="hidden" name="test" value="0" disabled><input type="radio" name="test" value="2" disabled>
                HTML,
                'test',
                false,
                [
                    'disabled' => true,
                    'uncheck' => '0',
                    'value' => 2,
                ],
            ],
            'label and uncheck' => [
                <<<HTML
                <input type="hidden" name="test" value="0"><label><input type="radio" class="a" name="test" value="2" checked> ccc</label>
                HTML,
                'test',
                true,
                [
                    'class' => 'a',
                    'uncheck' => '0',
                    'label' => 'ccc',
                    'value' => 2,
                ],
            ],
            'uncheck hidden input' => [
                <<<HTML
                <input type="hidden" name="test" value="0"><input type="radio" class="a" name="test" value="2" checked>
                HTML,
                'test',
                true,
                [
                    'class' => 'a',
                    'uncheck' => '0',
                    'value' => 2,
                ],
            ],
        ];
    }

    /**
     * @phpstan-return array<string, array{string, string, bool, array<string, mixed>}>
     */
    public static function checkbox(): array
    {
        return [
            'checked option override' => [
                <<<HTML
                <input type="hidden" name="test" value="0"><label><input type="checkbox" class="a" name="test" value="2" checked> ccc</label>
                HTML,
                'test',
                false,
                [
                    'class' => 'a',
                    'uncheck' => '0',
                    'label' => 'ccc',
                    'value' => 2,
                    'checked' => true,
                ],
            ],
            'checked with null value' => [
                <<<HTML
                <input type="checkbox" class="a" name="test" checked>
                HTML,
                'test',
                true,
                [
                    'class' => 'a',
                    'value' => null,
                ],
            ],
            'custom form attribute' => [
                <<<HTML
                <input type="hidden" name="test" value="0" form="test-form"><label><input type="checkbox" class="a" name="test" value="2" form="test-form" checked> ccc</label>
                HTML,
                'test',
                true,
                [
                    'class' => 'a',
                    'uncheck' => '0',
                    'label' => 'ccc',
                    'value' => 2,
                    'form' => 'test-form',
                ],
            ],
            'custom label options' => [
                <<<HTML
                <label class="bbb"><input type="checkbox" class="a" name="test" checked> ccc</label>
                HTML,
                'test',
                true,
                [
                    'class' => 'a',
                    'value' => null,
                    'label' => 'ccc',
                    'labelOptions' => ['class' => 'bbb'],
                ],
            ],
            'default options' => [
                <<<HTML
                <input type="checkbox" name="test" value="1">
                HTML,
                'test',
                false,
                [],
            ],
            'disabled state propagation' => [
                <<<HTML
                <input type="hidden" name="test" value="0" disabled><input type="checkbox" name="test" value="2" disabled>
                HTML,
                'test',
                false,
                [
                    'disabled' => true,
                    'uncheck' => '0',
                    'value' => 2,
                ],
            ],
            'label and uncheck' => [
                <<<HTML
                <input type="hidden" name="test" value="0"><label><input type="checkbox" class="a" name="test" value="2" checked> ccc</label>
                HTML,
                'test',
                true,
                [
                    'class' => 'a',
                    'uncheck' => '0',
                    'label' => 'ccc',
                    'value' => 2,
                ],
            ],
            'uncheck hidden input' => [
                <<<HTML
                <input type="hidden" name="test" value="0"><input type="checkbox" class="a" name="test" value="2" checked>
                HTML,
                'test',
                true,
                [
                    'class' => 'a',
                    'uncheck' => '0',
                    'value' => 2,
                ],
            ],
        ];
    }
}
