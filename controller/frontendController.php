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
        $this->setJsonHeader();
        echo $freshWeather->getJson();
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
        $this->setJsonHeader();
        echo json_encode($ret);

    }

    public function getCityAutocompleteAction()
    {
        $q = $this->getRequestParam('q');
        $cities = new \maidea\model\cities();
        $this->setJsonHeader();
        echo json_encode($cities->getAutocompleteCities($q));
    }




}
