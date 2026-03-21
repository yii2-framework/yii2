<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yiiunit\framework\rbac;

use PHPUnit\Framework\Attributes\Group;
use yiiunit\data\rbac\UserID;

/**
 * Unit test for {@see \yii\rbac\DbManager} with Oracle driver.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 2.2
 */
#[Group('db')]
#[Group('rbac')]
#[Group('oci')]
class OciManagerTest extends DbManagerTestCase
{
    protected static $driverName = 'oci';

    /**
     * Oracle treats empty strings as `null`, so the empty-string data set is excluded.
     */
    public static function emptyValuesProvider(): array
    {
        return [
            [0, 0, true],
            [0, new UserID(0), true],
        ];
    }
}
