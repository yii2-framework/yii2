<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yii\db\oci;

use yii\db\Expression;
use yii\db\PdoValue;

use function is_string;
use function str_replace;
use function uniqid;

/**
 * Class ColumnSchema for Oracle database.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 2.2
 */
class ColumnSchema extends \yii\db\ColumnSchema
{
    /**
     * {@inheritdoc}
     *
     * Converts string values for BLOB columns to `TO_BLOB(UTL_RAW.CAST_TO_RAW(:placeholder))` expressions to avoid
     * direct string binding errors in Oracle when inserting into BLOB columns.
     */
    public function dbTypecast($value)
    {
        if ($this->type === Schema::TYPE_BINARY && $this->dbType === 'BLOB') {
            if ($value instanceof PdoValue && is_string($value->getValue())) {
                $value = $value->getValue();
            }

            if (is_string($value)) {
                $placeholder = 'qp' . str_replace('.', '', uniqid('', true));

                return new Expression(
                    'TO_BLOB(UTL_RAW.CAST_TO_RAW(:' . $placeholder . '))',
                    [$placeholder => $value]
                );
            }
        }

        return parent::dbTypecast($value);
    }
}
