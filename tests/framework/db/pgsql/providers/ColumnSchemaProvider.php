<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yiiunit\framework\db\pgsql\providers;

use yii\db\Expression;

/**
 * Data provider for {@see \yiiunit\framework\db\pgsql\ColumnSchemaTest} test cases.
 *
 * Provides representative input/output pairs for the `defaultPhpTypecast()` method.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 2.2
 */
final class ColumnSchemaProvider
{
    /**
     * @phpstan-return array<string, array{string, string, string, mixed, mixed}>
     */
    public static function defaultPhpTypecast(): array
    {
        return [
            "binary bit B'10101'" => [
                'integer',
                'bit',
                'integer',
                "B'10101'::bit(5)",
                21,
            ],
            'boolean false' => [
                'boolean',
                'bool',
                'boolean',
                'false',
                false,
            ],
            'boolean true' => [
                'boolean',
                'bool',
                'boolean',
                'true',
                true,
            ],
            'cast notation integer' => [
                'integer',
                'int4',
                'integer',
                "'42'::integer",
                42,
            ],
            'cast notation string' => [
                'string',
                'varchar',
                'string',
                "'hello'::character varying",
                'hello',
            ],
            'complex timezone expression' => [
                'timestamp',
                'timestamp',
                'string',
                "timezone('UTC'::text, '1970-01-01 00:00:00+00'::timestamp with time zone)",
                new Expression("timezone('UTC'::text, '1970-01-01 00:00:00+00'::timestamp with time zone)"),
            ],
            'CURRENT_DATE on date' => [
                'date',
                'date',
                'string',
                'CURRENT_DATE',
                new Expression('CURRENT_DATE'),
            ],
            'CURRENT_TIME on time' => [
                'time',
                'time',
                'string',
                'CURRENT_TIME',
                new Expression('CURRENT_TIME'),
            ],
            'CURRENT_TIMESTAMP on timestamp' => [
                'timestamp',
                'timestamp',
                'string',
                'CURRENT_TIMESTAMP',
                new Expression('CURRENT_TIMESTAMP'),
            ],
            'now() on timestamp' => [
                'timestamp',
                'timestamp',
                'string',
                'now()',
                new Expression('now()'),
            ],
            'NOW() on timestamp' => [
                'timestamp',
                'timestamp',
                'string',
                'NOW()',
                new Expression('NOW()'),
            ],
            'null returns null' => [
                'timestamp',
                'timestamp',
                'string',
                null,
                null,
            ],
            'parenthesized NULL' => [
                'string',
                'varchar',
                'string',
                '(NULL)::character varying',
                null,
            ],
            'parenthesized numeric' => [
                'decimal',
                'numeric',
                'string',
                '(0)::numeric',
                '0',
            ],
            'quoted bit' => [
                'integer',
                'bit',
                'integer',
                "'101'::\"bit\"",
                5,
            ],
            'regular integer' => [
                'integer',
                'int4',
                'integer',
                '42',
                42,
            ],
            'regular string' => [
                'string',
                'varchar',
                'string',
                'hello',
                'hello',
            ],
            'timezone expression' => [
                'timestamp',
                'timestamp',
                'string',
                "timezone('UTC'::text, now())",
                new Expression("timezone('UTC'::text, now())"),
            ],
        ];
    }
}
