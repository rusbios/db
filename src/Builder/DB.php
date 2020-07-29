<?php
declare(strict_types = 1);

namespace RB\DB\Builder;

use RB\DB\Exceptions\{OperatorException, PropertyException, QueryException};
use RB\DB\DBUtils;

class DB
{
    use WhereTrait;

    private static PDOConnect $connect;

    /**
     * @param PDOConnect $connect
     */
    public static function setConnect(PDOConnect $connect): void
    {
        self::$connect = $connect;
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
        QueryBuilder::setConnect(self::$connect);
        $qb = QueryBuilder::table($table)
            ->column($columns)
            ->limit($limit, $offset);

        foreach ($where as $key => $value) {
            $qb->where($key, $value);
        }

        return $qb->get();
    }

    /**
     * @param string $table
     * @param array $values
     * @return int
     * @throws PropertyException
     * @throws QueryException
     */
    public static function insert(string $table, array $values): int
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

        return self::$connect->insert($sql);
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

        return self::$connect->updated($sql);
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

        return self::$connect->updated($sql);
    }
}