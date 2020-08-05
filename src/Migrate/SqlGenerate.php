<?php
declare(strict_types = 1);

namespace RB\DB\Migrate;

use RB\DB\DBUtils;

trait SqlGenerate
{
    public function sql(): string
    {
        $row = $this->updated
            ? $this->update()
            : $this->create();
        return $row . ' ' . $this->genIndex();
    }

    private function create(): string
    {
        $columns = [];

        foreach ($this->colums as $colum) {
            $tmp = DBUtils::wrap($colum[self::OPTION_NAME]) . ' ' . $colum[self::OPTION_TYPE];

            if ($colum[self::OPTION_PARAM]) {
                $tmp .= '(' . $colum[self::OPTION_PARAM] . ')';
            } elseif ($colum[self::OPTION_VALUES]) {
                $tmp .= '(' . $colum[self::OPTION_VALUES] . ')';
            }

            if ($colum[self::OPTION_UNSIGNED]) {
                $tmp .= ' unsigned';
            }

            if ($colum[self::OPTION_AUTO_INCREMENT]) {
                $tmp .= ' auto_increment';
            }

            if ($colum[self::OPTION_PRIMARY_KEY]) {
                $tmp .= ' primary key';
            } else {
                if ($colum[self::OPTION_DEFAULT]) {
                    $tmp .= ' default ' . $colum[self::OPTION_DEFAULT];
                }
                $tmp .= $colum[self::OPTION_NULLABLE] ? ' null' : ' not null';
            }

            $columns[] = $tmp;
        }

        return sprintf(
            'create table %s (%s) collate = utf8mb4_unicode_ci;',
            DBUtils::wrap($this->tableName),
            implode(', ', $columns)
        );
    }

    private function update(): string
    {
        $rows = [];

        foreach ($this->colums as $colum) {
            $sql = sprintf(
                'alter table %s %s %s',
                DBUtils::wrap($this->tableName),
                DBUtils::wrap($colum[self::OPTION_NAME]),
                $colum[self::OPTION_TYPE]
            );

            if ($colum[self::OPTION_PARAM]) {
                $tmp .= '(' . $colum[self::OPTION_PARAM] . ')';
            } elseif ($colum[self::OPTION_VALUES]) {
                $tmp .= '(' . $colum[self::OPTION_VALUES] . ')';
            }

            if ($colum[self::OPTION_UNSIGNED]) {
                $sql .= ' unsigned';
            }

            if ($colum[self::OPTION_AUTO_INCREMENT]) {
                $sql .= ' auto_increment';
            }

            if ($colum[self::OPTION_PRIMARY_KEY]) {
                $sql .= ' primary key';
            } else {
                if ($colum[self::OPTION_DEFAULT]) {
                    $sql .= ' default ' . $colum[self::OPTION_DEFAULT];
                }
                $sql .= $colum[self::OPTION_NULLABLE] ? ' null' : ' not null';
            }

            $rows[] = $sql.';';
        }

        return implode(' ', $rows);
    }

    protected function genIndex(): string
    {
        $rows = [];

        foreach ($this->indexs ?? [] as $name => $keys) {
            if (is_array($keys)) {
                foreach ($keys as &$key) {
                    $key = DBUtils::wrap($key);
                }
                $keys = implode(', ', $keys);
            } else {
                $keys = DBUtils::wrap($keys);
            }

            $rows[] = sprintf(
                'alter table %s add index %s (%s);',
                DBUtils::wrap($this->tableName),
                DBUtils::wrap($name),
                $keys
            );
        }

        return implode(' ', $rows);
    }
}