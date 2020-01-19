<?php

namespace maidea\model;

class configs extends modelsAbstract
{

    public static function getModelName()
    {
        return 'config';
    }

    /**
     * @param string $name
     * @return string
     */
    public function getValue($name)
    {
        $this->setWhere('name = :name', array('name' => $name), array('name' => \PDO::PARAM_STR));
        $this->load();
        return $this->current()->getValue();
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function setValue($name, $value)
    {
        $this->setWhere('name = :name', array('name' => (string)$name), array('name' => \PDO::PARAM_STR));
        $this->load();
        $this->current()->setValue($value)->save();

    }

    /**
     * @return int
     */
    public function getMigrationVersion()
    {
        return (int)$this->getValue('migration_version');
    }

    public function setMigrationVersion($versionNum)
    {
        return $this->setValue('migration_version', $versionNum);
    }

    /**
     * @return bool
     */
    public function getMigrationInProgress()
    {
        return (bool)$this->getValue('migration_in_progress');
    }

    public function setMigrationInProgress($bool)
    {
        return $this->setValue('migration_in_progress', (int)$bool);
    }

    /**
     * @return \DateTime
     */
    public function getMigrationLastStarted()
    {
        return new \DateTime($this->getValue('migration_last_started'));
    }

    /**
     * @return int
     */
    public function getMigrationAllowedDuration()
    {
        return (int)$this->getValue('migration_allowed_duration');
    }

    /**
     * @return int
     */
    public function getWeatherValidDuration()
    {
        return (int)$this->getValue('weather_valid_duration');
    }


}