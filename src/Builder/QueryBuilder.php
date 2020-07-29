<?php
declare(strict_types = 1);

namespace RB\DB\Builder;

use Exception;
use RB\DB\{DBUtils, Model};
use RB\DB\Exceptions\OperatorException;

class QueryBuilder
{
    use WhereTrait;

    private static PDOConnect $connect;

    private string $table;
    private array $columns = [];
    private array $having = [];
    private array $orderBy = [];
    private array $groupBy = [];
    private array $joins = [];
    private array $union = [];
    private int $offset = 0;
    private ?int $limit = null;

    /**
     * QueryBuilder constructor.
     * @param string $table
     *
     * @throws Exception
     */
    protected function __construct(string $table)
    {
        if (!self::$connect) {
            throw new Exception('Connect not found');
        }

        $this->table = $table;
    }

    /**
     * @param PDOConnect $connect
     */
    public static function setConnect(PDOConnect $connect): void
    {
        self::$connect = $connect;
    }

    /**
     * @param string $name
     * @return static
     * @throws Exception
     */
    public static function table(string $name): self
    {
        return new self($name);
    }

    /**
     * @param string $table
     * @param string $firstKey
     * @param string $secondKey
     * @param string $operator
     * @return QueryBuilder
     * @throws OperatorException
     */
    public function join(string $table, string $firstKey, string $secondKey, string $operator = '='): self
    {
        $this->joins[] = 'inner ' . $this->addJoin($table, $firstKey, $secondKey, $operator);
        return $this;
    }

    /**
     * @param string $table
     * @param string $firstKey
     * @param string $secondKey
     * @param string $operator
     * @return QueryBuilder
     * @throws OperatorException
     */
    public function leftJoin(string $table, string $firstKey, string $secondKey, string $operator = '='): self
    {
        $this->joins[] = 'left ' . $this->addJoin($table, $firstKey, $secondKey, $operator);
        return $this;
    }

    /**
     * @param string $table
     * @param string $firstKey
     * @param string $secondKey
     * @param string $operator
     * @return QueryBuilder
     * @throws OperatorException
     */
    public function rightJoin(string $table, string $firstKey, string $secondKey, string $operator = '='): self
    {
        $this->joins[] = 'right ' . $this->addJoin($table, $firstKey, $secondKey, $operator);
        return $this;
    }

    /**
     * @param string $table
     * @param string $firstKey
     * @param string $secondKey
     * @param string $operator
     * @return QueryBuilder
     * @throws OperatorException
     */
    public function fullJoin(string $table, string $firstKey, string $secondKey, string $operator = '='): self
    {
        $this->joins[] = 'full ' . $this->addJoin($table, $firstKey, $secondKey, $operator);
        return $this;
    }

    /**
     * @param array|string $column
     * @return QueryBuilder
     */
    public function column($column): self
    {
        if (is_string($column)) {
            $column = [$column];
        }

        foreach ($column as $item) {
            $this->columns[] = DBUtils::wrap($item);
        }

        return $this;
    }

    /**
     * @param string $sql
     * @return QueryBuilder
     */
    public function columnRaw(string $sql): self
    {
        $this->columns[] = trim($sql);
        return $this;
    }

    /**
     * @param string $column
     * @param bool $asc
     * @return QueryBuilder
     */
    public function orderBy(string $column, bool $asc = true): self
    {
        $this->orderBy[] = implode(' ', [
                DBUtils::wrap($column),
                $asc ? 'asc' : 'desc',
            ]);
        return $this;
    }

    /**
     * @param string|array $column
     * @return $this
     */
    public function groupBy($column): self
    {
        if (is_string($column)) {
            $column = [$column];
        }

        foreach ($column as $item) {
            $this->groupBy[] = DBUtils::wrap($item);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param null $value
     * @param string $operator
     * @return QueryBuilder
     * @throws OperatorException
     */
    public function having(string $key, $value = null, string $operator = '='): self
    {
        $this->having[] = $this->filter($key, $value, $operator, false);
        return $this;
    }

    /**
     * @param string $key
     * @param null $value
     * @param string $operator
     * @return QueryBuilder
     * @throws OperatorException
     */
    public function orHaving(string $key, $value = null, string $operator = '='): self
    {
        $this->having[] = $this->filter($key, $value, $operator, true);
        return $this;
    }

    /**
     * @param string $sql
     * @return QueryBuilder
     */
    public function havingRaw(string $sql): self
    {
        $this->having[] = "and $sql";
        return $this;
    }

    /**
     * @param string $sql
     * @return QueryBuilder
     */
    public function orHavingRaw(string $sql): self
    {
        $this->having[] = "or $sql";
        return $this;
    }

    /**
     * @param int $offset
     * @param int|null $limit
     * @return QueryBuilder
     */
    public function limit(int $limit = null, int $offset = 0): self
    {
        $this->offset = $offset;
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return Model|null
     */
    public function first(): ?Model
    {
        $array = self::$connect->query($this->build());

        return !empty($array[0]) ? new Model($array[0]) : null;
    }

    /**
     * @param string|array $column
     * @return array
     */
    public function pluck($column): array
    {
        if (is_string($column)) {
            $column = [$column];
        }

        $this->column($column);

        $array = self::$connect->query($this->build());

        if (count($column) == 1) {
            foreach ($array as $item) {
                $res[] = $item[$this->columns[0]];
            }

            return $res ?? [];
        }

        return $array;
    }

    /**
     * @param string $column
     * @return int
     */
    public function max(string $column): int
    {
        $this->columns = [];
        $this->columnRaw('max(' . DBUtils::wrap($column) . ') ' . trim($column));

        $array = self::$connect->query($this->build());

        return $array[0][trim($column)] ?? 0;
    }

    /**
     * @param string $column
     * @return int
     */
    public function count(string $column): int
    {
        $this->columns = [];
        $this->columnRaw('count(' . DBUtils::wrap($column) . ') ' . trim($column));

        $array = self::$connect->query($this->build());

        return $array[0][trim($column)] ?? 0;
    }

    /**
     * @param string $column
     * @return int
     */
    public function avg(string $column): int
    {
        $this->columns = [];
        $this->columnRaw('avg(' . DBUtils::wrap($column) . ') ' . trim($column));

        $array = self::$connect->query($this->build());

        return $array[0][trim($column)] ?? 0;
    }

    /**
     * @param $column
     * @return array|null
     */
    public function get($column): ?array
    {
        $this->columns = [];
        $this->column($column);

        return self::$connect->query($this->build());
    }

    /**
     * @return string
     */
    public function getSQL(): string
    {
        return $this->build();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function union(QueryBuilder $queryBuilder): self
    {
        $this->union[] = $queryBuilder;
        return $this;
    }

    /**
     * @return string
     */
    private function build(): string
    {
        $column = count($this->columns) ? implode(', ', $this->columns) : '*';
        $sql = "select $column from " . DBUtils::wrap($this->table);

        if ($this->joins) {
            $sql .= implode(' ', $this->joins);
        }

        if ($this->where) {
            $sql .= ' where ' . implode(' ', $this->where);
        }

        if ($this->union) {
            $sql .= ' union ' . implode(' union ', $this->union);
        }

        if ($this->orderBy) {
            $sql .= ' group by ' . implode(', ', $this->orderBy);
        }

        if ($this->groupBy) {
            $sql .= ' order by ' . implode(', ', $this->groupBy);
        }

        if ($this->having) {
            $sql .= ' having ' . implode(' ', $this->having);
        }

        if ($this->offset) {
            $sql .= " offset $this->offset";
        }

        if ($this->limit) {
            $sql .= " limit $this->limit";
        }

        return $sql . ';';
    }

    /**
     * @param string $table
     * @param string $firstKey
     * @param string $secondKey
     * @param string $operator
     * @return string
     * @throws OperatorException
     */
    private function addJoin(string $table, string $firstKey, string $secondKey, string $operator = '='): string
    {
        $operator = trim($operator);
        if (!in_array($operator, $this->options)) {
            throw new OperatorException('Join operator not found');
        }

        $param = [
            DBUtils::wrap($table),
            'on',
            DBUtils::wrap($firstKey),
            $operator,
            DBUtils::wrap($secondKey),
        ];

        return trim('join ' . implode(' ', $param));
    }
}