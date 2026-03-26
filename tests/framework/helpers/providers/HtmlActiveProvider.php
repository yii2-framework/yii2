<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yiiunit\framework\helpers\providers;

use yii\base\DynamicModel;

/**
 * Data provider for {@see \yiiunit\framework\helpers\HtmlActiveTest} test cases.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 2.2
 */
final class HtmlActiveProvider
{
    /**
     * @phpstan-return array<string, array{string, array<string, mixed>, string}>
     */
    public static function activeTextInput(): array
    {
        return [
            'maxlength custom' => [
                '',
                ['maxlength' => 99],
                <<<HTML
                <input type="text" id="htmltestmodel-name" name="HtmlTestModel[name]" value="" maxlength="99">
                HTML,
            ],
            'maxlength true' => [
                '',
                ['maxlength' => true],
                <<<HTML
                <input type="text" id="htmltestmodel-name" name="HtmlTestModel[name]" value="" maxlength="100">
                HTML,
            ],
            'value without options' => [
                'some text',
                [],
                <<<HTML
                <input type="text" id="htmltestmodel-name" name="HtmlTestModel[name]" value="some text">
                HTML,
            ],
        ];
    }

    /**
     * @phpstan-return array<string, array{string, array<string, mixed>, string, string}>
     */
    public static function activeTextInputMaxLength(): array
    {
        return [
            'maxlength custom' => [
                '',
                ['maxlength' => 99],
                <<<HTML
                <input type="text" id="htmltestmodel-title" name="HtmlTestModel[title]" value="" maxlength="99">
                HTML,
                <<<HTML
                <input type="text" id="htmltestmodel-alias" name="HtmlTestModel[alias]" value="" maxlength="99">
                HTML,
            ],
            'maxlength true' => [
                '',
                ['maxlength' => true],
                <<<HTML
                <input type="text" id="htmltestmodel-title" name="HtmlTestModel[title]" value="" maxlength="10">
                HTML,
                <<<HTML
                <input type="text" id="htmltestmodel-alias" name="HtmlTestModel[alias]" value="" maxlength="20">
                HTML,
            ],
            'value without options' => [
                'some text',
                [],
                <<<HTML
                <input type="text" id="htmltestmodel-title" name="HtmlTestModel[title]" value="some text">
                HTML,
                <<<HTML
                <input type="text" id="htmltestmodel-alias" name="HtmlTestModel[alias]" value="some text">
                HTML,
            ],
        ];
    }

    /**
     * @phpstan-return array<string, array{string, array<string, mixed>, string}>
     */
    public static function activePasswordInput(): array
    {
        return [
            'maxlength custom' => [
                '',
                ['maxlength' => 99],
                <<<HTML
                <input type="password" id="htmltestmodel-name" name="HtmlTestModel[name]" value="" maxlength="99">
                HTML,
            ],
            'maxlength true' => [
                '',
                ['maxlength' => true],
                <<<HTML
                <input type="password" id="htmltestmodel-name" name="HtmlTestModel[name]" value="" maxlength="100">
                HTML,
            ],
            'value without options' => [
                'some text',
                [],
                <<<HTML
                <input type="password" id="htmltestmodel-name" name="HtmlTestModel[name]" value="some text">
                HTML,
            ],
        ];
    }

    /**
     * @phpstan-return array<string, array{string, array<string, mixed>, string}>
     */
    public static function activeInputTypeText(): array
    {
        return [
            'maxlength custom' => [
                '',
                ['maxlength' => 99],
                <<<HTML
                <input type="text" id="htmltestmodel-name" name="HtmlTestModel[name]" value="" maxlength="99">
                HTML,
            ],
            'maxlength true' => [
                '',
                ['maxlength' => true],
                <<<HTML
                <input type="text" id="htmltestmodel-name" name="HtmlTestModel[name]" value="" maxlength="100">
                HTML,
            ],
            'value without options' => [
                'some text',
                [],
                <<<HTML
                <input type="text" id="htmltestmodel-name" name="HtmlTestModel[name]" value="some text">
                HTML,
            ],
        ];
    }

    /**
     * @phpstan-return array<
     *   string,
     *   array{0: string, 1: array<string, mixed>, 2: string, 3?: callable(DynamicModel): void|null},
     * >
     */
    public static function errorSummary(): array
    {
        return [
            'custom error with encoding' => [
                'not_an_integer',
                [],
                <<<HTML
                <div><p>Please fix the following errors:</p><ul><li>Error message. Here are some chars: &lt; &gt;</li></ul></div>
                HTML,
                static function (DynamicModel $model): void {
                    $model->addError('name', 'Error message. Here are some chars: < >');
                },
            ],
            'custom error without encoding' => [
                'not_an_integer',
                ['encode' => false],
                <<<HTML
                <div><p>Please fix the following errors:</p><ul><li>Error message. Here are some chars: < ></li></ul></div>
                HTML,
                static function (DynamicModel $model): void {
                    $model->addError('name', 'Error message. Here are some chars: < >');
                },
            ],
            'custom header, footer, and style' => [
                'ok',
                ['header' => 'Custom header', 'footer' => 'Custom footer', 'style' => 'color: red'],
                <<<HTML
                <div style="color: red; display:none">Custom header<ul></ul>Custom footer</div>
                HTML,
            ],
            'empty class' => [
                'empty_class',
                ['emptyClass' => 'd-none'],
                <<<'HTML'
                <div class="d-none"><p>Please fix the following errors:</p><ul></ul></div>
                HTML,
            ],
            'long string with custom error' => [
                str_repeat('long_string', 60),
                [],
                <<<HTML
                <div><p>Please fix the following errors:</p><ul><li>Error message. Here are some chars: &lt; &gt;</li></ul></div>
                HTML,
                static function (DynamicModel $model): void {
                    $model->addError('name', 'Error message. Here are some chars: < >');
                },
            ],
            'show all errors' => [
                'not_an_integer',
                ['showAllErrors' => true],
                <<<HTML
                <div><p>Please fix the following errors:</p><ul><li>Error message. Here are some chars: &lt; &gt;</li>
                <li>Error message. Here are even more chars: &quot;&quot;</li></ul></div>
                HTML,
                static function (DynamicModel $model): void {
                    $model->addError('name', 'Error message. Here are some chars: < >');
                    $model->addError('name', 'Error message. Here are even more chars: ""');
                },
            ],
            'string too long' => [
                str_repeat('long_string', 60),
                [],
                <<<HTML
                <div><p>Please fix the following errors:</p><ul><li>Name should contain at most 100 characters.</li></ul></div>
                HTML,
            ],
            'valid value, no errors' => [
                'ok',
                [],
                <<<HTML
                <div style="display:none"><p>Please fix the following errors:</p><ul></ul></div>
                HTML,
            ],
        ];
    }

    /**
     * @phpstan-return array<string, array{string, array<string, mixed>, string}>
     */
    public static function activeTextArea(): array
    {
        return [
            'maxlength custom' => [
                'some text',
                ['maxlength' => 99],
                <<<HTML
                <textarea id="htmltestmodel-description" name="HtmlTestModel[description]" maxlength="99">some text</textarea>
                HTML,
            ],
            'maxlength true' => [
                'some text',
                ['maxlength' => true],
                <<<HTML
                <textarea id="htmltestmodel-description" name="HtmlTestModel[description]" maxlength="500">some text</textarea>
                HTML,
            ],
            'override value' => [
                'some text',
                ['value' => 'override text'],
                <<<HTML
                <textarea id="htmltestmodel-description" name="HtmlTestModel[description]">override text</textarea>
                HTML,
            ],
            'value without options' => [
                'some text',
                [],
                <<<HTML
                <textarea id="htmltestmodel-description" name="HtmlTestModel[description]">some text</textarea>
                HTML,
            ],
        ];
    }

    /**
     * @phpstan-return array<string, array{bool, array<string, mixed>, string}>
     */
    public static function activeRadio(): array
    {
        return [
            'default options' => [
                true,
                [],
                <<<HTML
                <input type="hidden" name="HtmlTestModel[radio]" value="0"><label><input type="radio" id="htmltestmodel-radio" name="HtmlTestModel[radio]" value="1" checked> Radio</label>
                HTML,
            ],
            'label false' => [
                true,
                ['label' => false],
                <<<HTML
                <input type="hidden" name="HtmlTestModel[radio]" value="0"><input type="radio" id="htmltestmodel-radio" name="HtmlTestModel[radio]" value="1" checked>
                HTML,
            ],
            'uncheck false' => [
                true,
                ['uncheck' => false],
                <<<HTML
                <label><input type="radio" id="htmltestmodel-radio" name="HtmlTestModel[radio]" value="1" checked> Radio</label>
                HTML,
            ],
            'uncheck and label false' => [
                true,
                ['uncheck' => false, 'label' => false],
                <<<HTML
                <input type="radio" id="htmltestmodel-radio" name="HtmlTestModel[radio]" value="1" checked>
                HTML,
            ],
        ];
    }

    /**
     * @phpstan-return array<string, array{bool, array<string, mixed>, string}>
     */
    public static function activeCheckbox(): array
    {
        return [
            'default options' => [
                true,
                [],
                <<<HTML
                <input type="hidden" name="HtmlTestModel[checkbox]" value="0"><label><input type="checkbox" id="htmltestmodel-checkbox" name="HtmlTestModel[checkbox]" value="1" checked> Checkbox</label>
                HTML,
            ],
            'label false' => [
                true,
                ['label' => false],
                <<<HTML
                <input type="hidden" name="HtmlTestModel[checkbox]" value="0"><input type="checkbox" id="htmltestmodel-checkbox" name="HtmlTestModel[checkbox]" value="1" checked>
                HTML,
            ],
            'uncheck false' => [
                true,
                ['uncheck' => false],
                <<<HTML
                <label><input type="checkbox" id="htmltestmodel-checkbox" name="HtmlTestModel[checkbox]" value="1" checked> Checkbox</label>
                HTML,
            ],
            'uncheck and label false' => [
                true,
                ['uncheck' => false, 'label' => false],
                <<<HTML
                <input type="checkbox" id="htmltestmodel-checkbox" name="HtmlTestModel[checkbox]" value="1" checked>
                HTML,
            ],
        ];
    }

    /**
     * @phpstan-return array<string, array{string, string, array<string, mixed>, string}>
     */
    public static function activeFileInput(): array
    {
        return [
            'custom hiddenOptions id' => [
                <<<HTML
                <input type="hidden" id="specific-id" name="foo" value=""><input type="file" id="htmltestmodel-types" name="foo">
                HTML,
                'types',
                ['name' => 'foo', 'hiddenOptions' => ['id' => 'specific-id']],
                "Generated HTML with custom 'hiddenOptions' id does not match.",
            ],
            'custom hiddenOptions id with default name' => [
                <<<HTML
                <input type="hidden" id="specific-id" name="HtmlTestModel[types]" value=""><input type="file" id="htmltestmodel-types" name="HtmlTestModel[types]">
                HTML,
                'types',
                ['hiddenOptions' => ['id' => 'specific-id']],
                "Generated HTML with custom 'hiddenOptions' id and default name does not match.",
            ],
            'custom name' => [
                <<<HTML
                <input type="hidden" name="foo" value=""><input type="file" id="htmltestmodel-types" name="foo">
                HTML,
                'types',
                ['name' => 'foo'],
                'Generated HTML with custom name does not match.',
            ],
            'custom name with empty hiddenOptions' => [
                <<<HTML
                <input type="hidden" name="foo" value=""><input type="file" id="htmltestmodel-types" name="foo">
                HTML,
                'types',
                ['name' => 'foo', 'hiddenOptions' => []],
                "Generated HTML with custom name and empty 'hiddenOptions' does not match.",
            ],
            'disabled' => [
                <<<HTML
                <input type="hidden" name="foo" value="" disabled><input type="file" id="htmltestmodel-types" name="foo" disabled>
                HTML,
                'types',
                ['name' => 'foo', 'disabled' => true],
                "Generated HTML with 'disabled' option does not match.",
            ],
            'empty hiddenOptions' => [
                <<<HTML
                <input type="hidden" name="HtmlTestModel[types]" value=""><input type="file" id="htmltestmodel-types" name="HtmlTestModel[types]">
                HTML,
                'types',
                ['hiddenOptions' => []],
                "Generated HTML with empty 'hiddenOptions' does not match.",
            ],
        ];
    }
}
