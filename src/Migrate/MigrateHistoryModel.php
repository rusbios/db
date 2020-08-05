<?php
declare(strict_types = 1);

namespace RB\DB\Migrate;

use RB\DB\Builder\QueryBuilder;
use RB\DB\Model;

/**
 * Class MigrateHistoryModel
 * @package RB\DB\Migrate
 *
 * @property int $id
 * @property string $class_name
 * @property DateTime $created_ts
 * @property DateTime $updated_ts
 */
class MigrateHistoryModel extends Model
{
    protected string $table = 'migrate_history';

    /**
     * @param string $class
     * @return Model|null
     */
    public static function getByModel(string $class): ?Model
    {
        return QueryBuilder::table(self::getTable())
            ->where('class_name' , $class)
            ->first();
    }
}