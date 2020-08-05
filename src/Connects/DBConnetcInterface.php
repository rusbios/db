<?php
declare(strict_types = 1);

namespace RB\DB\Connects;

use RB\DB\Exceptions\QueryException;

interface DBConnetcInterface
{
    /**
     * @param string $sql
     * @return array
     */
    public function query(string $sql): array;

    /**
     * @param string $sql
     * @return int
     * @throws QueryException
     */
    public function insert(string $sql): int;

    /**
     * @param string $sql
     * @return int
     */
    public function updated(string $sql): int;

    /**
     * @param string $string
     * @return string
     */
    public function quote(string $string): string;

    /**
     * @return QueryException
     */
    public function error(): QueryException;
}