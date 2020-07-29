<?php
declare(strict_types = 1);

namespace RB\DB\Builder;

use DateTime;
use RB\DB\DBUtils;
use RB\DB\Exceptions\OperatorException;

trait WhereTrait
{
    private array $where = [];
    private array $orWhere = [];

    /**
     * @param string $key
     * @param mixed $value
     * @param string $operator
     * @return QueryBuilder
     * @throws OperatorException
     */
    public function where(string $key, $value = null, string $operator = '='): self
    {
        $this->where[] = $this->filter($key, $value, $operator);
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param string $operator
     * @return $this
     * @throws OperatorException
     */
    public function orWhere(string $key, $value = null, string $operator = '='): self
    {
        $this->orWhere[] = $this->filter($key, $value, $operator);
        return $this;
    }

    /**
     * @param string $sql
     * @return QueryBuilder
     */
    public function whereRaw(string $sql): self
    {
        $this->where[] = $sql;
        return $this;
    }

    /**
     * @param string $sql
     * @return QueryBuilder
     */
    public function orWhereRaw(string $sql): self
    {
        $this->orWhere[] = $sql;
        return $this;
    }

    /**
     * @param string $column
     * @param array $params
     * @return QueryBuilder
     */
    public function whereIn(string $column, array $params): self
    {
        $this->where[] = $this->in($column, $params, false);
        return $this;
    }

    /**
     * @param string $column
     * @param array $params
     * @return QueryBuilder
     */
    public function orWhereIn(string $column, array $params): self
    {
        $this->orWhere[] = $this->in($column, $params, false);
        return $this;
    }

    /**
     * @param string $column
     * @param array $params
     * @return QueryBuilder
     */
    public function whereNotIn(string $column, array $params): self
    {
        $this->where[] = $this->in($column, $params, true);
        return $this;
    }

    /**
     * @param string $column
     * @param array $params
     * @return QueryBuilder
     */
    public function orWhereNotIn(string $column, array $params): self
    {
        $this->orWhere[] = $this->in($column, $params, true);
        return $this;
    }

    /**
     * @param string $column
     * @param DateTime|int|string $start
     * @param DateTime|int|string $end
     * @return QueryBuilder
     */
    public function whereBetween(string $column, $start, $end): self
    {
        $this->where[] = $this->between($column, $start, $end, false);
        return $this;
    }

    /**
     * @param string $column
     * @param DateTime|int|string $start
     * @param DateTime|int|string $end
     * @return QueryBuilder
     */
    public function orWhereBetween(string $column, $start, $end): self
    {
        $this->orWhere[] = $this->between($column, $start, $end, false);
        return $this;
    }

    /**
     * @param string $column
     * @param DateTime|int|string $start
     * @param DateTime|int|string $end
     * @return QueryBuilder
     */
    public function whereNotBetween(string $column, $start, $end): self
    {
        $this->where[] = $this->between($column, $start, $end, true);
        return $this;
    }

    /**
     * @param string $column
     * @param DateTime|int|string $start
     * @param DateTime|int|string $end
     * @return QueryBuilder
     */
    public function orWhereNotBetween(string $column, $start, $end): self
    {
        $this->orWhere[] = $this->between($column, $start, $end, true);
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param string $operator
     *
     * @return QueryBuilder
     *
     * @throws OperatorException
     */
    public function filter(string $key, $value = null, string $operator = '='): string
    {
        return DBUtils::filter($key, $value, $operator);
    }

    /**
     * @param string $column
     * @param array $params
     * @param bool $not
     * @return string
     */
    private function in(string $column, array $params = [], bool $not = false): string
    {
        foreach ($params as &$param) {
            $param = DBUtils::formatter($param);
        }

        return implode(' ', [
            DBUtils::wrap($column),
            $not ? 'not in' : 'in',
            '(' . implode(', ', $params) . ')',
        ]);
    }

    /**
     * @param string $column
     * @param DateTime|int|string $begin
     * @param DateTime|int|string $end
     * @param bool $not
     * @return string
     */
    private function between(string $column, $begin, $end, bool $not = false): string
    {
        return implode(' ', [
            DBUtils::wrap($column),
            $not ? 'not between' : 'between',
            DBUtils::formatter($begin),
            'and',
            DBUtils::formatter($end),
        ]);
    }
}