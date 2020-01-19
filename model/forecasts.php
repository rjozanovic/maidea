<?php

namespace maidea\model;

class forecasts extends modelsAbstract
{

    public static function getModelName()
    {
        return 'forecast';
    }

    public function loadLatestForecasts($cityId)
    {
        $this->setWhere('city_id = :city_id AND datetime > "' . date('Y-m-d H:i:s') . '"', array('city_id' => $cityId), array('city_id' => \PDO::PARAM_INT))
            ->setOrderBy('datetime ASC')
            ->setLimit(40)
            ->load();

        if($this->count() < 40)       //pull new data
            exec("php backgroundTask.php pullForecastData cityId={$cityId} >/dev/null 2>&1 &");

    }

}