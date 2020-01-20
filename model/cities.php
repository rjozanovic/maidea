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

    public function getAutocompleteCities($q, $limit = 10)
    {
        $this->setWhere('name LIKE :name', array('name' =>  $q . '%'), array('name' => \PDO::PARAM_STR))
            ->setLimit($limit)
            ->load();

        $ret = array();
        foreach($this as $city){
            $ret[] = array('id' => $city->getCityId(), 'title' => $city->getName());
        }
        return $ret;

    }

}