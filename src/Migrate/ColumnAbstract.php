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
    
    const DEFAULT_CURRENT_TS = 'CURRENT_TIMESTAMP';
    
    protected array $colums;

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
        
        $this->colums[trim($name)] = [
            'name' => DBUtils::wrap($name),
            'type' => trim($type),
            'nullable' => false,
            'default' => null,
            'unsigned' => false,
            'autoIncrement' => false,
            'primaryKey' => false,
        ];

        return $this;
    }

    /**
     * @param string $name
     * @param bool $nullable
     * @param string|int|array|null $default
     * @param bool $unsigned
     * @param bool $autoIncrement
     * @param bool $primaryKey
     * @return $this
     * @throws Exception
     */
    protected function addColumnParam(
        string $name,
        bool $nullable = false,
        $default = null,
        bool $unsigned = false,
        bool $autoIncrement = false,
        bool $primaryKey = false
    ): self
    {
        if (empty($this->colums[trim($name)])) {
            throw new Exception('Column not found');
        }
        
        $this->colums[trim($name)] += [
            'nullable' => $nullable,
            'default' => trim($default),
            'unsigned' => $unsigned,
            'autoIncrement' => $autoIncrement,
            'primaryKey' => $primaryKey,
        ];
        
        return $this;
    }

    /**
     * @param string $name
     * @param int $length
     * @param int|null $scale
     * @return $this
     * @throws Exception
     */
    protected function addColumnOption(string $name, int $length = 100, int $scale = null): self
    {
        if (empty($this->colums[trim($name)])) {
            throw new Exception('Column not found');
        }

        $this->colums[trim($name)] += [
            'length' => $length,
            'scale' => $scale,
        ];

        return $this;
    }
}