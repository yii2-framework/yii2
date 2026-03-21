<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\rbac;

use yii\db\Connection;
use yii\db\Query;

/**
 * Cascade strategy for SQLite, which does not enforce foreign key constraints by default.
 *
 * Performs manual UPDATE/DELETE on referencing rows before the parent row is modified.
 * Because SQLite does not enforce FK constraints, the update order does not matter.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 2.2
 */
final class SqliteCascadeStrategy implements CascadeStrategyInterface
{
    public function updateItem(
        Connection $db,
        string $oldName,
        string $newName,
        string $itemTable,
        string $itemChildTable,
        string $assignmentTable,
    ): callable|null {
        $db->createCommand()
            ->update(
                $itemChildTable,
                ['parent' => $newName],
                ['parent' => $oldName],
            )
            ->execute();
        $db->createCommand()
            ->update(
                $itemChildTable,
                ['child' => $newName],
                ['child' => $oldName],
            )
            ->execute();
        $db->createCommand()
            ->update(
                $assignmentTable,
                ['item_name' => $newName],
                ['item_name' => $oldName],
            )
            ->execute();

        return null;
    }

    public function updateRule(Connection $db, string $oldName, string $newName, string $itemTable): callable|null
    {
        $db->createCommand()
            ->update(
                $itemTable,
                ['rule_name' => $newName],
                ['rule_name' => $oldName],
            )
            ->execute();

        return null;
    }

    public function removeItem(Connection $db, string $name, string $itemChildTable, string $assignmentTable): void
    {
        $db->createCommand()
            ->delete(
                $itemChildTable,
                ['or', '[[parent]]=:parent', '[[child]]=:child'],
                [':parent' => $name, ':child' => $name],
            )
            ->execute();
        $db->createCommand()
            ->delete(
                $assignmentTable,
                ['item_name' => $name],
            )
            ->execute();
    }

    public function removeRule(Connection $db, string $name, string $itemTable): void
    {
        $db->createCommand()
            ->update(
                $itemTable,
                ['rule_name' => null],
                ['rule_name' => $name],
            )
            ->execute();
    }

    public function removeAllItems(
        Connection $db,
        int $type,
        string $itemTable,
        string $itemChildTable,
        string $assignmentTable,
    ): void {
        $names = (new Query())
            ->select(['name'])
            ->from($itemTable)
            ->where(['type' => $type])
            ->column($db);

        if ($names === []) {
            return;
        }

        $key = $type === Item::TYPE_PERMISSION ? 'child' : 'parent';

        $db->createCommand()
            ->delete(
                $itemChildTable,
                [$key => $names],
            )
            ->execute();
        $db->createCommand()
            ->delete(
                $assignmentTable,
                ['item_name' => $names],
            )
            ->execute();
    }

    public function removeAllRules(Connection $db, string $itemTable): void
    {
        $db->createCommand()
            ->update(
                $itemTable,
                ['rule_name' => null],
            )
            ->execute();
    }
}
