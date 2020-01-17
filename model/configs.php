<?php

namespace maidea\model;

class configs extends modelsAbstract
{

    public static function getModelName()
    {
        return 'config';
    }

    public function fetchMigrationVersion()
    {
        $this->setWhere('key = :key', array('key' => 'migration_version'), array('key' => \PDO::PARAM_STR));
        $this->load();
        return $this->current();
    }

}