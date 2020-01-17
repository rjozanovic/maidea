<?php

namespace maidea\migration;

abstract class migrationAbstract
{
    /**
     * @var PDO $pdo
     */
    protected $pdo = null;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    abstract public function upgrade();
}