<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\rbac;

use yii\db\Connection;

/**
 * Encapsulates driver-specific referential-integrity actions for the RBAC tables.
 *
 * Databases that support `ON UPDATE CASCADE` and `ON DELETE CASCADE` (MySQL, PostgreSQL) can use the no-op
 * {@see DefaultCascadeStrategy}. Databases that lack native cascade support (SQLite, Oracle, MSSQL) provide their own
 * implementations.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 2.2
 */
interface CascadeStrategyInterface
{
    /**
     * Cascades a rename of an auth item (role or permission) to all referencing tables.
     *
     * Called **before** the item row itself is updated in `auth_item`.
     * Implementations that need a post-update step must return a callable; otherwise return `null`.
     *
     * @param Connection $db the database connection.
     * @param string $oldName the current item name.
     * @param string $newName the new item name.
     * @param string $itemTable the auth item table name.
     * @param string $itemChildTable the auth item child table name.
     * @param string $assignmentTable the auth assignment table name.
     *
     * @return callable|null an optional callback to execute **after** the item row has been renamed, or `null` if no
     * post-update step is needed.
     */
    public function updateItem(
        Connection $db,
        string $oldName,
        string $newName,
        string $itemTable,
        string $itemChildTable,
        string $assignmentTable,
    ): callable|null;

    /**
     * Cascades a rename of a rule to all referencing items.
     *
     * Called **before** the rule row itself is updated in `auth_rule`.
     * Implementations that need a post-update step must return a callable; otherwise return `null`.
     *
     * @param Connection $db the database connection.
     * @param string $oldName the current rule name.
     * @param string $newName the new rule name.
     * @param string $itemTable the auth item table name.
     *
     * @return callable|null an optional callback to execute **after** the rule row has been renamed, or `null` if no
     * post-update step is needed.
     */
    public function updateRule(Connection $db, string $oldName, string $newName, string $itemTable): callable|null;

    /**
     * Removes referencing rows before an auth item is deleted.
     *
     * Called **before** the item row is deleted from `auth_item`.
     *
     * @param Connection $db the database connection.
     * @param string $name the item name being removed.
     * @param string $itemChildTable the auth item child table name.
     * @param string $assignmentTable the auth assignment table name.
     */
    public function removeItem(Connection $db, string $name, string $itemChildTable, string $assignmentTable): void;

    /**
     * Clears rule references before a rule is deleted.
     *
     * Called **before** the rule row is deleted from `auth_rule`.
     *
     * @param Connection $db the database connection.
     * @param string $name the rule name being removed.
     * @param string $itemTable the auth item table name.
     */
    public function removeRule(Connection $db, string $name, string $itemTable): void;

    /**
     * Removes referencing rows before all items of a given type are deleted.
     *
     * Called **before** the item rows are deleted from `auth_item`.
     *
     * @param Connection $db the database connection.
     * @param int $type the auth item type ({@see Item::TYPE_PERMISSION} or {@see Item::TYPE_ROLE}).
     * @param string $itemTable the auth item table name.
     * @param string $itemChildTable the auth item child table name.
     * @param string $assignmentTable the auth assignment table name.
     */
    public function removeAllItems(
        Connection $db,
        int $type,
        string $itemTable,
        string $itemChildTable,
        string $assignmentTable,
    ): void;

    /**
     * Clears all rule references before all rules are deleted.
     *
     * Called **before** the rule rows are deleted from `auth_rule`.
     *
     * @param Connection $db the database connection.
     * @param string $itemTable the auth item table name.
     */
    public function removeAllRules(Connection $db, string $itemTable): void;
}
