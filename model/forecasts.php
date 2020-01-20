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
    }

    public function getJsonWithExtInfo($cityId)
    {
        $ret = array("reload" => array("dataUrl" => "index.php?action=getForecastData&cityId=" . $cityId));
        $this->loadLatestForecasts($cityId);
        $forecastData = array();
        foreach ($this as $forecast)
            $forecastData[] = json_decode($forecast->getJson(), true);

        if($this->count() < 40){       //pull new data
            exec("php backgroundTask.php pullForecastData cityId={$cityId} >/dev/null 2>&1 &");
            $ret['reload']["inTime"] = 5;
        }

        $ret['data'] = $forecastData;
        return $ret;

    }

}