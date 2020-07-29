<?php
declare(strict_types = 1);

namespace RB\DB;

use DateTime;
use RB\DB\Builder\DB;

class Model
{
    public const CREATED_TS = 'created_ts';
    public const UPDATED_TS = 'updated_ts';

    protected string $table;
    public string $primaryKey = 'id';
    public bool $timestamps = true;

    private array $oldData;
    private array $data;

    /**
     * Model constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
        if (!empty($this->data[$this->primaryKey])) {
            unset($this->data[$this->primaryKey]);
        }
        $this->oldData = $data;
    }

    /**
     * @return self[]
     */
    public static function all(): iterable
    {
        return static::select();
    }

    /**
     * @param mixed $id
     * @return self|null
     * @throws Exceptions\OperatorException
     */
    public static function find($id): self
    {
        $model = new static();
        $collect = static::select([
            $model->primaryKey => $id
        ]);

        return array_shift($collect);
    }

    /**
     * @param array $where
     * @param array $orders
     * @param int|null $limit
     * @return self[]
     * @throws Exceptions\OperatorException
     */
    public static function select(array $where = [], int $offset = 0, int $limit = null): iterable
    {
        $models = [];

        foreach (DB::select(static::getTable(), [], $where, $offset, $limit) as $data) {
            $models[] = new static($data);
        }

        return $models;
    }

    /**
     * @param array $values
     * @return int
     * @throws Exceptions\PropertyException
     */
    public static function insert(array $values): int
    {
        return DB::insert(static::getTable(), $values);
    }

    /**
     * @param array $values
     * @param array $where
     * @return int
     * @throws Exceptions\OperatorException
     * @throws Exceptions\PropertyException
     */
    public static function update(array $values, array $where = []): int
    {
        return DB::update(static::getTable(), $values, $where);
    }

    /**
     * @return string
     */
    public static function getTable(): string
    {
        $class = explode('\\',static::class);
        $table = end($class);
        if (substr($table, -5) == 'Model') {
            $table = substr($table, 0, -5);
        }
        return (new static())->table ?? DBUtils::caseCamelToSnake($table);
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name)
    {
        if ($name == $this->primaryKey) {
            return $this->getId();
        }

        $value = $this->data[$name] ?? null;
        $function = 'get'.DBUtils::caseSnakeToCamel($name).'Attribute';
        if (function_exists($function)) {
            return $this->$function($value);
        }
        return $value;
    }

    /**
     * @param string $name
     * @param mixed|null $value
     * @return self
     */
    public function __set(string $name, $value = null): self
    {
        if ($name == $this->primaryKey) {
            $this->setId((int)$value);
            return $this;
        }

        $function = 'set'.DBUtils::caseSnakeToCamel($name).'Attribute';
        if (function_exists($function)) {
            $this->$function($value);
        } else {
            $this->data[$name] = $value;
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->oldData[$this->primaryKey] ?? null;
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->oldData[$this->primaryKey] = $id;
        return $this;
    }

    /**
     * @param array $attributes
     * @return self
     * @throws Exceptions\OperatorException
     * @throws Exceptions\PropertyException
     */
    public function save(array $attributes = []): self
    {
        $attributes += $this->data;

        foreach ($attributes as $key => $value) {
            if (empty($this->oldData[$key]) || $value != $this->oldData[$key]) {
                $diff[$key] = $value;
            }
        }

        if (isset($diff)) {

            if ($this->timestamps) {
                $this->data[static::UPDATED_TS] = $diff[static::UPDATED_TS] = new DateTime();
            }

            if (empty($this->oldData[$this->primaryKey])) {
                if ($this->timestamps) {
                    $this->data[static::CREATED_TS] = $diff[static::CREATED_TS] = $this->data[static::UPDATED_TS];
                }
                $this->oldData[$this->primaryKey] = DB::insert(static::getTable(), $diff);
            } else {
                DB::update(static::getTable(), $diff, [$this->primaryKey => $this->oldData[$this->primaryKey]]);
            }
        }

        return $this;
    }

    /**
     * @throws Exceptions\OperatorException
     */
    public function deleted(): void
    {
        if ($this->primaryKey) {
            DB::deleted(static::getTable(), [$this->primaryKey => (int)$this->oldData[$this->primaryKey]]);
        }
    }
}