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
        $forecasts = new \maidea\model\weathers();
        $this->setJsonHeader();
        echo json_encode($forecasts->getJsonWithExtInfo($cityId));
    }

    //ajax
    public function getForecastDataAction()
    {
        $cityId = $this->getRequestParam('cityId');
        $forecasts = new \maidea\model\forecasts();
        $this->setJsonHeader();
        echo json_encode($forecasts->getJsonWithExtInfo($cityId));
    }

    public function getCityAutocompleteAction()
    {
        $q = $this->getRequestParam('q');
        $cities = new \maidea\model\cities();
        $this->setJsonHeader();
        echo json_encode($cities->getAutocompleteCities($q));
    }

}
