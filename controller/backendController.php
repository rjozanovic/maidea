<?php

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

        $json = \maidea\helpers::fetchFile($url);

        $weather = new \maidea\model\weather();

        $weather->setCityId($cityId);
        $weather->setDatetime(date('Y-m-d H:i:s'));     //TODO read from json
        $weather->setJson($json);
        $weather->save();

    }

    public function pullForecastDataAction()
    {
        $cityId = $this->getRequestParam('cityId');
        $appId = \maidea\config::getConfig()['openWeather']['appId'];


    }


}