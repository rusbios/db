<?php
declare(strict_types = 1);

namespace RB\DB\Builder;

use RB\DB\Exceptions\{OperatorException, PropertyException, QueryException, ConnectException};
use RB\DB\Connects\DBConnetcInterface;
use RB\DB\{DBConnect, DBUtils};

class DB
{
    use WhereTrait;

    private ?string $connectName;

    /**
     * DB constructor.
     * @param string|null $connectName
     */
    public function __construct(?string $connectName)
    {
        $this->connectName = $connectName;
    }

    /**
     * @param string $name
     * @return static
     */
    public static function connect(string $name): self
    {
        return new self($name);
    }

    /**
     * @param string $table
     * @param array $columns
     * @param array $where
     * @param int $offset
     * @param int|null $limit
     * @return array
     * @throws OperatorException
     */
    public static function selected(string $table, array $columns = [], array $where = [], int $offset = 0, int $limit = null): array
    {
        return (new self())->select($table, $columns, $where, $offset, $limit);
    }

    /**
     * @param string $table
     * @param array $values
     * @return int|null
     * @throws PropertyException
     * @throws QueryException
     */
    public static function inserted(string $table, array $values): ?int
    {
        return (new self())->insert($table, $values);
    }

    /**
     * @param string $table
     * @param array $values
     * @param array $where
     * @return int|null
     * @throws OperatorException
     * @throws PropertyException
     */
    public static function updated(string $table, array $values, array $where = []): ?int
    {
        return (new self())->update($table, $values, $where);
    }

    /**
     * @param string $table
     * @param array $where
     * @return int
     * @throws OperatorException
     */
    public static function deleted(string $table, array $where = []): int
    {
        return (new self())->delete($table, $where);
    }

    /**
     * @param string $string
     * @return string
     * @throws ConnectException
     */
    public static function quoted(string $string): string
    {
        return DBConnect::quote($string);
    }

    /**
     * @param string $table
     * @param array $columns
     * @param array $where
     * @param int $offset
     * @param int|null $limit
     * @return array
     * @throws OperatorException
     */
    public function select(string $table, array $columns = [], array $where = [], int $offset = 0, int $limit = null): array
    {
        $qb = QueryBuilder::table($table, $this->connectName)
            ->column($columns)
            ->limit($limit, $offset);

        self::$tmpDbName = null;

        foreach ($where as $key => $value) {
            $qb->where($key, $value);
        }

        try {
            return $qb->get();
        } catch (\Exception $e) {
            throw $this->getLastError();
        }
    }

    /**
     * @param string $table
     * @param array $values
     * @return int|null
     * @throws PropertyException
     * @throws QueryException
     */
    public function insert(string $table, array $values): ?int
    {
        foreach ($values as $key => $value) {
            $keys[] = DBUtils::wrap($key);
            $data[] = DBUtils::formatter($value);
        }

        if (empty($keys) || empty($data)) {
            throw new PropertyException('No variable fed');
        }

        $sql = sprintf(
            'insert into %s (%s) values (%s);',
            DBUtils::wrap($table),
            implode(', ', $keys),
            implode(', ', $data)
        );

        try {
            $id = DBConnect::insert($sql, $this->connectName);
            self::$tmpDbName = null;
        } catch (\Exception $e) {
            throw $this->getLastError();
        }

        return $id;
    }

    /**
     * @param string $table
     * @param array $values
     * @param array $where
     * @return int
     * @throws PropertyException
     * @throws OperatorException
     */
    public function update(string $table, array $values, array $where = []): int
    {
        foreach ($values as $key => $value) {
            $data[] = DBUtils::wrap($key) . ' = ' . DBUtils::formatter($value);
        }

        if (empty($data)) {
            throw new PropertyException('No variable fed');
        }

        $sql = 'update ' . DBUtils::wrap($table) . ' set ' . implode(', ', $data);

        foreach ($where as $key => $value) {
            $wheres[] = self::filter($key, $value);
        }
        if (isset($wheres)) {
            $sql .= ' where ' . implode(' and ', $wheres);
        }

        try {
            $count = DBConnect::updated($sql, $this->connectName);
            self::$tmpDbName = null;
        } catch (\Exception $e) {
            throw $this->getLastError();
        }

        return $count;
    }

    /**
     * @param string $table
     * @param array $where
     * @return int
     * @throws OperatorException
     */
    public function delete(string $table, array $where = []): int
    {
        foreach ($where as $key => $value) {
            $wheres[] = self::filter($key, $value);
        }

        $sql = 'delete from ' . DBUtils::wrap($table) . ' where ' . implode(' and ', $wheres);

        try {
            $count = DBConnect::updated($sql, $this->connectName);
            self::$tmpDbName = null;
        } catch (\Exception $e) {
            throw $this->getLastError();
        }

        return $count;
    }

    /**
     * @return QueryException|null
     * @throws ConnectException
     */
    public function getLastError(): ?QueryException
    {
        return DBConnect::error($this->connectName);
    }
}