<?php

abstract class migrationAbstract
{
    protected $pdo = null;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    abstract public function upgrade();
}