<?php

namespace maidea\model;

class city extends modelAbstract
{

    public static function getTableName()
    {
        return 'city';
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
            'name' => \PDO::PARAM_STR,
            'country' => \PDO::PARAM_STR,
        );
    }

}