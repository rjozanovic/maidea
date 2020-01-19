<?php

namespace maidea\model;

class weather extends modelAbstract
{

    public static function getTableName()
    {
        return 'weather';
    }

    public static function getPkName()
    {
        return 'id';
    }

    public static function getSchema()
    {
        return array(
            'id' => \PDO::PARAM_INT,
            'city_id' => \PDO::PARAM_INT,
            'datetime' => \PDO::PARAM_STR,
            'json' => \PDO::PARAM_STR,
        );
    }

}