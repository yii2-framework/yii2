<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yiiunit\base\db\conditions;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yii\db\conditions\HashCondition;

/**
 * Unit tests for {@see HashCondition}.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 2.2
 */
#[Group('condition')]
final class HashConditionTest extends TestCase
{
    public function testFromArrayDefinition(): void
    {
        $condition = HashCondition::fromArrayDefinition('', ['a' => 1, 'b' => 2]);

        self::assertInstanceOf(
            HashCondition::class,
            $condition,
            'Should return a HashCondition instance.',
        );
        self::assertSame(
            ['a' => 1, 'b' => 2],
            $condition->getHash(),
            'Hash should match the provided operands.',
        );
    }

    public function testFromArrayDefinitionWithEmptyOperands(): void
    {
        $condition = HashCondition::fromArrayDefinition('', []);

        self::assertInstanceOf(
            HashCondition::class,
            $condition,
            'Should return a HashCondition instance for empty operands.',
        );
        self::assertEmpty(
            $condition->getHash(),
            "Hash should be an empty 'array' when no operands are provided.",
        );
    }

    public function testFromArrayDefinitionWithNullValues(): void
    {
        $condition = HashCondition::fromArrayDefinition('', ['a' => null]);

        self::assertSame(
            ['a' => null],
            $condition->getHash(),
            "Hash should preserve 'null' values.",
        );
    }

    public function testGetHashReturnsConstructorValue(): void
    {
        $condition = new HashCondition(['x' => 10]);

        self::assertSame(
            ['x' => 10],
            $condition->getHash(),
            'Should return the value passed to the constructor.',
        );
    }

    public function testGetHashReturnsNullWhenConstructedWithNull(): void
    {
        $condition = new HashCondition(null);

        self::assertNull(
            $condition->getHash(),
            "Should return 'null' when constructed with 'null'.",
        );
    }
}
