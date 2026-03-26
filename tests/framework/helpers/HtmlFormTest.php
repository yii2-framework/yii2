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
use yii\helpers\Url;
use yii\web\Request;
use yii\web\Response;
use yiiunit\framework\helpers\providers\HtmlFormProvider;
use yiiunit\TestCase;

/**
 * Unit tests for {@see Html} form-related helper methods.
 *
 * {@see HtmlFormProvider} for test case data providers.
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */
#[Group('helpers')]
#[Group('html')]
#[Group('html-form')]
final class HtmlFormTest extends TestCase
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

    #[DataProviderExternal(HtmlFormProvider::class, 'beginFormSimulateViaPost')]
    public function testBeginFormSimulateViaPost(string $expected, string $method): void
    {
        $actual = Html::beginForm('/foo', $method);

        self::assertStringMatchesFormat(
            $expected,
            $actual,
            "Form begin tag for method '$method' does not match expected format.",
        );
    }

    #[DataProviderExternal(HtmlFormProvider::class, 'beginForm')]
    public function testBeginForm(string $expected, string $action, string $method): void
    {
        self::assertSame(
            $expected,
            Html::beginForm($action, $method),
            'Generated HTML does not match.',
        );
    }

    public function testEndForm(): void
    {
        self::assertSame(
            <<<HTML
            </form>
            HTML,
            Html::endForm(),
            'Generated HTML does not match.',
        );
    }

    #[DataProviderExternal(HtmlFormProvider::class, 'a')]
    public function testA(string $expected, string $text, string|null $url): void
    {
        self::assertSame(
            $expected,
            Html::a($text, $url),
            'Generated HTML does not match.',
        );
    }

    public function testAWithRoute(): void
    {
        self::assertSame(
            <<<HTML
            <a href="https://www.example.com/index.php?r=site%2Ftest">Test page</a>
            HTML,
            Html::a('Test page', Url::to(['/site/test'], 'https')),
            'Generated HTML for HTTPS route URL does not match.',
        );
    }

    public function testMailto(): void
    {
        self::assertSame(
            <<<HTML
            <a href="mailto:test&lt;&gt;">test<></a>
            HTML,
            Html::mailto('test<>'),
            'Generated HTML using label as email does not match.',
        );
        self::assertSame(
            <<<HTML
            <a href="mailto:test&gt;">test<></a>
            HTML,
            Html::mailto('test<>', 'test>'),
            'Generated HTML with explicit email value does not match.',
        );
    }

    /**
     * @phpstan-param array<string, mixed> $options
     */
    #[DataProviderExternal(HtmlFormProvider::class, 'img')]
    public function testImg(string $expected, string $src, array $options): void
    {
        self::assertSame(
            $expected,
            Html::img($src, $options),
            "Generated HTML for source '$src' does not match.",
        );
    }

    public function testLabel(): void
    {
        self::assertSame(
            <<<HTML
            <label>something<></label>
            HTML,
            Html::label('something<>'),
            "Generated HTML without 'for' attribute does not match.",
        );
        self::assertSame(
            <<<HTML
            <label for="a">something<></label>
            HTML,
            Html::label('something<>', 'a'),
            "Generated HTML with 'for' attribute does not match.",
        );
        self::assertSame(
            <<<HTML
            <label class="test" for="a">something<></label>
            HTML,
            Html::label('something<>', 'a', ['class' => 'test']),
            'Generated HTML with additional attributes does not match.',
        );
    }

    public function testButton(): void
    {
        self::assertSame(
            <<<HTML
            <button type="button">Button</button>
            HTML,
            Html::button(),
            'Button with default options does not match.',
        );
        self::assertSame(
            <<<HTML
            <button type="button" name="test" value="value">content<></button>
            HTML,
            Html::button(
                'content<>',
                ['name' => 'test', 'value' => 'value'],
            ),
            "Generated HTML with custom 'name' and 'value' does not match.",
        );
        self::assertSame(
            <<<HTML
            <button type="submit" class="t" name="test" value="value">content<></button>
            HTML,
            Html::button(
                'content<>',
                ['type' => 'submit', 'name' => 'test', 'value' => 'value', 'class' => 't'],
            ),
            'Generated HTML with submit type and CSS class does not match.',
        );
    }

    public function testSubmitButton(): void
    {
        self::assertSame(
            <<<HTML
            <button type="submit">Submit</button>
            HTML,
            Html::submitButton(),
            'Submit button with default options does not match.',
        );
        self::assertSame(
            <<<HTML
            <button type="submit" class="t" name="test" value="value">content<></button>
            HTML,
            Html::submitButton(
                'content<>',
                ['name' => 'test', 'value' => 'value', 'class' => 't'],
            ),
            'Submit button with custom options does not match.',
        );
    }

    public function testResetButton(): void
    {
        self::assertSame(
            <<<HTML
            <button type="reset">Reset</button>
            HTML,
            Html::resetButton(),
            'Reset button with default options does not match.',
        );
        self::assertSame(
            <<<HTML
            <button type="reset" class="t" name="test" value="value">content<></button>
            HTML,
            Html::resetButton(
                'content<>',
                ['name' => 'test', 'value' => 'value', 'class' => 't'],
            ),
            'Reset button with custom options does not match.',
        );
    }
}
