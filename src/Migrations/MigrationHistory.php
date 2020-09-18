<?php
declare(strict_types = 1);

namespace RB\DB\Migrations;

use RB\DB\Migrate\{MigrateHistoryModel, Schema, Table};
use RB\DB\Exceptions\ConnectException;

class MigrationHistory extends Migration
{
    /**
     * @param Schema $schema
     * @param Table $table
     * @throws ConnectException
     */
    public function up(Schema $schema, Table $table): void
    {
        if ($schema->hasTable(MigrateHistoryModel::getTable())) {
            return;
        }

        $table->setTable(MigrateHistoryModel::getTable())
            ->id()
            ->string('class_name')->unique()
            ->timestamps();

        $schema->create($table);
    }

    /**
     * @param Schema $schema
     * @throws ConnectException
     */
    public function down(Schema $schema): void
    {
        $schema->drop(MigrateHistoryModel::getTable());
    }
}