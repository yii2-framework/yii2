<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

use yii\base\InvalidConfigException;
use yii\rbac\DbManager;

/**
 * Initializes RBAC tables.
 *
 * @author Alexander Kochetov <creocoder@gmail.com>
 * @since 2.0
 */
class m140506_102106_rbac_init extends \yii\db\Migration
{
    /**
     * @throws yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException(
                'You should configure "authManager" component to use database before executing this migration.',
            );
        }

        return $authManager;
    }

    /**
     * @return bool
     */
    protected function isMSSQL()
    {
        return in_array($this->db->driverName, ['mssql', 'sqlsrv', 'dblib'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $authManager = $this->getAuthManager();

        $this->db = $authManager->db;

        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            $authManager->ruleTable,
            [
                'name' => $this->string(64)->notNull(),
                'data' => $this->binary(),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
                'PRIMARY KEY ([[name]])',
            ],
            $tableOptions,
        );
        $this->createTable(
            $authManager->itemTable,
            [
                'name' => $this->string(64)->notNull(),
                'type' => $this->smallInteger()->notNull(),
                'description' => $this->text(),
                'rule_name' => $this->string(64),
                'data' => $this->binary(),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
                'PRIMARY KEY ([[name]])',
                'FOREIGN KEY ([[rule_name]]) REFERENCES '
                . $authManager->ruleTable
                . ' ([[name]])'
                . $this->buildFkClause('ON DELETE SET NULL', 'ON UPDATE CASCADE'),
            ],
            $tableOptions,
        );
        $this->createIndex(
            'idx-auth_item-type',
            $authManager->itemTable,
            'type',
        );
        $this->createTable(
            $authManager->itemChildTable,
            [
                'parent' => $this->string(64)->notNull(),
                'child' => $this->string(64)->notNull(),
                'PRIMARY KEY ([[parent]], [[child]])',
                'FOREIGN KEY ([[parent]]) REFERENCES '
                . $authManager->itemTable
                . ' ([[name]])'
                . $this->buildFkClause('ON DELETE CASCADE', 'ON UPDATE CASCADE'),
                'FOREIGN KEY ([[child]]) REFERENCES '
                . $authManager->itemTable
                . ' ([[name]])'
                . $this->buildFkClause('ON DELETE CASCADE', 'ON UPDATE CASCADE'),
            ],
            $tableOptions,
        );
        $this->createTable(
            $authManager->assignmentTable,
            [
                'item_name' => $this->string(64)->notNull(),
                'user_id' => $this->string(64)->notNull(),
                'created_at' => $this->integer(),
                'PRIMARY KEY ([[item_name]], [[user_id]])',
                'FOREIGN KEY ([[item_name]]) REFERENCES '
                . $authManager->itemTable
                . ' ([[name]])'
                . $this->buildFkClause('ON DELETE CASCADE', 'ON UPDATE CASCADE'),
            ],
            $tableOptions,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $authManager = $this->getAuthManager();

        $this->db = $authManager->db;

        $this->dropTable($authManager->assignmentTable);
        $this->dropTable($authManager->itemChildTable);
        $this->dropTable($authManager->itemTable);
        $this->dropTable($authManager->ruleTable);
    }

    /**
     * Builds the FK clause for the current driver.
     *
     * - MySQL, PostgreSQL: both ON DELETE and ON UPDATE CASCADE.
     * - Oracle, SQLite: ON DELETE only (no ON UPDATE CASCADE support).
     * - MSSQL: no FK actions (multiple cascade paths cause cycles; handled by {@see SoftCascadeStrategy}).
     *
     * @param string $delete the ON DELETE clause.
     * @param string $update the ON UPDATE clause.
     * @return string
     */
    protected function buildFkClause($delete = '', $update = '')
    {
        if ($this->isMSSQL()) {
            return '';
        }

        if (in_array($this->db->driverName, ['oci', 'oci8', 'sqlite', 'sqlite2'], true)) {
            return ' ' . $delete;
        }

        return implode(' ', ['', $delete, $update]);
    }
}
