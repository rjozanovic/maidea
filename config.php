<?php

/** @note you will need to fill out this information with you database settings.
 *
 * @requires user with grants to create a new database OR database created and user with privileges to create tables and access.
 *
 *
 */

namespace maidea;

class config
{

    const CONFIG_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'config.json';

    private function __construct(){}

    public static function getConfig()
    {
        return json_decode(file_get_contents(self::CONFIG_FILE), true);
    }

}
