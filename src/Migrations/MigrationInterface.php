<?php
declare(strict_types = 1);

namespace RB\DB\Migrations;

use RB\DB\Migrate\Schema;
use RB\DB\Migrate\Table;

interface MigrationInterface
{
    public function up(Schema $schema, Table $table): void;

    public function down(Schema $schema): void;
}