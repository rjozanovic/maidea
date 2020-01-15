<?php

/**
 * Class migration_1
 *
 * Initial migration, creates database and sets up for further migrations.
 *
 */

class migration_1 extends migrationAbstract
{
    public function upgrade()
    {
        echo 'Upgrading 1';

        //TODO
        //create database
        //create user with perm on database
        //modify conf to use new db connection stuff
        //create conf table
        //initialise conf variables (migration version!!!)

    }
}
