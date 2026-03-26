<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yiiunit\framework\grid;

use Yii;
use yii\data\ArrayDataProvider;
use yii\grid\CheckboxColumn;
use yii\grid\GridView;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yiiunit\framework\i18n\IntlTestHelper;
use yiiunit\TestCase;

/**
 * @group grid
 */
class CheckboxColumnTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        IntlTestHelper::resetIntlStatus();
        $this->mockApplication();
        Yii::setAlias('@webroot', '@yiiunit/runtime');
        Yii::setAlias('@web', 'http://localhost/');
        FileHelper::createDirectory(Yii::getAlias('@webroot/assets'));
        Yii::$app->assetManager->bundles['yii\web\JqueryAsset'] = false;
    }

    public function testInputName(): void
    {
        $column = new CheckboxColumn(['name' => 'selection', 'grid' => $this->getGrid()]);
        $this->assertStringContainsString('name="selection_all"', $column->renderHeaderCell());

        $column = new CheckboxColumn(['name' => 'selections[]', 'grid' => $this->getGrid()]);
        $this->assertStringContainsString('name="selections_all"', $column->renderHeaderCell());

        $column = new CheckboxColumn(['name' => 'MyForm[grid1]', 'grid' => $this->getGrid()]);
        $this->assertStringContainsString('name="MyForm[grid1_all]"', $column->renderHeaderCell());

        $column = new CheckboxColumn(['name' => 'MyForm[grid1][]', 'grid' => $this->getGrid()]);
        $this->assertStringContainsString('name="MyForm[grid1_all]"', $column->renderHeaderCell());

        $column = new CheckboxColumn(['name' => 'MyForm[grid1][key]', 'grid' => $this->getGrid()]);
        $this->assertStringContainsString('name="MyForm[grid1][key_all]"', $column->renderHeaderCell());

        $column = new CheckboxColumn(['name' => 'MyForm[grid1][key][]', 'grid' => $this->getGrid()]);
        $this->assertStringContainsString('name="MyForm[grid1][key_all]"', $column->renderHeaderCell());
    }

    public function testUnselectHiddenInput(): void
    {
        $column = new CheckboxColumn(['grid' => $this->getGrid()]);
        $headerCell = $column->renderHeaderCell();

        self::assertStringContainsString(
            'type="hidden"',
            $headerCell,
            'Header cell should render hidden unselect input by default.',
        );
        self::assertStringContainsString(
            'name="selection" value=""',
            $headerCell,
            'Hidden unselect input should use checkbox name without [] suffix.',
        );

        $column = new CheckboxColumn(['name' => 'MyForm[grid1][]', 'grid' => $this->getGrid()]);
        $headerCell = $column->renderHeaderCell();

        self::assertStringContainsString(
            'name="MyForm[grid1]" value=""',
            $headerCell,
            'Hidden unselect input should preserve nested input names.',
        );
    }

    public function testUnselectCanBeDisabled(): void
    {
        $column = new CheckboxColumn(['unselect' => null, 'grid' => $this->getGrid()]);
        $headerCell = $column->renderHeaderCell();

        self::assertStringNotContainsString(
            'type="hidden"',
            $headerCell,
            'Hidden unselect input should not be rendered when unselect is null.',
        );
    }

    public function testUnselectHiddenInputPropagatesCheckboxAttributes(): void
    {
        $column = new CheckboxColumn([
            'checkboxOptions' => [
                'disabled' => true,
                'form' => 'bulk-form',
            ],
            'grid' => $this->getGrid(),
        ]);
        $headerCell = $column->renderHeaderCell();

        self::assertStringContainsString(
            'type="hidden"',
            $headerCell,
            'Header cell should include hidden unselect input.',
        );
        self::assertStringContainsString(
            'name="selection" value=""',
            $headerCell,
            'Hidden unselect input should target the base checkbox name.',
        );
        self::assertStringContainsString(
            'disabled',
            $headerCell,
            'Hidden unselect input should propagate disabled attribute from checkboxOptions.',
        );
        self::assertStringContainsString(
            'form="bulk-form"',
            $headerCell,
            'Hidden unselect input should propagate form attribute from checkboxOptions.',
        );
    }

    public function testUnselectWithoutHeaderIsRenderedOnce(): void
    {
        $grid = $this->getGrid([
            'showHeader' => false,
            'dataProvider' => new ArrayDataProvider([
                'allModels' => [['id' => 1], ['id' => 2]],
                'key' => 'id',
            ]),
        ]);
        $column = new CheckboxColumn(['grid' => $grid]);

        $content = $column->renderDataCell(['id' => 1], 1, 0) . $column->renderDataCell(['id' => 2], 2, 1);

        self::assertSame(
            1,
            substr_count($content, 'type="hidden"'),
            'Hidden unselect input should be rendered only once when header is disabled.',
        );
        self::assertStringContainsString(
            'name="selection" value=""',
            $content,
            'Hidden unselect input should still be rendered when showHeader is false.',
        );
    }

    public function testInputValue(): void
    {
        $column = new CheckboxColumn(['grid' => $this->getGrid()]);
        $this->assertStringContainsString('value="1"', $column->renderDataCell([], 1, 0));
        $this->assertStringContainsString('value="42"', $column->renderDataCell([], 42, 0));
        $this->assertStringContainsString('value="[1,42]"', $column->renderDataCell([], [1, 42], 0));

        $column = new CheckboxColumn(['checkboxOptions' => ['value' => 42], 'grid' => $this->getGrid()]);
        $this->assertStringNotContainsString('value="1"', $column->renderDataCell([], 1, 0));
        $this->assertStringContainsString('value="42"', $column->renderDataCell([], 1, 0));

        $column = new CheckboxColumn([
            'checkboxOptions' => function ($model, $key, $index, $column) {
                return [];
            },
            'grid' => $this->getGrid(),
        ]);
        $this->assertStringContainsString('value="1"', $column->renderDataCell([], 1, 0));
        $this->assertStringContainsString('value="42"', $column->renderDataCell([], 42, 0));
        $this->assertStringContainsString('value="[1,42]"', $column->renderDataCell([], [1, 42], 0));

        $column = new CheckboxColumn([
            'checkboxOptions' => function ($model, $key, $index, $column) {
                return ['value' => 42];
            },
            'grid' => $this->getGrid(),
        ]);
        $this->assertStringNotContainsString('value="1"', $column->renderDataCell([], 1, 0));
        $this->assertStringContainsString('value="42"', $column->renderDataCell([], 1, 0));
    }

    public function testContent(): void
    {
        $column = new CheckboxColumn([
            'content' => function ($model, $key, $index, $column) {
                return null;
            },
            'grid' => $this->getGrid(),
        ]);
        $this->assertStringContainsString('<td></td>', $column->renderDataCell([], 1, 0));

        $column = new CheckboxColumn([
            'content' => function ($model, $key, $index, $column) {
                return Html::checkBox('checkBoxInput', false);
            },
            'grid' => $this->getGrid(),
        ]);
        $this->assertStringContainsString(Html::checkBox('checkBoxInput', false), $column->renderDataCell([], 1, 0));
    }

    /**
     * @return GridView a mock gridview
     */
    protected function getGrid(array $config = [])
    {
        return new GridView(array_merge([
            'dataProvider' => new ArrayDataProvider(['allModels' => [], 'totalCount' => 0]),
        ], $config));
    }
}
