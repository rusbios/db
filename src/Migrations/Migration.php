<?php
declare(strict_types = 1);

namespace RB\DB\Migrations;

use RB\DB\Migrate\Schema;
use RB\DB\Migrate\Table;

abstract class Migration
{
    /** @var string|null */
    protected ?string $connectName;

    public abstract function up(Schema $schema, Table $table): void;

    public abstract function down(Schema $schema): void;
}