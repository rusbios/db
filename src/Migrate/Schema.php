<?php
declare(strict_types = 1);

namespace RB\DB\Migrate;

use RB\DB\{DBConnect, DBUtils};
use Exception;

class Schema
{
    /**
     * @param Table $table
     * @param string|null $connectName
     */
    public function create(Table $table, ?string $connectName): void
    {
        DBConnect::get($connectName)->query($table->sql());
    }

    public function modify(Table $table, ?string $connectName): void
    {
        DBConnect::get($connectName)->query($table->sql());
    }

    public function drop(string $tableName, ?string $connectName): void
    {
        DBConnect::get($connectName)->query(sprintf('drop table %s;', DBUtils::wrap($tableName)));
    }

    public function hasTable(string $table, ?string $connectName): bool
    {
        try {
            $res = DBConnect::get($connectName)->query(sprintf(
                'show tables like \'%s\'',
                trim($table)
            ));
            return !empty($res);
        } catch (Exception $e) {
            return false;
        }
    }

    public function hasColumn(string $table, string $column, ?string $connectName): bool
    {
        try {
            $res = DBConnect::get($connectName)->query(sprintf(
                'show columns from %s like %s',
                DBUtils::wrap($table),
                DBUtils::wrap($column)
            ));
            return !empty($res);
        } catch (Exception $e) {
            return false;
        }
    }
}