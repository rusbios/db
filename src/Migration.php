<?php
declare(strict_types = 1);

namespace RB\DB;

use RB\DB\Migrate\Schema;
use RB\DB\Migrate\Table;

abstract class Migration
{
    public abstract function up(Schema $schema, Table $table): void;

    public abstract function down(Schema $schema): void;
}