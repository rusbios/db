<?php
declare(strict_types = 1);

namespace RB\DB;

use PDO;
use RB\DB\Exceptions\ConnectException;
use RB\DB\Exceptions\QueryException;
use RB\DB\Exceptions\ValidationException;

class DBConnect
{
    public const DB_TYPE_MYSQL = 'mysql';
    public const DB_TYPE_POSTGRESQL = 'postgresql';
    public const DB_TYPE_SQLITE = 'sqlite';

    /** @var PDO[] */
    protected static array $connections;

    /** @var string|null */
    protected static ?string $defaultNameConnect;

    /**
     * @param string $name
     * @param array $config
     * @param bool $default
     * @throws ValidationException
     */
    public static function addConnect(string $name, array $config, bool $default = false): void
    {
        if (empty($config['type']) || strlen($name) <= 3 || !empty(self::$connections[$name])) {
            throw new ValidationException('Invalid config data');
        }

        switch (strtolower($config['type'])) {
            case self::DB_TYPE_MYSQL:
                if (
                    empty($config['host'])
                    || empty($config['dbName'])
                    || empty($config['user'])
                    || empty($config['password'])
                ) {
                    throw new ValidationException('MySQL configuration not found');
                }

                self::$connections[$name] = new PDO(
                    sprintf(
                        'mysql:host=%s;port=%s;dbname=%s',
                        $config['host'],
                        $config['port'] ?? 3306,
                        $config['dbName']
                    ),
                    $config['user'],
                    $config['password']
                );
                break;

            case self::DB_TYPE_SQLITE:
                if (empty($config['path']) || !file_exists($config['path'])) {
                    throw new ValidationException('File BD not found');
                }

                self::$connections[$name] = new PDO('sqlite:'.$config['path']);
                break;

            case self::DB_TYPE_POSTGRESQL:
                if (
                    empty($config['host'])
                    || empty($config['dbName'])
                    || empty($config['user'])
                    || empty($config['password'])
                ) {
                    throw new ValidationException('PostgreSql configuration not found');
                }

                self::$connections[$name] = new PDO(
                    sprintf(
                        'pgsql:host=%s;port=%s;dbname=%s;',
                        $config['host'],
                        $config['port'] ?? 5432,
                        $config['dbName']
                    ),
                    $config['user'],
                    $config['password']
                );
                break;

            default:
                throw new ValidationException('Type connectioon not found');
        }

        if ($default) {
            self::$defaultNameConnect = $name;
        }
    }

    /**
     * @param string|null $connectName
     * @return PDO|null
     * @throws ConnectException
     */
    protected static function getConnect(?string $connectName = null): ?PDO
    {
        if (!$connectName) {
            $connectName = empty(self::$defaultNameConnect)
                ? array_key_first(self::$connections)
                : self::$defaultNameConnect;
        }

        if (!self::$connections[$connectName]) {
            throw new ConnectException('DB connect not found');
        }

        return self::$connections[$connectName];
    }

    /**
     * @param string $sql
     * @param int $pdoFetch
     * @param string|null $connectName
     * @return array
     * @throws ConnectException
     */
    public static function query(string $sql, int $pdoFetch = PDO::FETCH_ASSOC, ?string $connectName = null): array
    {
        return self::getConnect($connectName)->query($sql)->fetchAll($pdoFetch);
    }

    /**
     * @param string|null $connectName
     * @return bool
     * @throws ConnectException
     */
    public static function beginTransaction(?string $connectName = null): bool
    {
        return self::getConnect($connectName)->beginTransaction();
    }

    /**
     * @param string $sql
     * @param string|null $connectName
     * @return false|int
     * @throws ConnectException
     */
    public static function exec(string $sql, ?string $connectName = null): false|int
    {
        return self::getConnect($connectName)->exec($sql);
    }

    /**
     * @param string|null $connectName
     * @return bool
     * @throws ConnectException
     */
    public static function commit(?string $connectName = null): bool
    {
        return self::getConnect($connectName)->commit();
    }

    /**
     * @param string $sql
     * @param string|null $connectName
     * @return int|null
     * @throws ConnectException
     * @throws QueryException
     */
    public static function insert(string $sql, ?string $connectName = null): ?int
    {
        if (self::getConnect($connectName)->query($sql) === false) {
            throw new QueryException('Error sql "' . $sql . '"');
        }
        return (int)self::getConnect($connectName)->lastInsertId() ?? null;
    }

    /**
     * @param string $sql
     * @param string|null $connectName
     * @return false|int
     * @throws ConnectException
     * @throws QueryException
     */
    public static function updated(string $sql, ?string $connectName = null): false|int
    {
        if ($res = self::getConnect($connectName)->query($sql) === false) {
            throw new QueryException('Error sql "' . $sql . '"');
        }
        return $res->rowCount();
    }

    /**
     * @param string $string
     * @param string|null $connectName
     * @return false|string
     * @throws ConnectException
     */
    public static function quote(string $string, ?string $connectName = null): string|false
    {
        return self::getConnect($connectName)->quote($string);
    }

    /**
     * @param string|null $connectName
     * @return QueryException
     * @throws ConnectException
     */
    public static function error(?string $connectName = null): QueryException
    {
        $connect = self::getConnect($connectName);
        return new QueryException(
            'Query error: ' . implode(' : ', $connect->errorInfo()),
            $connect->errorCode()
        );
    }
}