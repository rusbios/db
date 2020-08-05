<?php
declare(strict_types = 1);

namespace RB\DB\Connects;

use PDO;
use RB\DB\Exceptions\QueryException;

class MySQLConnect implements DBConnetcInterface
{
    protected PDO $connect;

    /**
     * PDOConnect constructor.
     * @param string $host
     * @param string $dbName
     * @param string $user
     * @param string $password
     * @param int $port
     */
    public function __construct(string $host, string $dbName, string $user, string $password, int $port = 3306)
    {
        $this->connect = new PDO(
            sprintf('mysql:host=%s;port=%s;dbname=%s', $host, $port, $dbName),
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

    /**
     * @param string $string
     * @return string
     */
    public function quote(string $string): string
    {
        return $this->connect->quote($string);
    }

    /**
     * @return QueryException
     */
    public function error(): QueryException
    {
        return new QueryException(
            'Query error: ' . implode(' : ', $this->connect->errorInfo()),
            $this->connect->errorCode()
        );
    }
}