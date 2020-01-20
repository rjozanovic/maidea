<?php

namespace maidea\view;

class main extends viewAbstract
{
    public function getOutput() {

        $weathers = new \maidea\model\weathers();
        $latestWeather = $weathers->getLatestWeather($this->data['cityId']);
        $weatherData = json_decode($latestWeather->getJson(), true);

        $forecasts = new \maidea\model\forecasts();
        $forecasts->loadLatestForecasts($this->data['cityId']);
        $forecastData = array();
        foreach ($forecasts as $forecast)
            $forecastData[] = json_decode($forecast->getJson(), true);

        $cfg = new \maidea\model\configs();

        $data = array(
            'favoriteCities' => array(
                array(
                    "id" => 3186886,
                    "name" => "Zagreb"
                ),
                 array(
                     "id" => 3193935,
                     "name" => "Osijek"
                 ),
                array(
                    "id" => 3201047,
                    "name" => "Dubrovnik"
                ),
                array(
                    "id" => 5506956,
                    "name" => "Las Vegas"
                )
            ),
            'weather' => array(
                'data' => $weatherData,
                'upToDate' => $weatherData['dt'] + $cfg->getValue('weather_valid_duration') > time()
            ),
            'forecasts' => array(
                'data' => $forecastData,
                'isComplete' => count($forecastData) === 40
            )
        );

        $content = $this->includePartialTemplate('sidebar');
        $content .= $this->includePartialTemplate('weather');
        $content .= $this->includePartialTemplate('forecast');
        $content .= $this->renderTemplate('main', $data);

        return $this->renderPage($content);


    }
}


