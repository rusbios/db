<?php
declare(strict_types = 1);

namespace RB\DB\Migrate;

interface ColumnInterface
{
    const COLUMN_INT = 'int';
    const COLUMN_BIG_INT = 'bigint';
    const COLUMMN_SMAL_INT = 'smalint';
    const COLUMN_TINY_INT = 'tinyint';
    const COLUMN_FLOAT = 'decimal';
    const COLUMN_BIT = 'bit';
    const COLUMN_BOOL = 'bool';
    const COLUMN_MONEY = 'money';

    const COLUMN_BINARY = 'binary';
    const COLUMN_CHAR = 'car';
    const COLUMN_VARCHAR = 'varchar';
    const COLUMN_BLOB = 'blob';
    const COLUMN_TINY_BLOB = 'tinyblob';
    const COLUMN_MEDIUM_BLOB = 'mediumblob';
    const COLUMN_LONG_BLOB = 'longblob';
    const COLUMN_TEXT = 'text';
    const COLUMN_TINY_TEXT = 'tinytext';
    const COLUMN_MEDIUM_TEXT = 'mediumtext';
    const COLUMN_LONG_TEXT = 'longtext';
    const COLUMN_ENUM = 'enum';
    const COLUMN_SET = 'set';

    const COLUMN_DATE = 'date';
    const COLUMN_DATETIME = 'datetime';
    const COLUMN_TIMESTAMP = 'timestamp';
    const COLUMN_TIME = 'time';
    const COLUMN_YEAR = 'year';

    const COLUMS = [
        self::COLUMN_INT,
        self::COLUMN_BIG_INT,
        self::COLUMMN_SMOL_INT,
        self::COLUMN_TINY_INT,
        self::COLUMN_FLOAT,
        self::COLUMN_BIT,
        self::COLUMN_BOOL,
        self::COLUMN_MONEY,

        self::COLUMN_BINARY,
        self::COLUMN_CHAR,
        self::COLUMN_VARCHAR,
        self::COLUMN_BLOB,
        self::COLUMN_TINY_BLOB,
        self::COLUMN_MEDIUM_BLOB,
        self::COLUMN_LONG_BLOB,
        self::COLUMN_TEXT,
        self::COLUMN_TINY_TEXT,
        self::COLUMN_MEDIUM_TEXT,
        self::COLUMN_LONG_TEXT,
        self::COLUMN_ENUM,
        self::COLUMN_SET,
        
        self::COLUMN_DATE,
        self::COLUMN_DATETIME,
        self::COLUMN_TIMESTAMP,
        self::COLUMN_TIME,
        self::COLUMN_YEAR,
    ];
}