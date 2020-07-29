<?php
declare(strict_types = 1);

namespace RB\DB\Builder;

use PDO;
use RB\DB\Exceptions\QueryException;

class PDOConnect
{
    protected PDO $connect;

    /**
     * PDOConnect constructor.
     * @param string $driver
     * @param string $host
     * @param string $dbName
     * @param string $user
     * @param string $password
     * @param int $port
     */
    public function __construct(string $driver, string $host, string $dbName, string $user, string $password, int $port)
    {
        $this->connect = new PDO(
            sprintf('%s:host=%s;port=%i;dbname=%s', $driver, $host, $port, $dbName),
            $user,
            $password
        );
    }

    /**
     * @param string $sql
     * @return array
     */
    public function query(string $sql): array
    {
        return $this->connect->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     * @return int
     * @throws QueryException
     */
    public function insert(string $sql): int
    {
        if ($this->connect->query($sql) === false) {
            throw new QueryException('Error sql "' . $sql . '"');
        }
        return (int)$this->connect->lastInsertId();
    }

    /**
     * @param string $sql
     * @return int
     */
    public function updated(string $sql): int
    {
        return $this->connect->query($sql)->rowCount();
    }
}