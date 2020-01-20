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

    public function addFavoriteAction()
    {
        $cityId = $this->getRequestParam('cityId');
        $cookie = $_COOKIE['maidea_weather'];
        $favs = new \maidea\model\favorites();
        $info = $favs->getJsonWithExtInfo($cookie);
        foreach($info['data'] as $cityInfo){
            if($cityInfo['id'] === $cityId)
                return;
        }
        $favorite = new \maidea\model\favorite();
        $favorite->setCookie($cookie);
        $favorite->setCityId($cityId);
        $favorite->save();
    }

}
