<?php

namespace maidea\model;

class config extends modelAbstract
{

    public static function getTableName()
    {
        return 'config';
    }

    public static function getPkName()
    {
        return 'id';
    }

    public static function getSchema()
    {
        return array(
            'key' => PDO::PARAM_STR,
            'value' => PDO::PARAM_STR,
        );
    }

}