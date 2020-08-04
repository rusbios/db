<?php
declare(strict_types = 1);

namespace RB\DB\Migrate;

trait TableIndexTrait
{
    public function after(string $columnName): self
    {
        return $this;
    }

    public function autoIncrement(): self
    {
        return $this;
    }

    public function charset(string $specify): self
    {
        return $this;
    }

    public function comment(string $comment): self
    {
        return $this;
    }

    public function default($value): self
    {
        return $this;
    }

    public function nullable(): self
    {
        return $this;
    }

    public function unsigned(): self
    {
        return $this;
    }

    /**
     * @param string|array $columnNames
     * @return $this
     */
    public function primary($columnNames): self
    {
        return $this;
    }

    public function unique(string $columnName): self
    {
        return $this;
    }

    public function index(string $columnName): self
    {
        return $this;
    }
}