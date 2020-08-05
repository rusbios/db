<?php
declare(strict_types = 1);

namespace RB\DB\Migrate;

use Exception;
use RB\DB\DBUtils;
use RB\DB\Exceptions\QueryException;

abstract class ColumnAbstract implements ColumnInterface
{
    const ID = 'id';
    const CREATED_TS = 'created_ts';
    const UPDATED_TS = 'updated_ts';
    const DELETED_TS = 'deleted_ts';

    protected const OPTION_NAME = 'name';
    protected const OPTION_TYPE = 'type';
    protected const OPTION_NULLABLE = 'nullable';
    protected const OPTION_DEFAULT = 'default';
    protected const OPTION_UNIQUE = 'unique';
    protected const OPTION_AUTO_INCREMENT = 'autoIncrement';
    protected const OPTION_PRIMARY_KEY = 'primaryKey';
    protected const OPTION_COMMENT = 'comment';
    protected const OPTION_PARAM = 'param';
    protected const OPTION_VALUES = 'values';
    protected const OPTION_UNSIGNED = 'unsigned';

    private const OPTIONS = [
        self::OPTION_NAME => null,
        self::OPTION_TYPE => null,
        self::OPTION_NULLABLE => false,
        self::OPTION_DEFAULT => null,
        self::OPTION_UNIQUE => false,
        self::OPTION_AUTO_INCREMENT => false,
        self::OPTION_PRIMARY_KEY => false,
        self::OPTION_COMMENT => null,
        self::OPTION_PARAM => null,
        self::OPTION_VALUES => null,
        self::OPTION_UNSIGNED => false,
    ];
    
    const DEFAULT_CURRENT_TS = 'CURRENT_TIMESTAMP';
    
    protected array $colums;
    protected array $indexs;
    protected string $lastColumnName;

    /**
     * @param string $name
     * @param string $type
     * @return $this
     * @throws QueryException
     */
    protected function addColumn(string $name, string $type): self
    {
        if (!$name || !$type || !in_array($type, self::COLUMS)) {
            throw new QueryException('Name or type not found');
        }

        $this->lastColumnName = trim($name);
        
        $this->colums[trim($name)] = [
            self::OPTION_NAME => trim($name),
            self::OPTION_TYPE => trim($type),
        ] + self::OPTIONS;

        return $this;
    }

    /**
     * @param int $total
     * @param int $scale
     * @return $this
     */
    protected function param(int $total = 8, int $scale = 2): self
    {
        $this->colums[$this->lastColumnName][self::OPTION_PARAM] = "$total, $scale";
        return $this;
    }

    /**
     * @param int $length
     * @return $this
     */
    protected function length(int $length = 100): self
    {
        $this->colums[$this->lastColumnName][self::OPTION_PARAM] = $length;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function nullable(): self
    {
        $this->colums[$this->lastColumnName][self::OPTION_NULLABLE] = true;
        return $this;
    }

    /**
     * @param string|int|array|null $value
     * @return $this
     */
    public function default($value): self
    {
        $this->colums[$this->lastColumnName][self::OPTION_DEFAULT] = $value;
        return $this;
    }

    /**
     * @return $this
     */
    public function autoIncrement(): self
    {
        $this->colums[$this->lastColumnName][self::OPTION_AUTO_INCREMENT] = true;
        return $this;
    }

    public function unsigned(): self
    {
        $this->colums[$this->lastColumnName][self::OPTION_AUTO_INCREMENT] = true;
        return $this;
    }

    /**
     * @param string $comment
     * @return $this
     */
    public function comment(string $comment): self
    {
        $this->colums[$this->lastColumnName][self::OPTION_COMMENT] = trim($comment);
        return $this;
    }

    /**
     * @return $this
     */
    public function primary(): self
    {
        $this->colums[$this->lastColumnName][self::OPTION_PRIMARY_KEY] = true;
        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    protected function values(array $values): self
    {
        $this->colums[$this->lastColumnName][self::OPTION_VALUES] = implode(', ', array_map(fn($item) => "'$item'", $values));
        return $this;
    }

    /**
     * @return $this
     */
    public function index(): self
    {
        $this->indexs[$this->lastColumnName] = trim($this->lastColumnName);
        return $this;
    }

    /**
     * @param string|array $column
     * @param string|null $name
     * @return $this
     */
    public function addIndex($column, string $name = null): self
    {
        if (!$name) {
            $name = implode('_', $column);
        }
        $this->indexs[$name] = implode(',', $column);
        return $this;
    }
}