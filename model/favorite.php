<?php

namespace maidea\model;

class favorite extends modelAbstract
{

    public static function getTableName()
    {
        return 'favorite';
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
            'cookie' => \PDO::PARAM_STR,
        );
    }

}