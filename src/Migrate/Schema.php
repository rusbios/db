<?php
declare(strict_types = 1);

namespace RB\DB\Migrate;

use RB\DB\Connects\DBConnetcInterface;
use RB\DB\DBUtils;
use Exception;

class Schema
{
    private DBConnetcInterface $DBConnetc;
    
    public function __construct(DBConnetcInterface $DBConnetc)
    {
        $this->DBConnetc = $DBConnetc;
    }

    public function create(Table $table): void
    {
        $this->DBConnetc->query($table->sql());
    }

    public function modify(Table $table): void
    {
        $this->DBConnetc->query($table->sql());
    }

    public function drop(string $tableName): void
    {
        $this->DBConnetc->query(sprintf('drop table %s;', DBUtils::wrap($tableName)));
    }

    public function hasTable(string $table): bool
    {
        try {
            $res = $this->DBConnetc->query(sprintf(
                'show tables like \'%s\'',
                trim($table)
            ));
            return !empty($res);
        } catch (Exception $e) {
            return false;
        }
    }

    public function hasColumn(string $table, string $column): bool
    {
        try {
            $res = $this->DBConnetc->query(sprintf(
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