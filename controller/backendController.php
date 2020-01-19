<?php

namespace maidea\controller;

class backendController extends controllerAbstract
{

    public function pullCityDataAction()
    {
        $cfg = new \maidea\model\config();
        $cfg->setData(array('name' => 'test', 'value' => 'trst'));
        sleep(20);
        die('pullCityDataAction');
    }

}