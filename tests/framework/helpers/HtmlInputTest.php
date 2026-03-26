<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yiiunit\framework\helpers;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Group;
use yii\helpers\Html;
use yii\web\Request;
use yii\web\Response;
use yiiunit\framework\helpers\providers\HtmlInputProvider;
use yiiunit\TestCase;

/**
 * Unit tests for {@see Html} helper managing HTML input elements.
 *
 * {@see HtmlInputProvider} for test case data providers.
 *
 * @copyright Copyright (c) 2008 Yii Software LLC.
 * @license https://www.yiiframework.com/license/
 */
#[Group('helpers')]
#[Group('html')]
#[Group('html-input')]
final class HtmlInputTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockApplication(
            [
                'components' => [
                    'request' => [
                        'class' => Request::class,
                        'url' => '/test',
                        'scriptUrl' => '/index.php',
                        'hostInfo' => 'http://www.example.com',
                        'enableCsrfValidation' => false,
                    ],
                    'response' => ['class' => Response::class],
                ],
            ],
        );
    }

    public function testInput(): void
    {
        self::assertSame(
            <<<HTML
            <input type="text">
            HTML,
            Html::input('text'),
            'Input with default options does not match.',
        );
        self::assertSame(
            <<<HTML
            <input type="text" class="t" name="test" value="value">
            HTML,
            Html::input('text', 'test', 'value', ['class' => 't']),
            'Input with custom options does not match.',
        );
    }

    public function testButtonInput(): void
    {
        self::assertSame(
            <<<HTML
            <input type="button" value="Button">
            HTML,
            Html::buttonInput(),
            'Button input with default options does not match.',
        );
        self::assertSame(
            <<<HTML
            <input type="button" class="a" name="test" value="text">
            HTML,
            Html::buttonInput(
                'text',
                ['name' => 'test', 'class' => 'a'],
            ),
            'Button input with custom options does not match.',
        );
    }

    public function testSubmitInput(): void
    {
        self::assertSame(
            <<<HTML
            <input type="submit" value="Submit">
            HTML,
            Html::submitInput(),
            'Submit input with default options does not match.',
        );
        self::assertSame(
            <<<HTML
            <input type="submit" class="a" name="test" value="text">
            HTML,
            Html::submitInput(
                'text',
                ['name' => 'test', 'class' => 'a'],
            ),
            'Submit input with custom options does not match.',
        );
    }

    public function testResetInput(): void
    {
        self::assertSame(
            <<<HTML
            <input type="reset" value="Reset">
            HTML,
            Html::resetInput(),
            'Reset input with default options does not match.',
        );
        self::assertSame(
            <<<HTML
            <input type="reset" class="a" name="test" value="text">
            HTML,
            Html::resetInput(
                'text',
                ['name' => 'test', 'class' => 'a'],
            ),
            'Reset input with custom options does not match.',
        );
    }

    public function testTextInput(): void
    {
        self::assertSame(
            <<<HTML
            <input type="text" name="test">
            HTML,
            Html::textInput('test'),
            'Text input with default options does not match.',
        );
        self::assertSame(
            <<<HTML
            <input type="text" class="t" name="test" value="value">
            HTML,
            Html::textInput('test', 'value', ['class' => 't']),
            'Text input with custom options does not match.',
        );
    }

    public function testHiddenInput(): void
    {
        self::assertSame(
            <<<HTML
            <input type="hidden" name="test">
            HTML,
            Html::hiddenInput('test'),
            'Hidden input with default options does not match.',
        );
        self::assertSame(
            <<<HTML
            <input type="hidden" class="t" name="test" value="value">
            HTML,
            Html::hiddenInput('test', 'value', ['class' => 't']),
            'Hidden input with custom options does not match.',
        );
    }

    public function testPasswordInput(): void
    {
        self::assertSame(
            <<<HTML
            <input type="password" name="test">
            HTML,
            Html::passwordInput('test'),
            'Password input with default options does not match.',
        );
        self::assertSame(
            <<<HTML
            <input type="password" class="t" name="test" value="value">
            HTML,
            Html::passwordInput('test', 'value', ['class' => 't']),
            'Password input with custom options does not match.',
        );
    }

    public function testFileInput(): void
    {
        self::assertSame(
            <<<HTML
            <input type="file" name="test">
            HTML,
            Html::fileInput('test'),
            'File input with default options does not match.',
        );
        self::assertSame(
            <<<HTML
            <input type="file" class="t" name="test" value="value">
            HTML,
            Html::fileInput('test', 'value', ['class' => 't']),
            'File input with custom options does not match.',
        );
    }

    /**
     * @phpstan-param array<string, mixed> $options
     */
    #[DataProviderExternal(HtmlInputProvider::class, 'textarea')]
    public function testTextarea(string $expected, string $name, string|null $value, array $options): void
    {
        self::assertSame(
            $expected,
            Html::textarea($name, $value, $options),
            "Generated HTML for name '$name' does not match.",
        );
    }

    /**
     * @phpstan-param array<string, mixed> $options
     */
    #[DataProviderExternal(HtmlInputProvider::class, 'radio')]
    public function testRadio(string $expected, string $name, bool $checked, array $options): void
    {
        self::assertSame(
            $expected,
            Html::radio($name, $checked, $options),
            'Generated HTML does not match.',
        );
    }

    /**
     * @phpstan-param array<string, mixed> $options
     */
    #[DataProviderExternal(HtmlInputProvider::class, 'checkbox')]
    public function testCheckbox(string $expected, string $name, bool $checked, array $options): void
    {
        self::assertSame(
            $expected,
            Html::checkbox($name, $checked, $options),
            'Generated HTML does not match.',
        );
    }
}
