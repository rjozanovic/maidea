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
        return 'name';
    }

    public static function getSchema()
    {
        return array(
            'name' => \PDO::PARAM_STR,
            'value' => \PDO::PARAM_STR,
        );
    }

}