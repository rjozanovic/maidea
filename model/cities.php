<?php

namespace maidea\model;

class cities extends modelsAbstract
{

    public static function getModelName()
    {
        return 'city';
    }

    public function getCityById($cityId)
    {
        $this->setWhere('city_id = :city_id', array('city_id' => $cityId), array('city_id' => \PDO::PARAM_INT))
            ->setLimit(1)
            ->load();
        return $this->current();
    }

}