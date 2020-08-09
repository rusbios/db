<?php
declare(strict_types = 1);

namespace RB\DB\Migrate;

use Exception;
use RB\DB\Connects\DBConnetcInterface;
use RB\DB\{DBUtils, Migration};
use RB\DB\Migrations\MigrationHistory;

class Run
{
    private static array $migrations;

    /**
     * @param string $class
     * @throws Exception
     */
    public static function add(string $class): void
    {
        if (!class_exists($class)) {
            throw new Exception(sprintf('Migration "%s" not found', $class));
        }

        self::$migrations[] = $class;
    }

    /**
     * @throws Exception
     */
    public static function applay(): void
    {
        $schema = new Schema();

        (new MigrationHistory())->up($schema, new Table());

        foreach (self::$migrations as $class) {

            if (MigrateHistoryModel::getByModel($class)) {
                continue;
            }

            /** @var Migration $migrate */
            $migrate = new $class();

            try {
                $migrate->up($schema, new Table());

                $history = new MigrateHistoryModel();
                $history->class_name = $class;
                $history->save();

            } catch (Exception $e) {
                $migrate->down($schema);
                throw $e;
            }
        }
    }
}