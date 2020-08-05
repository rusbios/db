<?php
declare(strict_types = 1);

namespace RB\DB\Migrate;

use RB\DB\Connects\DBConnetcInterface;
use RB\DB\DBUtils;

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
}