<?php

namespace maidea\model;

class weathers extends modelsAbstract
{

    public static function getModelName()
    {
        return 'weather';
    }

    /**
     * @param int $cityId
     * @return weather
     */
    public function getLatestWeather($cityId)
    {
        $this->setWhere('city_id = :city_id', array('city_id' => $cityId), array('city_id' => \PDO::PARAM_INT))        //TODO type via model schema
            ->setOrderBy('datetime DESC')
            ->setLimit(1)
            ->load();

        /**
         * @var \maidea\model\weather $freshWeather
         */
        $freshWeather = $this->current();

        $cfg = new \maidea\model\configs();

        $weatherData = json_decode($freshWeather->getJson(), true);
        $lastDownload = $weatherData['dt'];
        if($lastDownload + (int)$cfg->getValue('weather_valid_duration') < time())
            exec("php backgroundTask.php pullWeatherData cityId={$cityId} >/dev/null 2>&1 &");

        return $freshWeather;
    }

}