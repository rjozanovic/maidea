<?php

namespace maidea\model;

class favorites extends modelsAbstract
{

    public static function getModelName()
    {
        return 'favorite';
    }

    public function loadFavorites($cookie)
    {
        $this->setWhere('cookie = :cookie', array('cookie' => $cookie), array('cookie' => \PDO::PARAM_STR))
            ->load();
    }

    public function getJsonWithExtInfo($cookie)
    {
        $ret = array("reload" => array("dataUrl" => "index.php?action=getFavoritesData"));
        $this->loadFavorites($cookie);
        $favoriteData = array(
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
        );
        foreach ($this as $favorite) {
            $cities = new cities();
            $city = $cities->getCityById($favorite->getCityId());
            $favoriteData[] = array("id" => $city->getCityId(), "name" => $city->getName());
        }
        $ret['data'] = $favoriteData;
        return $ret;

    }

}