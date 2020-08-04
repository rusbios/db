<?php
declare(strict_types = 1);

namespace RB\DB\Migrate;

use RB\DB\Connects\DBConnetcInterface;

class Schema
{
    private DBConnetcInterface $DBConnetc;

    /** @var Table[] */
    private iterable $created;

    /** @var Table[] */
    private iterable $modify;

    /** @var string[] */
    private array $drops;
    
    public function __construct(DBConnetcInterface $DBConnetc)
    {
        $this->DBConnetc = $DBConnetc;
    }

    protected function create(string $tableName, Table $table): void
    {

    }

    protected function modify(string $tableName, Table $table): void
    {

    }

    protected function drop(string $tableName): void
    {

    }

    public function run()
    {
        $table = new Table();
    }
}