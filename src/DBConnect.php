<?php
declare(strict_types = 1);

namespace RB\DB;

use PDO;
use RB\DB\Exceptions\ConnectException;
use RB\DB\Exceptions\QueryException;

class DBConnect
{
    public const TYPE_MYSQL = 'mysql';
    public const TYPE_LITESQL = 'litesql';

    /** @var self[] */
    private static array $connections;
    private static ?string $defaultName;

    private PDO $connect;

    /**
     * @param string $type
     * @param string $name
     * @param array $config
     * @return static
     * @throws ConnectException
     */
    public static function created(string $type, string $name, array $config): self
    {
        if (empty(self::$connections[$name])) {
            self::$connections[$name] = new self($type, $config);
            if (empty(self::$defaultName)) {
                self::setDetaultName($name);
            }
        }

        return self::get($name);
    }

    /**
     * @param string|null $name
     * @return static|null
     */
    public static function get(string $name = null): ?self
    {
        return self::$connections[$name ?: self::$defaultName] ?? null;
    }

    public static function setDetaultName(string $name): void
    {
        self::$defaultName = $name;
    }

    /**
     * DBConnect constructor.
     * @param string $type
     * @param array $config
     * @throws ConnectException
     */
    public function __construct(string $type, array $config)
    {
        switch (strtolower($type)) {
            case self::TYPE_MYSQL:
                if (empty($config['host'])
                    || empty($config['dbName'])
                    || empty($config['user'])
                    || empty($config['password'])
                ) {
                    throw new ConnectException('Config not found');
                }

                $this->connect = new PDO(
                    sprintf(
                        'mysql:host=%s;port=%s;dbname=%s',
                        $config['host'],
                        $config['port'] ?? 3306,
                        $config['dbName']),
                    $config['user'],
                    $config['password']
                );
                break;

            case self::TYPE_LITESQL:
                if (empty($config['path']) || !file_exists($config['path'])) {
                    throw new ConnectException('File BD not found');
                }

                $this->connect = new PDO('sqlite:'.$config['path']);
                break;
        }

        throw new ConnectException('Type connect BD not found');
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
     * @return int|null
     * @throws QueryException
     */
    public function insert(string $sql): ?int
    {
        if ($this->connect->query($sql) === false) {
            throw new QueryException('Error sql "' . $sql . '"');
        }
        return (int)$this->connect->lastInsertId() ?? null;
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