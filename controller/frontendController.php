<?php

namespace maidea\controller;

class frontendController extends controllerAbstract
{
    public function mainAction()
    {
        $cityId = $this->getRequestParam('cityId');
        echo $this->getView('main')->setData(array('cityId' => $cityId))->getOutput();
    }

    //ajax
    public function getWeatherDataAction()
    {
        $cityId = $this->getRequestParam('cityId');
        $weathers = new \maidea\model\weathers();
        $freshWeather = $weathers->getLatestWeather($cityId);
        var_dump($freshWeather->getJson());
        //TODO upToDate
    }

    //ajax
    public function getForecastDataAction()
    {
        $cityId = $this->getRequestParam('cityId');

        $forecasts = new \maidea\model\forecasts();
        $forecasts->loadLatestForecasts($cityId);

        $ret = array('data' => array(), 'complete' => $forecasts->count() === 40);
        foreach($forecasts as $forecast){
            $ret['data'][] = $forecast->getJson();
        }

        var_dump($ret);

    }

}
