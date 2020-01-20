<?php

//TODO disable outside acess to backend controller (allow only from backgroundTask.php)

namespace maidea\controller;

class backendController extends controllerAbstract
{

    public function pullCityDataAction()
    {

        set_time_limit(100);
        ini_set('memory_limit', '256M');

        //download city list
        $url = "http://bulk.openweathermap.org/sample/city.list.min.json.gz";
        $tmp = "/tmp/city.list.min.json.gz";
        $fp = fopen($tmp, "w");
        $ch = \curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $page = curl_exec($ch);
        if(!$page) {
            echo "Error :- ".curl_error($ch);
        }
        curl_close($ch);

        //unzip
        $bufferSize = 4096;
        $outFileName = str_replace('.gz', '', $tmp);
        $file = gzopen($tmp, 'rb');
        $outFile = fopen($outFileName, 'wb');
        while (!gzeof($file))
            fwrite($outFile, gzread($file, $bufferSize));
        fclose($outFile);
        gzclose($file);

        //save to db
        $citysData = json_decode(file_get_contents($outFileName), true);
        $values = array();
        $batchSize = 1000;
        $counter = 0;
        $pdo = \maidea\db::getPdoHandle();
        foreach($citysData as $cityData){
            $cityData = array($cityData['id'], '"' . $cityData['name'] . '"', '"' . $cityData['country'] . '"');
            $values[] = '(' . implode(',', $cityData) . ')';
            $counter++;
            if($counter >= $batchSize){
                $sql = 'INSERT INTO city (city_id, name, country) VALUES ' . implode(',', $values) . ';';
                $pdo->exec($sql);
                $values = array();
                $counter = 0;
            }
        }
    }

    public function pullWeatherDataAction()
    {
        $cityId = $this->getRequestParam('cityId');

        $appId = \maidea\config::getConfig()['openWeather']['appId'];
        $url = "http://api.openweathermap.org/data/2.5/weather?id={$cityId}&APPID={$appId}";

        $cities = new \maidea\model\cities();
        $city = $cities->getCityById($cityId);
        if($city->getWeatherDownloadInProgress())      //prevent simultanious downloads
            return;

        $city->setWeatherDownloadInProgress(true);

        $json = \maidea\helpers::fetchFile($url);
        $weather = new \maidea\model\weather();

        $weather->setCityId($cityId);
        $weather->setDatetime(date('Y-m-d H:i:s'));     //TODO read from json
        $weather->setJson($json);
        $weather->save();

        $city->setWeatherDownloadInProgress(false);

    }

    public function pullForecastDataAction()
    {
        $cityId = $this->getRequestParam('cityId');
        $appId = \maidea\config::getConfig()['openWeather']['appId'];
        $url = "http://api.openweathermap.org/data/2.5/forecast?id={$cityId}&APPID={$appId}";

        $cities = new \maidea\model\cities();
        $city = $cities->getCityById($cityId);
        if($city->getForecastDownloadInProgress())      //prevent simultanious downloads
            return;

        $city->setForecastDownloadInProgress(true);

        $json = \maidea\helpers::fetchFile($url);

        $data = json_decode($json, true);

        foreach($data['list'] as $forecastItem){

            //find row where city and datetime match this ones. if so, update, don't insert a new row
            $forecasts = new \maidea\model\forecasts();
            $forecasts->setWhere('city_id = :city_id AND datetime = :datetime',
                array('city_id' => $cityId, 'datetime' => date('Y-m-d H:i:s', $forecastItem['dt'])),
                array('city_id' => \PDO::PARAM_INT, 'datetime' => \PDO::PARAM_STR))
                ->setLimit(1)->load();

            if($forecasts->count())
                $forecast = $forecasts->current();
            else
                $forecast = new \maidea\model\forecast();

            $forecast->setCityId($cityId);
            $forecast->setDatetime(date('Y-m-d H:i:s', $forecastItem['dt']));     //TODO read from json
            $forecast->setJson(json_encode($forecastItem));
            $forecast->save();
        }

        $city->setForecastDownloadInProgress(false);

    }


}