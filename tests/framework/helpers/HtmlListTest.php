<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yiiunit\framework\helpers;

use ArrayObject;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Group;
use yii\db\ArrayExpression;
use yii\helpers\Html;
use yii\web\Request;
use yii\web\Response;
use yiiunit\framework\helpers\providers\HtmlListProvider;
use yiiunit\TestCase;

/**
 * Unit tests for {@see Html} helper managing HTML list-related methods.
 *
 * {@see HtmlListProvider} for test case data providers.
 *
 * @copyright Copyright (c) 2008 Yii Software LLC.
 * @license https://www.yiiframework.com/license/
 */
#[Group('helpers')]
#[Group('html')]
final class HtmlListTest extends TestCase
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

    /**
     * @phpstan-param array<string, mixed> $options
     */
    #[DataProviderExternal(HtmlListProvider::class, 'dropDownList')]
    public function testDropDownList(
        string $expected,
        string $name,
        string|array|ArrayObject|null $selection,
        array $items,
        array $options,
    ): void {
        self::assertEqualsWithoutLE(
            $expected,
            Html::dropDownList($name, $selection, $items, $options),
            'Dropdown list does not match.',
        );
    }

    #[DataProviderExternal(HtmlListProvider::class, 'nonStrictBooleanDropDownList')]
    public function testNonStrictBooleanDropDownList(
        string|int|bool|null $selection,
        bool $selectedEmpty,
        bool $selectedYes,
        bool $selectedNo,
    ): void {
        $selectedEmpty = $selectedEmpty ? ' selected' : '';
        $selectedYes = $selectedYes ? ' selected' : '';
        $selectedNo = $selectedNo ? ' selected' : '';

        self::assertEqualsWithoutLE(
            <<<HTML
            <select name="test">
            <option value=""$selectedEmpty></option>
            <option value="1"$selectedYes>Yes</option>
            <option value="0"$selectedNo>No</option>
            </select>
            HTML,
            Html::dropDownList(
                'test',
                $selection,
                ['' => '', '1' => 'Yes', '0' => 'No'],
            ),
            'Non-strict boolean selection does not match.',
        );
    }

    #[DataProviderExternal(HtmlListProvider::class, 'strictBooleanDropDownList')]
    public function testStrictBooleanDropDownList(
        string|int|bool|null $selection,
        bool $selectedEmpty,
        bool $selectedYes,
        bool $selectedNo,
    ): void {
        $selectedEmpty = $selectedEmpty ? ' selected' : '';
        $selectedYes = $selectedYes ? ' selected' : '';
        $selectedNo = $selectedNo ? ' selected' : '';

        self::assertEqualsWithoutLE(
            <<<HTML
            <select name="test">
            <option value=""$selectedEmpty></option>
            <option value="1"$selectedYes>Yes</option>
            <option value="0"$selectedNo>No</option>
            </select>
            HTML,
            Html::dropDownList(
                'test',
                $selection,
                ['' => '', '1' => 'Yes', '0' => 'No'],
                ['strict' => true],
            ),
            'Strict boolean selection does not match.',
        );
    }

    /**
     * @phpstan-param array<string, mixed> $options
     */
    #[DataProviderExternal(HtmlListProvider::class, 'listBox')]
    public function testListBox(
        string $expected,
        string $name,
        string|array|ArrayObject|null $selection,
        array $items,
        array $options,
    ): void {
        self::assertEqualsWithoutLE(
            $expected,
            Html::listBox($name, $selection, $items, $options),
            'Listbox does not match.',
        );
    }

    /**
     * @phpstan-param array<string, mixed> $options
     */
    #[DataProviderExternal(HtmlListProvider::class, 'checkboxList')]
    public function testCheckboxList(
        string $expected,
        string $name,
        array|ArrayObject|null $selection,
        array $items,
        array $options,
    ): void {
        self::assertEqualsWithoutLE(
            $expected,
            Html::checkboxList($name, $selection, $items, $options),
            'Checkbox list does not match.',
        );
    }

    public function testCheckboxListWithSeparatorAndUnselect(): void
    {
        self::assertEqualsWithoutLE(
            <<<HTML
            <input type="hidden" name="test" value="0"><div><label><input type="checkbox" name="test[]" value="value1"> text1</label><br>
            <label><input type="checkbox" name="test[]" value="value2" checked> text2</label></div>
            HTML,
            Html::checkboxList(
                'test',
                ['value2'],
                $this->getDataItems(),
                ['separator' => "<br>\n", 'unselect' => '0'],
            ),
            'Checkbox list with separator and unselect does not match.',
        );
    }

    public function testCheckboxListWithDisabledUnselect(): void
    {
        self::assertEqualsWithoutLE(
            <<<HTML
            <input type="hidden" name="test" value="0" disabled><div><label><input type="checkbox" name="test[]" value="value1"> text1</label><br>
            <label><input type="checkbox" name="test[]" value="value2"> text2</label></div>
            HTML,
            Html::checkboxList(
                'test',
                null,
                $this->getDataItems(),
                ['separator' => "<br>\n", 'unselect' => '0', 'disabled' => true],
            ),
            'Checkbox list with disabled unselect does not match.',
        );
    }

    public function testCheckboxListWithCustomItemCallback(): void
    {
        $itemCallback = static fn(
            $index,
            $label,
            $name,
            $checked,
            $value,
        ): string => $index . Html::label("{$label} " . Html::checkbox($name, $checked, ['value' => $value]));

        self::assertEqualsWithoutLE(
            <<<HTML
            <div>0<label>text1 <input type="checkbox" name="test[]" value="value1"></label>
            1<label>text2 <input type="checkbox" name="test[]" value="value2" checked></label></div>
            HTML,
            Html::checkboxList(
                'test',
                ['value2'],
                $this->getDataItems(),
                ['item' => $itemCallback],
            ),
            'Checkbox list with custom item callback does not match.',
        );
        self::assertEqualsWithoutLE(
            <<<HTML
            0<label>text1 <input type="checkbox" name="test[]" value="value1"></label>
            1<label>text2 <input type="checkbox" name="test[]" value="value2" checked></label>
            HTML,
            Html::checkboxList(
                'test',
                ['value2'],
                $this->getDataItems(),
                ['item' => $itemCallback, 'tag' => false],
            ),
            'Checkbox list with custom item callback and no wrapper tag does not match.',
        );
        self::assertEqualsWithoutLE(
            <<<HTML
            0<label>text1 <input type="checkbox" name="test[]" value="value1"></label>
            1<label>text2 <input type="checkbox" name="test[]" value="value2" checked></label>
            HTML,
            Html::checkboxList(
                'test',
                new ArrayObject(['value2']),
                $this->getDataItems(),
                ['item' => $itemCallback, 'tag' => false],
            ),
            'Checkbox list with ArrayObject selection and no wrapper tag does not match.',
        );
    }

    public function testRadioListWithArrayExpression(): void
    {
        $selection = new ArrayExpression(['first']);

        self::assertEqualsWithoutLE(
            <<<HTML
            <div><label><input type="radio" name="test" value="first" checked> first</label>
            <label><input type="radio" name="test" value="second"> second</label></div>
            HTML,
            Html::radioList(
                'test',
                $selection,
                ['first' => 'first', 'second' => 'second'],
            ),
            'Radio list with ArrayExpression selection does not match.',
        );
    }

    public function testCheckboxListWithArrayExpression(): void
    {
        self::assertEqualsWithoutLE(
            <<<HTML
            <div><label><input type="checkbox" name="test[]" value="first" checked> first</label>
            <label><input type="checkbox" name="test[]" value="second"> second</label></div>
            HTML,
            Html::checkboxList(
                'test',
                new ArrayExpression(['first']),
                ['first' => 'first', 'second' => 'second'],
            ),
            'Checkbox list with ArrayExpression selection does not match.',
        );
    }

    public function testRenderSelectOptionsWithArrayExpression(): void
    {
        self::assertEqualsWithoutLE(
            <<<HTML
            <option value="first" selected>first</option>
            <option value="second">second</option>
            HTML,
            Html::renderSelectOptions(
                new ArrayExpression(['first']),
                ['first' => 'first', 'second' => 'second'],
            ),
            'Render select options with ArrayExpression selection does not match.',
        );
    }

    /**
     * @phpstan-param array<string, mixed> $options
     */
    #[DataProviderExternal(HtmlListProvider::class, 'radioList')]
    public function testRadioList(
        string $expected,
        string $name,
        array|ArrayObject|null $selection,
        array $items,
        array $options,
    ): void {
        self::assertEqualsWithoutLE(
            $expected,
            Html::radioList($name, $selection, $items, $options),
            'Radio list does not match.',
        );
    }

    public function testRadioListWithSeparatorAndUnselect(): void
    {
        self::assertEqualsWithoutLE(
            <<<HTML
            <input type="hidden" name="test" value="0"><div><label><input type="radio" name="test" value="value1"> text1</label><br>
            <label><input type="radio" name="test" value="value2" checked> text2</label></div>
            HTML,
            Html::radioList(
                'test',
                ['value2'],
                $this->getDataItems(),
                ['separator' => "<br>\n", 'unselect' => '0'],
            ),
            'Radio list with separator and unselect does not match.',
        );
    }

    public function testRadioListWithDisabledUnselect(): void
    {
        self::assertEqualsWithoutLE(
            <<<HTML
            <input type="hidden" name="test" value="0" disabled><div><label><input type="radio" name="test" value="value1"> text1</label><br>
            <label><input type="radio" name="test" value="value2"> text2</label></div>
            HTML,
            Html::radioList(
                'test',
                null,
                $this->getDataItems(),
                ['separator' => "<br>\n", 'unselect' => '0', 'disabled' => true],
            ),
            'Radio list with disabled unselect does not match.',
        );
    }

    public function testRadioListWithCustomItemCallback(): void
    {
        $itemCallback = static fn(
            $index,
            $label,
            $name,
            $checked,
            $value,
        ): string => $index . Html::label("{$label} " . Html::radio($name, $checked, ['value' => $value]));

        self::assertEqualsWithoutLE(
            <<<HTML
            <div>0<label>text1 <input type="radio" name="test" value="value1"></label>
            1<label>text2 <input type="radio" name="test" value="value2" checked></label></div>
            HTML,
            Html::radioList(
                'test',
                ['value2'],
                $this->getDataItems(),
                ['item' => $itemCallback],
            ),
            'Radio list with custom item callback does not match.',
        );
        self::assertEqualsWithoutLE(
            <<<HTML
            0<label>text1 <input type="radio" name="test" value="value1"></label>
            1<label>text2 <input type="radio" name="test" value="value2" checked></label>
            HTML,
            Html::radioList(
                'test',
                ['value2'],
                $this->getDataItems(),
                ['item' => $itemCallback, 'tag' => false],
            ),
            'Radio list with custom item callback and no wrapper tag does not match.',
        );
        self::assertEqualsWithoutLE(
            <<<HTML
            0<label>text1 <input type="radio" name="test" value="value1"></label>
            1<label>text2 <input type="radio" name="test" value="value2" checked></label>
            HTML,
            Html::radioList(
                'test',
                new ArrayObject(['value2']),
                $this->getDataItems(),
                ['item' => $itemCallback, 'tag' => false],
            ),
            'Radio list with ArrayObject selection and no wrapper tag does not match.',
        );
    }

    public function testUl(): void
    {
        $data = [1, 'abc', '<>'];

        self::assertEqualsWithoutLE(
            <<<HTML
            <ul>
            <li>1</li>
            <li>abc</li>
            <li>&lt;&gt;</li>
            </ul>
            HTML,
            Html::ul($data),
            'UL with default options does not match.',
        );
        self::assertEqualsWithoutLE(
            <<<HTML
            <ul class="test">
            <li class="item-0">1</li>
            <li class="item-1">abc</li>
            <li class="item-2"><></li>
            </ul>
            HTML,
            Html::ul(
                $data,
                [
                    'class' => 'test',
                    'item' => static fn($item, $index): string => <<<HTML
                    <li class="item-$index">$item</li>
                    HTML,
                ],
            ),
            'UL with custom item callback does not match.',
        );
        self::assertSame(
            '<ul class="test"></ul>',
            Html::ul([], ['class' => 'test']),
            'UL for empty data set with custom class does not match.',
        );
        self::assertStringMatchesFormat(
            '<foo>%A</foo>',
            Html::ul([], ['tag' => 'foo']),
            'UL for custom wrapper tag does not match.',
        );
    }

    public function testOl(): void
    {
        $data = [1, 'abc', '<>'];

        self::assertEqualsWithoutLE(
            <<<HTML
            <ol>
            <li class="ti">1</li>
            <li class="ti">abc</li>
            <li class="ti">&lt;&gt;</li>
            </ol>
            HTML,
            Html::ol($data, ['itemOptions' => ['class' => 'ti']]),
            'OL with item options does not match.',
        );
        self::assertEqualsWithoutLE(
            <<<HTML
            <ol class="test">
            <li class="item-0">1</li>
            <li class="item-1">abc</li>
            <li class="item-2"><></li>
            </ol>
            HTML,
            Html::ol(
                $data,
                [
                    'class' => 'test',
                    'item' => static fn($item, $index): string => <<<HTML
                    <li class="item-$index">$item</li>
                    HTML,
                ],
            ),
            'OL with custom item callback does not match.',
        );
        self::assertSame(
            '<ol class="test"></ol>',
            Html::ol([], ['class' => 'test']),
            'OL for empty data set with custom class does not match.',
        );
    }

    #[DataProviderExternal(HtmlListProvider::class, 'renderSelectOptions')]
    public function testRenderOptions(
        string $expected,
        mixed $selection,
        array $data,
        array $attributes,
    ): void {
        self::assertEqualsWithoutLE(
            $expected,
            Html::renderSelectOptions($selection, $data, $attributes),
            'Render select options does not match.',
        );
    }

    public function testRenderOptionsWithEncodedSpacesToggle(): void
    {
        $attributes = [
            'prompt' => 'please select<>',
            'options' => ['value111' => ['class' => 'option']],
            'groups' => ['group12' => ['class' => 'group']],
            'encodeSpaces' => true,
        ];
        $data = [
            'value1' => 'label1',
            'group1' => [
                'value11' => 'label11',
                'group11' => ['value111' => 'label111'],
                'group12' => [],
            ],
            'value2' => 'label2',
            'group2' => [],
        ];

        self::assertEqualsWithoutLE(
            <<<HTML
            <option value="">please&nbsp;select&lt;&gt;</option>
            <option value="value1" selected>label1</option>
            <optgroup label="group1">
            <option value="value11">label11</option>
            <optgroup label="group11">
            <option class="option" value="value111" selected>label111</option>
            </optgroup>
            <optgroup class="group" label="group12">

            </optgroup>
            </optgroup>
            <option value="value2">label2</option>
            <optgroup label="group2">

            </optgroup>
            HTML,
            Html::renderSelectOptions(
                ['value111', 'value1'],
                $data,
                $attributes,
            ),
            'Render select options with encoded spaces does not match.',
        );

        $attributes = [
            'prompt' => 'please select<>',
            'options' => ['value111' => ['class' => 'option']],
            'groups' => ['group12' => ['class' => 'group']],
        ];

        self::assertEqualsWithoutLE(
            <<<HTML
            <option value="">please select&lt;&gt;</option>
            <option value="value1" selected>label1</option>
            <optgroup label="group1">
            <option value="value11">label11</option>
            <optgroup label="group11">
            <option class="option" value="value111" selected>label111</option>
            </optgroup>
            <optgroup class="group" label="group12">

            </optgroup>
            </optgroup>
            <option value="value2">label2</option>
            <optgroup label="group2">

            </optgroup>
            HTML,
            Html::renderSelectOptions(
                ['value111', 'value1'],
                $data,
                $attributes,
            ),
            'Render select options without encoded spaces does not match.',
        );
    }

    protected function getDataItems(): array
    {
        return [
            'value1' => 'text1',
            'value2' => 'text2',
        ];
    }



}
