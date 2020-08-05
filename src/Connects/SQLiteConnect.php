<?php
declare(strict_types = 1);

namespace RB\DB\Connects;

class SQLiteConnect extends PDOConnect
{
    /**
     * SQLiteConnect constructor.
     * @param string $path
     * @throws \Exception
     */
    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            throw new \Exception('File BD not found');
        }

        $this->connect = new PDO('sqlite:'.$path);
    }
}