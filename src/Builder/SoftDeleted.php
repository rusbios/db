<?php
declare(strict_types = 1);

namespace RB\DB\Builder;

use DateTime;
use RB\DB\Model;

trait SoftDeleted
{
    public static string $deletedTs = 'deleted_ts';

    /**
     * @param array $where
     * @param array $orders
     * @param int|null $limit
     * @return self[]
     * @throws Exceptions\OperatorException
     */
    public static function select(array $where = [], int $offset = 0, int $limit = null): iterable
    {
        $where[static::$deletedTs] = null;

        return parent::select($where, $offset, $limit);
    }

    public function deleted(): void
    {
        $this->date[static::$deletedTs] = new DateTime();
        $this->save();
    }
}