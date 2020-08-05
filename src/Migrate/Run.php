<?php
declare(strict_types = 1);

namespace RB\DB\Migrate;

use Exception;
use RB\DB\Connects\DBConnetcInterface;
use RB\DB\Migration;

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
     * @param DBConnetcInterface $db
     * @throws Exception
     */
    public static function applay(DBConnetcInterface $db): void
    {
        $schema = new Schema($db);

        foreach (self::$migrations as $class) {
            /** @var Migration $migrate */
            $migrate = new $class();

            try {
                $migrate->up($schema, new Table());
                // todo save migrate run
            } catch (Exception $e) {
                $migrate->down($schema);
                throw $e;
            }
        }
    }
}