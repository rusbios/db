<?php
declare(strict_types = 1);

namespace RB\DB;

use DateTime;
use RB\DB\Builder\QueryBuilder;
use RB\DB\Exceptions\OperatorException;

class DBUtils
{
    public const OPTIONS = ['=', '!=', '<>', '<', '>', '<=', '>='];

    /**
     * @param string $name
     * @return string
     */
    public static function caseCamelToSnake(string $name): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $name, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    /**
     * @param string $name
     * @return string
     */
    public static function caseSnakeToCamel(string $name): string
    {
        $ret = explode('_', $name);
        foreach ($ret as &$str) {
            $str = ucfirst($str);
        }
        return implode('', $ret);
    }

    /**
     * @param string $key
     * @return string
     */
    public static function wrap(string $key): string
    {
        $keys = explode('.', $key);
        foreach ($keys as &$item) {
            $item = '`' . trim($item) . '`';
        }
        return implode('.', $keys);
    }

    /**
     * @param mixed $value
     * @return string
     */
    public static function formatter($value): string
    {
        if (is_int($value)) {
            return (string)$value;
        } elseif (is_float($value)) {
            return "'$value'";
        } elseif (is_null($value)) {
            return 'null';
        } elseif ($value instanceof DateTime) {
            return "'" . $value->format('Y-m-d H:i:s') . "'";
        }

        return trim($value);
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
    public static function filter(string $key, $value = null, string $operator = '='): string
    {
        $operator = trim($operator);

        if (!in_array($operator, self::OPTIONS)) {
            throw new OperatorException('Operator not found');
        }

        if (is_null($value)) {
            $operator = $operator == '=' ? 'is' : 'is not';
        }

        $param = [
            self::wrap($key),
            $operator,
            self::formatter($value),
        ];

        return trim(implode(' ', $param));
    }
}