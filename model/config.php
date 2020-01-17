<?php

namespace maidea\model;

class config extends modelAbstract
{

    protected function getTable()
    {
        return 'patient';
    }

    protected function getPkey()
    {
        return 'patient_id';
    }

    protected function getFieldBindings()
    {
        return array(
            'patient_id' => self::T_SQL_INT,
            'name' => self::T_SQL_INT,
            'surname' => self::T_SQL_VARCHAR,
            'date_of_birth' => self::T_SQL_VARCHAR,
            'telephone' => self::T_SQL_VARCHAR,
            'address' => self::T_SQL_VARCHAR,
        );
    }

}