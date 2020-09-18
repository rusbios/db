<?php
declare(strict_types = 1);

namespace RB\DB\Migrate;

use RB\DB\{DBConnect, DBUtils};
use RB\DB\Exceptions\ConnectException;
use Exception;
use PDO;

class Schema
{
    /**
     * @param Table $table
     * @param string|null $connectName
     * @throws ConnectException
     */
    public function create(Table $table, ?string $connectName): void
    {
        DBConnect::query($table->sql(), PDO::FETCH_LAZY, $connectName);
    }

    /**
     * @param Table $table
     * @param string|null $connectName
     * @throws ConnectException
     */
    public function modify(Table $table, ?string $connectName): void
    {
        DBConnect::query($table->sql(), PDO::FETCH_LAZY, $connectName);
    }

    /**
     * @param string $tableName
     * @param string|null $connectName
     * @throws ConnectException
     */
    public function drop(string $tableName, ?string $connectName): void
    {
        DBConnect::query(sprintf('drop table %s;', DBUtils::wrap($tableName)), PDO::FETCH_LAZY, $connectName);
    }

    /**
     * @param string $table
     * @param string|null $connectName
     * @return bool
     */
    public function hasTable(string $table, ?string $connectName): bool
    {
        try {
            $res = DBConnect::query(sprintf(
                'show tables like \'%s\'',
                trim($table)
            ), PDO::FETCH_LAZY, $connectName);
            return !empty($res);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param string $table
     * @param string $column
     * @param string|null $connectName
     * @return bool
     */
    public function hasColumn(string $table, string $column, ?string $connectName): bool
    {
        try {
            $res = DBConnect::query(sprintf(
                'show columns from %s like %s',
                DBUtils::wrap($table),
                DBUtils::wrap($column)
            ), PDO::FETCH_LAZY, $connectName);
            return !empty($res);
        } catch (Exception $e) {
            return false;
        }
    }
}