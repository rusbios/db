<?php
declare(strict_types = 1);

namespace RB\DB\Migrate;

use RB\DB\DBUtils;

class Table extends ColumnAbstract
{
    private string $tableName;
    private string $lastAddColumnName;
    
    public function setTable(string $name): self
    {
        $this->tableName = $name;
        return $this;
    }
    
    /** Numbers */

    public function id(): self
    {
        return $this->bigIncrements(self::ID);
    }

    public function bigInteger(string $columnName): self
    {
        return $this->addColumn($columnName, self::COLUMN_BIG_INT);
    }

    public function bigIncrements(string $columnName = self::ID): self
    {
        return $this->bigInteger($columnName)
            ->addColumnParam($columnName, false, null, true, true, true);
    }

    public function integer(string $columnName): self
    {
        return $this->addColumn($columnName, self::COLUMN_INT);
    }

    public function increments(string $columnName = self::ID): self
    {
        return $this->integer($columnName)
            ->addColumnParam($columnName, false, null, true, true, true);
    }

    public function smallInteger(string $columnName): self
    {
        return $this->addColumn($columnName, self::COLUMMN_SMAL_INT);
    }

    public function tinyInteger(string $columnName): self
    {
        return $this->addColumn($columnName, self::COLUMN_TINY_INT);
    }

    public function float(string $columnName, int $total = 8, int $scale = 2): self
    {
        return $this->addColumn($columnName, self::COLUMN_FLOAT)
            ->addColumnOption($columnName, $total, $scale);
    }
    
    public function bit(string $columnName, int $length = 1): self
    {
        return $this->addColumn($columnName, self::COLUMN_BIT)
            ->addColumnOption($length);
    }

    public function bool(string $columnName): self
    {
        return $this->addColumn($columnName, self::COLUMN_BOOL);
    }

    public function money(string $columnName): self
    {
        return $this->addColumn($columnName, self::COLUMN_MONEY);
    }

    /** strings */
    
    public function binary(string $columnName): self
    {
        return $this->addColumn($columnName, self::COLUMN_BINARY);
    }

    public function ipAddress(string $columnName): self
    {
        return $this->binary($columnName);
    }

    public function enum(string $columnName, array $values): self
    {
        return $this->addColumn($columnName, self::COLUMN_ENUM)
            ->addColumnParam($columnName, true, $values);
    }

    public function set(string $columnName, array $values): self
    {
        return $this->addColumn($columnName, self::COLUMN_SET)
            ->addColumnParam($columnName, true, $values);
    }

    public function string(string $columnName, int $length = 100): self
    {
        return $this->addColumn($columnName, self::COLUMN_VARCHAR)
            ->addColumnOption($columnName, $length);
    }

    public function text(string $columnName): self
    {
        return $this->addColumn($columnName, self::COLUMN_TEXT);
    }

    public function char(string $columnName, int $length = 100): self
    {
        return $this->addColumn($columnName, self::COLUMN_CHAR)
            ->addColumnOption($columnName, $length);
    }
    
    public function tinyText(string $columnName): self
    {
        return $this->addColumn($columnName, self::COLUMN_TINY_TEXT);
    }

    public function longText(string $columnName): self
    {
        return $this->addColumn($columnName, self::COLUMN_LONG_TEXT);
    }

    public function mediumText(string $columnName): self
    {
        return $this->addColumn($columnName, self::COLUMN_MEDIUM_TEXT);
    }
    
    /** DATETIME */

    public function date(string $columnName): self
    {
        return $this->addColumn($columnName, self::COLUMN_DATE);
    }

    public function dateTime(string $columnName, bool $currentTs = false): self
    {
        $this->addColumn($columnName, self::COLUMN_DATETIME);
        
        if ($currentTs) {
            $this->addColumnParam($columnName, null, self::DEFAULT_CURRENT_TS);
        }
        
        return $this;
    }

    public function timestamp(string $columnName, bool $currentTs = false): self
    {
        $this->addColumn($columnName, self::COLUMN_TIMESTAMP);

        if ($currentTs) {
            $this->addColumnParam($columnName, null, self::DEFAULT_CURRENT_TS);
        }

        return $this;
    }

    public function timestamps(): self
    {
        return $this
            ->timestamp(self::CREATED_TS, true)
            ->timestamp(self::UPDATED_TS, true);
    }

    public function softDeletes(string $columnName = self::DELETED_TS): self
    {
        return $this->timestamp($columnName, false);
    }
    
    public function year(string $columnName): self
    {
        return $this->addColumn($columnName, self::COLUMN_YEAR);
    }
}