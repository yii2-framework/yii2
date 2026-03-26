<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yiiunit\framework\helpers\providers;

use ArrayObject;

/**
 * Data provider for {@see \yiiunit\framework\helpers\HtmlListTest} test cases.
 *
 * Provides representative input/output pairs for HTML list-related methods.
 *
 * @copyright Copyright (c) 2008 Yii Software LLC.
 * @license https://www.yiiframework.com/license/
 */
final class HtmlListProvider
{
    private const DATA_ITEMS = [
        'value1' => 'text1',
        'value2' => 'text2',
    ];
    private const DATA_ITEMS_2 = [
        'value1<>' => 'text1<>',
        'value  2' => 'text  2',
    ];
    private const DATA_ITEMS_3 = [
        0 => 'zero',
        1 => 'one',
        'value3' => 'text3',
    ];

    /**
     * @phpstan-return array<
     *   string,
     *   array{string, string, array|\ArrayObject|null, array<string|int, string>, array<string, mixed>},
     * >
     */
    public static function dropDownList(): array
    {
        return [
            'default options' => [
                <<<HTML
                <select name="test">

                </select>
                HTML,
                'test',
                null,
                [],
                [],
            ],
            'selected value' => [
                <<<HTML
                <select name="test">
                <option value="value1">text1</option>
                <option value="value2" selected>text2</option>
                </select>
                HTML,
                'test',
                'value2',
                self::DATA_ITEMS,
                [],
            ],
            'selected value in options' => [
                <<<HTML
                <select name="test">
                <option value="value1">text1</option>
                <option value="value2" selected>text2</option>
                </select>
                HTML,
                'test',
                null,
                self::DATA_ITEMS,
                ['options' => ['value2' => ['selected' => true]]],
            ],
            'multiple with array value' => [
                <<<HTML
                <select name="test[]" multiple="true" size="4">
                <option value="0" selected>zero</option>
                <option value="1">one</option>
                <option value="value3">text3</option>
                </select>
                HTML,
                'test',
                [0],
                self::DATA_ITEMS_3,
                ['multiple' => 'true'],
            ],
            'multiple with ArrayObject' => [
                <<<HTML
                <select name="test[]" multiple="true" size="4">
                <option value="0" selected>zero</option>
                <option value="1">one</option>
                <option value="value3">text3</option>
                </select>
                HTML,
                'test',
                new ArrayObject([0]),
                self::DATA_ITEMS_3,
                ['multiple' => 'true'],
            ],
            'multiple with ArrayObject string values' => [
                <<<HTML
                <select name="test[]" multiple="true" size="4">
                <option value="0">zero</option>
                <option value="1" selected>one</option>
                <option value="value3" selected>text3</option>
                </select>
                HTML,
                'test',
                new ArrayObject(['1', 'value3']),
                self::DATA_ITEMS_3,
                ['multiple' => 'true'],
            ],
            'multiple with empty items' => [
                <<<HTML
                <select name="test[]" multiple="true" size="4">

                </select>
                HTML,
                'test',
                null,
                [],
                ['multiple' => 'true'],
            ],
            'multiple with string values' => [
                <<<HTML
                <select name="test[]" multiple="true" size="4">
                <option value="0">zero</option>
                <option value="1" selected>one</option>
                <option value="value3" selected>text3</option>
                </select>
                HTML,
                'test',
                ['1', 'value3'],
                self::DATA_ITEMS_3,
                ['multiple' => 'true'],
            ],
            'with items' => [
                <<<HTML
                <select name="test">
                <option value="value1">text1</option>
                <option value="value2">text2</option>
                </select>
                HTML,
                'test',
                null,
                self::DATA_ITEMS,
                [],
            ],
        ];
    }

    /**
     * @phpstan-return array<
     *   string,
     *   array{string, string, string|array|ArrayObject|null, array<string|int, string>, array<string, mixed>},
     * >
     */
    public static function listBox(): array
    {
        return [
            'ArrayObject integer zero selection' => [
                <<<HTML
                <select name="test" size="4">
                <option value="0" selected>zero</option>
                <option value="1">one</option>
                <option value="value3">text3</option>
                </select>
                HTML,
                'test',
                new ArrayObject([0]),
                self::DATA_ITEMS_3,
                [],
            ],
            'ArrayObject selection' => [
                <<<HTML
                <select name="test" size="4">
                <option value="value1" selected>text1</option>
                <option value="value2" selected>text2</option>
                </select>
                HTML,
                'test',
                new ArrayObject(['value1', 'value2']),
                self::DATA_ITEMS,
                [],
            ],
            'ArrayObject string value selection' => [
                <<<HTML
                <select name="test" size="4">
                <option value="0">zero</option>
                <option value="1" selected>one</option>
                <option value="value3" selected>text3</option>
                </select>
                HTML,
                'test',
                new ArrayObject(['1', 'value3']),
                self::DATA_ITEMS_3,
                [],
            ],
            'custom size' => [
                <<<HTML
                <select name="test" size="5">
                <option value="value1">text1</option>
                <option value="value2">text2</option>
                </select>
                HTML,
                'test',
                null,
                self::DATA_ITEMS,
                ['size' => 5],
            ],
            'default options' => [
                <<<HTML
                <select name="test" size="4">

                </select>
                HTML,
                'test',
                null,
                [],
                [],
            ],
            'disabled unselect hidden input' => [
                <<<HTML
                <input type="hidden" name="test" value="0" disabled><select name="test" disabled size="4">

                </select>
                HTML,
                'test',
                '',
                [],
                ['unselect' => '0', 'disabled' => true],
            ],
            'encoded spaces' => [
                <<<HTML
                <select name="test" size="4">
                <option value="value1&lt;&gt;">text1&lt;&gt;</option>
                <option value="value  2">text&nbsp;&nbsp;2</option>
                </select>
                HTML,
                'test',
                null,
                self::DATA_ITEMS_2,
                ['encodeSpaces' => true],
            ],
            'encoded spaces and encoding disabled' => [
                <<<HTML
                <select name="test" size="4">
                <option value="value1&lt;&gt;">text1<></option>
                <option value="value  2">text&nbsp;&nbsp;2</option>
                </select>
                HTML,
                'test',
                null,
                self::DATA_ITEMS_2,
                [
                    'encodeSpaces' => true,
                    'encode' => false,
                ],
            ],
            'encoding disabled' => [
                <<<HTML
                <select name="test" size="4">
                <option value="value1&lt;&gt;">text1<></option>
                <option value="value  2">text  2</option>
                </select>
                HTML,
                'test',
                null,
                self::DATA_ITEMS_2,
                ['encode' => false],
            ],
            'integer zero selection' => [
                <<<HTML
                <select name="test" size="4">
                <option value="0" selected>zero</option>
                <option value="1">one</option>
                <option value="value3">text3</option>
                </select>
                HTML,
                'test',
                [0],
                self::DATA_ITEMS_3,
                [],
            ],
            'multiple selected values' => [
                <<<HTML
                <select name="test" size="4">
                <option value="value1" selected>text1</option>
                <option value="value2" selected>text2</option>
                </select>
                HTML,
                'test',
                ['value1', 'value2'],
                self::DATA_ITEMS,
                [],
            ],
            'multiple with bracket name' => [
                <<<HTML
                <select name="test[]" multiple size="4">

                </select>
                HTML,
                'test[]',
                null,
                [],
                ['multiple' => true],
            ],
            'multiple with empty items' => [
                <<<HTML
                <select name="test[]" multiple size="4">

                </select>
                HTML,
                'test',
                null,
                [],
                ['multiple' => true],
            ],
            'selected value' => [
                <<<HTML
                <select name="test" size="4">
                <option value="value1">text1</option>
                <option value="value2" selected>text2</option>
                </select>
                HTML,
                'test',
                'value2',
                self::DATA_ITEMS,
                [],
            ],
            'special characters in items' => [
                <<<HTML
                <select name="test" size="4">
                <option value="value1&lt;&gt;">text1&lt;&gt;</option>
                <option value="value  2">text  2</option>
                </select>
                HTML,
                'test',
                null,
                self::DATA_ITEMS_2,
                [],
            ],
            'string value selection' => [
                <<<HTML
                <select name="test" size="4">
                <option value="0">zero</option>
                <option value="1" selected>one</option>
                <option value="value3" selected>text3</option>
                </select>
                HTML,
                'test',
                ['1', 'value3'],
                self::DATA_ITEMS_3,
                [],
            ],
            'unselect hidden input' => [
                <<<HTML
                <input type="hidden" name="test" value="0"><select name="test" size="4">

                </select>
                HTML,
                'test',
                '',
                [],
                ['unselect' => '0'],
            ],
        ];
    }

    /**
     * @phpstan-return array<
     *   string,
     *   array{string, string, array|ArrayObject|null, array<string|int, string>, array<string, mixed>},
     * >
     */
    public static function checkboxList(): array
    {
        return [
            'ArrayObject integer zero checked' => [
                <<<HTML
                <div><label><input type="checkbox" name="test[]" value="0" checked> zero</label>
                <label><input type="checkbox" name="test[]" value="1"> one</label>
                <label><input type="checkbox" name="test[]" value="value3"> text3</label></div>
                HTML,
                'test',
                new ArrayObject([0]),
                self::DATA_ITEMS_3,
                [],
            ],
            'ArrayObject string selections' => [
                <<<HTML
                <div><label><input type="checkbox" name="test[]" value="0"> zero</label>
                <label><input type="checkbox" name="test[]" value="1" checked> one</label>
                <label><input type="checkbox" name="test[]" value="value3" checked> text3</label></div>
                HTML,
                'test',
                new ArrayObject(['1', 'value3']),
                self::DATA_ITEMS_3,
                [],
            ],
            'bracket name' => [
                <<<HTML
                <div><label><input type="checkbox" name="test[]" value="value1"> text1</label>
                <label><input type="checkbox" name="test[]" value="value2" checked> text2</label></div>
                HTML,
                'test[]',
                ['value2'],
                self::DATA_ITEMS,
                [],
            ],
            'checked value' => [
                <<<HTML
                <div><label><input type="checkbox" name="test[]" value="value1"> text1</label>
                <label><input type="checkbox" name="test[]" value="value2" checked> text2</label></div>
                HTML,
                'test',
                ['value2'],
                self::DATA_ITEMS,
                [],
            ],
            'custom item options' => [
                <<<HTML
                <div><label><input type="checkbox" name="test[]" value="0"> Test Label</label>
                <label><input type="checkbox" name="test[]" value="0"> Test Label</label></div>
                HTML,
                'test',
                null,
                self::DATA_ITEMS,
                ['itemOptions' => ['value' => 0, 'label' => 'Test Label']],
            ],
            'empty data set' => [
                <<<HTML
                <div></div>
                HTML,
                'test',
                null,
                [],
                [],
            ],
            'integer zero checked' => [
                <<<HTML
                <div><label><input type="checkbox" name="test[]" value="0" checked> zero</label>
                <label><input type="checkbox" name="test[]" value="1"> one</label>
                <label><input type="checkbox" name="test[]" value="value3"> text3</label></div>
                HTML,
                'test',
                [0],
                self::DATA_ITEMS_3,
                [],
            ],
            'multiple string selections' => [
                <<<HTML
                <div><label><input type="checkbox" name="test[]" value="0"> zero</label>
                <label><input type="checkbox" name="test[]" value="1" checked> one</label>
                <label><input type="checkbox" name="test[]" value="value3" checked> text3</label></div>
                HTML,
                'test',
                ['1', 'value3'],
                self::DATA_ITEMS_3,
                [],
            ],
            'non-strict float comparison' => [
                <<<HTML
                <div><label><input type="checkbox" name="test[]" value="1"> 1</label>
                <label><input type="checkbox" name="test[]" value="1.1" checked> 1.1</label>
                <label><input type="checkbox" name="test[]" value="1.10" checked> 1.10</label></div>
                HTML,
                'test',
                [1.1],
                [
                    '1' => '1',
                    '1.1' =>
                    '1.1',
                    '1.10' => '1.10',
                ],
                [],
            ],
            'special characters' => [
                <<<HTML
                <div><label><input type="checkbox" name="test[]" value="value1&lt;&gt;"> text1&lt;&gt;</label>
                <label><input type="checkbox" name="test[]" value="value  2"> text  2</label></div>
                HTML,
                'test',
                ['value2'],
                self::DATA_ITEMS_2,
                [],
            ],
            'strict float comparison' => [
                <<<HTML
                <div><label><input type="checkbox" name="test[]" value="1"> 1</label>
                <label><input type="checkbox" name="test[]" value="1.1" checked> 1.1</label>
                <label><input type="checkbox" name="test[]" value="1.10"> 1.10</label></div>
                HTML,
                'test',
                [1.1],
                [
                    '1' => '1',
                    '1.1' => '1.1',
                    '1.10' => '1.10',
                ],
                ['strict' => true],
            ],
            'strict string comparison' => [
                <<<HTML
                <div><label><input type="checkbox" name="test[]" value="1"> 1</label>
                <label><input type="checkbox" name="test[]" value="1.1" checked> 1.1</label>
                <label><input type="checkbox" name="test[]" value="1.10"> 1.10</label></div>
                HTML,
                'test',
                ['1.1'],
                [
                    '1' => '1',
                    '1.1' => '1.1',
                    '1.10' => '1.10',
                ],
                ['strict' => true],
            ],
        ];
    }

    /**
     * @phpstan-return array<
     *   string,
     *   array{string, string, array|ArrayObject|null, array<string|int, string>, array<string, mixed>},
     * >
     */
    public static function radioList(): array
    {
        return [
            'ArrayObject integer zero checked' => [
                <<<HTML
                <div><label><input type="radio" name="test" value="0" checked> zero</label>
                <label><input type="radio" name="test" value="1"> one</label>
                <label><input type="radio" name="test" value="value3"> text3</label></div>
                HTML,
                'test',
                new ArrayObject([0]),
                self::DATA_ITEMS_3,
                [],
            ],
            'ArrayObject string value selection' => [
                <<<HTML
                <div><label><input type="radio" name="test" value="0"> zero</label>
                <label><input type="radio" name="test" value="1"> one</label>
                <label><input type="radio" name="test" value="value3" checked> text3</label></div>
                HTML,
                'test',
                new ArrayObject(['value3']),
                self::DATA_ITEMS_3,
                [],
            ],
            'checked value' => [
                <<<HTML
                <div><label><input type="radio" name="test" value="value1"> text1</label>
                <label><input type="radio" name="test" value="value2" checked> text2</label></div>
                HTML,
                'test',
                ['value2'],
                self::DATA_ITEMS,
                [],
            ],
            'custom item options' => [
                <<<HTML
                <div><label><input type="radio" name="test" value="0"> Test Label</label>
                <label><input type="radio" name="test" value="0"> Test Label</label></div>
                HTML,
                'test',
                null,
                self::DATA_ITEMS,
                ['itemOptions' => ['value' => 0, 'label' => 'Test Label']],
            ],
            'empty data set' => [
                <<<HTML
                <div></div>
                HTML,
                'test',
                null,
                [],
                [],
            ],
            'integer zero checked' => [
                <<<HTML
                <div><label><input type="radio" name="test" value="0" checked> zero</label>
                <label><input type="radio" name="test" value="1"> one</label>
                <label><input type="radio" name="test" value="value3"> text3</label></div>
                HTML,
                'test',
                [0],
                self::DATA_ITEMS_3,
                [],
            ],
            'non-strict float comparison' => [
                <<<HTML
                <div><label><input type="radio" name="test" value="1"> 1</label>
                <label><input type="radio" name="test" value="1.1" checked> 1.1</label>
                <label><input type="radio" name="test" value="1.10" checked> 1.10</label></div>
                HTML,
                'test',
                ['1.1'],
                [
                    '1' => '1',
                    '1.1' => '1.1',
                    '1.10' => '1.10',
                ],
                [],
            ],
            'special characters' => [
                <<<HTML
                <div><label><input type="radio" name="test" value="value1&lt;&gt;"> text1&lt;&gt;</label>
                <label><input type="radio" name="test" value="value  2"> text  2</label></div>
                HTML,
                'test',
                ['value2'],
                self::DATA_ITEMS_2,
                [],
            ],
            'strict float comparison' => [
                <<<HTML
                <div><label><input type="radio" name="test" value="1"> 1</label>
                <label><input type="radio" name="test" value="1.1" checked> 1.1</label>
                <label><input type="radio" name="test" value="1.10"> 1.10</label></div>
                HTML,
                'test',
                [1.1],
                [
                    '1' => '1',
                    '1.1' => '1.1',
                    '1.10' => '1.10',
                ],
                ['strict' => true],
            ],
            'strict string comparison' => [
                <<<HTML
                <div><label><input type="radio" name="test" value="1"> 1</label>
                <label><input type="radio" name="test" value="1.1" checked> 1.1</label>
                <label><input type="radio" name="test" value="1.10"> 1.10</label></div>
                HTML,
                'test',
                ['1.1'],
                [
                    '1' => '1',
                    '1.1' => '1.1',
                    '1.10' => '1.10',
                ],
                ['strict' => true],
            ],
            'string value selection' => [
                <<<HTML
                <div><label><input type="radio" name="test" value="0"> zero</label>
                <label><input type="radio" name="test" value="1"> one</label>
                <label><input type="radio" name="test" value="value3" checked> text3</label></div>
                HTML,
                'test',
                ['value3'],
                self::DATA_ITEMS_3,
                [],
            ],
        ];
    }

    /**
     * @phpstan-return array<string, array{string, mixed, array<string|int, string>, array<string, mixed>}>
     */
    public static function renderSelectOptions(): array
    {
        $boolData = [true => 'Yes', false => 'No'];

        return [
            'boolean false selection' => [
                <<<HTML
                <option value="">Please select</option>
                <option value="1">Yes</option>
                <option value="0" selected>No</option>
                HTML,
                false,
                $boolData,
                ['prompt' => 'Please select'],
            ],
            'non-strict float comparison' => [
                <<<HTML
                <option value="1">1</option>
                <option value="1.1" selected>1.1</option>
                <option value="1.10" selected>1.10</option>
                HTML,
                [1.1],
                [
                    '1' => '1',
                    '1.1' => '1.1',
                    '1.10' => '1.10',
                ],
                [],
            ],
            'prompt with attributes' => [
                <<<HTML
                <option class="prompt" value="-1" label="None">Please select</option>
                <option value="value1" selected>label1</option>
                <option value="value2">label2</option>
                HTML,
                ['value1'],
                [
                    'value1' => 'label1',
                    'value2' => 'label2',
                ],
                [
                    'prompt' => [
                        'text' => 'Please select',
                        'options' => [
                            'class' => 'prompt',
                            'value' => '-1',
                            'label' => 'None',
                        ],
                    ],
                ],
            ],
            'strict boolean false array selection' => [
                <<<HTML
                <option value="">Please select</option>
                <option value="1">Yes</option>
                <option value="0" selected>No</option>
                HTML,
                [false],
                $boolData,
                [
                    'prompt' => 'Please select',
                    'strict' => true,
                ],
            ],
            'strict boolean false selection' => [
                <<<HTML
                <option value="">Please select</option>
                <option value="1">Yes</option>
                <option value="0" selected>No</option>
                HTML,
                false,
                $boolData,
                [
                    'prompt' => 'Please select',
                    'strict' => true,
                ],
            ],
            'strict comparison in nested group' => [
                <<<HTML
                <option value="1">1</option>
                <option value="1.1">1.1</option>
                <optgroup label="group">
                <option value="1.10" selected>1.10</option>
                </optgroup>
                HTML,
                ['1.10'],
                [
                    '1' => '1',
                    '1.1' => '1.1',
                    'group' => ['1.10' => '1.10'],
                ],
                ['strict' => true],
            ],
            'strict float comparison' => [
                <<<HTML
                <option value="1">1</option>
                <option value="1.1" selected>1.1</option>
                <option value="1.10">1.10</option>
                HTML,
                [1.1],
                [
                    '1' => '1',
                    '1.1' => '1.1',
                    '1.10' => '1.10',
                ],
                ['strict' => true],
            ],
            'strict string comparison' => [
                <<<HTML
                <option value="1">1</option>
                <option value="1.1" selected>1.1</option>
                <option value="1.10">1.10</option>
                HTML,
                ['1.1'],
                [
                    '1' => '1',
                    '1.1' => '1.1',
                    '1.10' => '1.10',
                ],
                ['strict' => true],
            ],
        ];
    }

    /**
     * @phpstan-return array<string, array{string|int|bool|null, bool, bool, bool}>
     */
    public static function nonStrictBooleanDropDownList(): array
    {
        return [
            'empty string selects empty' => ['', true, false, false],
            'false selects No' => [false, false, false, true],
            'int 0 selects No' => [0, false, false, true],
            'int 1 selects Yes' => [1, false, true, false],
            'null selects none' => [null, false, false, false],
            'string 0 selects No' => ['0', false, false, true],
            'string 1 selects Yes' => ['1', false, true, false],
            'true selects Yes' => [true, false, true, false],
        ];
    }

    /**
     * @phpstan-return array<string, array{string|int|bool|null, bool, bool, bool}>
     */
    public static function strictBooleanDropDownList(): array
    {
        return self::nonStrictBooleanDropDownList();
    }
}
