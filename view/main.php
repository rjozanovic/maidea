<?php

namespace maidea\view;

class main extends viewAbstract
{
    public function getOutput() {

        $weathers = new \maidea\model\weathers();
        $forecasts = new \maidea\model\forecasts();
        $favorites = new \maidea\model\favorites();

        $data = array(
            /*'favoriteCities' => array(
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
            ),*/
            'favoriteCities' => $favorites->getJsonWithExtInfo($_COOKIE['maidea_weather']),
            'weather' => $weathers->getJsonWithExtInfo($this->data['cityId']),
            'forecasts' => $forecasts->getJsonWithExtInfo($this->data['cityId'])
        );

        $content = $this->renderTemplate('sidebar', $data['favoriteCities']);
        $content .= '<div class="weather-info">';
        $content .= $this->renderTemplate('weather', $data['weather']);
        $content .= $this->renderTemplate('forecast', $data['forecasts']);
        $content .= '</div>';

        return $this->renderPage($content);


    }
}


