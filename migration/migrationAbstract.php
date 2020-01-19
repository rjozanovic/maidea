<?php

namespace maidea\migration;

abstract class migrationAbstract
{
    /**
     * @var PDO $pdo
     */
    protected $pdo = null;

    public function __construct()
    {
        $this->pdo = \maidea\db::getPdoHandle();
    }

    abstract public function upgrade();

}