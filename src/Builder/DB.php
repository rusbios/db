<?php
declare(strict_types = 1);

namespace RB\DB\Builder;

use RB\DB\Exceptions\{OperatorException, PropertyException};
use RB\DB\DBUtils;

class DB
{
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
     * @param array $orders
     * @param int $offset
     * @param int|null $limit
     * @return array
     * @throws OperatorException
     */
    public static function select(
        string $table,
        array $columns = [],
        array $where = [],
        array $orders = [],
        int $offset = 0,
        int $limit = null
    ): array
    {
        foreach ($columns as &$column) {
            $column = DBUtils::wrap($column);
        }
        $columns = $columns ? implode(', ', $columns) : '*';

        $sql = "select $columns from " . DBUtils::wrap($table);

        foreach ($where as $key => $value) {
            $wheres[] = self::where($key, $value);
        }
        if (isset($wheres)) {
            $sql .= ' where ' . implode(' and ', $wheres);
        }

        foreach ($orders as $key => $order) {
            if (is_int($key)) {
                $key = $order;
                $order = '';
            }
            $orderBy[] = DBUtils::wrap($key) . ' ' . trim($order) ?? 'asc';
        }
        if (isset($orderBy)) {
            $sql .= ' order by ' . implode(', ', $orderBy);
        }

        if ($offset > 0) {
            $sql .= " offset $offset";
        }

        if ($limit) {
            $sql .= " limit $limit";
        }

        return self::$connect->query($sql);
    }

    /**
     * @param string $table
     * @param array $values
     * @return int
     * @throws PropertyException
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
            $wheres[] = self::where($key, $value);
        }
        if (isset($wheres)) {
            $sql .= ' where ' . implode(' and ', $wheres);
        }

        return self::$connect->updated($sql);
    }

    /**
     * @param string $key
     * @param string|bool|int|float|null $value
     * @param string $operator
     * @return string
     * @throws OperatorException
     */
    private static function where(string $key, $value = null, string $operator = '='): string
    {
        $operator = trim($operator);

        if (!in_array($operator, ['=', '!=', '<>', '<', '>', '<=', '>='])) {
            throw new OperatorException('Operator not found');
        }

        if (is_null($value)) {
            $operator = $operator == '=' ? 'is' : 'is not';
        }

        return DBUtils::wrap($key) . " $operator " . DBUtils::formatter($value);
    }
}