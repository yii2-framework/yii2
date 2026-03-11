<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yiiunit\framework\console\providers;

/**
 * Data provider for {@see \yiiunit\framework\console\ControllerTest} test cases.
 *
 * Provides representative input/output pairs for action argument type resolution.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 2.2
 */
final class ControllerProvider
{
    /**
     * @phpstan-return array<string, array{string, string, string, bool}>
     */
    public static function getActionArgsHelp(): array
    {
        return [
            'builtin bool' => [
                'builtin-types',
                'active',
                'bool',
                true,
            ],
            'builtin float' => [
                'builtin-types',
                'rate',
                'float',
                true,
            ],
            'builtin int' => [
                'builtin-types',
                'count',
                'int',
                true,
            ],
            'builtin string' => [
                'builtin-types',
                'name',
                'string',
                true,
            ],
            'class type' => [
                'class-type',
                'obj',
                'stdClass',
                true,
            ],
            'complex dnf' => [
                'complex-dnf-type',
                'param',
                '(Countable&Iterator)|string',
                true,
            ],
            'dnf type' => [
                'dnf-type',
                'param',
                '(Countable&Iterator)|null',
                false,
            ],
            'intersection type' => [
                'intersection-type',
                'param',
                'Countable&Iterator',
                true,
            ],
            'mixed type' => [
                'mixed-type',
                'param',
                'mixed',
                true,
            ],
            'no type with default' => [
                'no-type-with-default',
                'param',
                'integer',
                false,
            ],
            'nullable type' => [
                'nullable-type',
                'param',
                'int|null',
                false,
            ],
            'union type' => [
                'union-type',
                'param',
                'string|int',
                true,
            ],
        ];
    }
}
