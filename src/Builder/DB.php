<?php
declare(strict_types = 1);

namespace RB\DB\Builder;

use RB\DB\Exceptions\{OperatorException, PropertyException, QueryException};
use RB\DB\Connects\DBConnetcInterface;
use RB\DB\DBConnect;
use RB\DB\DBUtils;

class DB
{
    use WhereTrait;

    private static ?string $tmpDbName;

    /**
     * @param string $name
     * @return static
     */
    public static function connect(string $name): self
    {
        self::$tmpDbName = $name;
        return self;
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
    public static function select(string $table, array $columns = [], array $where = [], int $offset = 0, int $limit = null): array
    {
        QueryBuilder::setConnect(DBConnect::get(self::$tmpDbName));
        $qb = QueryBuilder::table($table)
            ->column($columns)
            ->limit($limit, $offset);

        self::$tmpDbName = null;

        foreach ($where as $key => $value) {
            $qb->where($key, $value);
        }

        try {
            return $qb->get();
        } catch (\Exception $e) {
            throw self::getLastError();
        }
    }

    /**
     * @param string $table
     * @param array $values
     * @return int|null
     * @throws PropertyException
     * @throws QueryException
     */
    public static function insert(string $table, array $values): ?int
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
            $id = DBConnect::get(self::$tmpDbName)->insert($sql);
            self::$tmpDbName = null;
        } catch (\Exception $e) {
            throw self::getLastError();
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
    public static function update(string $table, array $values, array $where = []): int
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
            $count = DBConnect::get(self::$tmpDbName)->updated($sql);
            self::$tmpDbName = null;
        } catch (\Exception $e) {
            throw self::getLastError();
        }

        return $count;
    }

    /**
     * @param string $table
     * @param array $where
     * @return int
     * @throws OperatorException
     */
    public static function deleted(string $table, array $where = []): int
    {
        foreach ($where as $key => $value) {
            $wheres[] = self::filter($key, $value);
        }

        $sql = 'delete from ' . DBUtils::wrap($table) . ' where ' . implode(' and ', $wheres);

        try {
            $count = DBConnect::get(self::$tmpDbName)->updated($sql);
            self::$tmpDbName = null;
        } catch (\Exception $e) {
            throw self::getLastError();
        }

        return $count;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function quote(string $string): string
    {
        return DBConnect::get()->quote($string);
    }

    /**
     * @return QueryException|null
     */
    public static function getLastError(): ?QueryException
    {
        return DBConnect::get(self::$tmpDbName)->error();
    }
}