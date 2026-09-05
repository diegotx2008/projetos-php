<?php

declare(strict_types=1);

namespace GeekPortal\Config;

use PDO;

final class Database
{
    public static function connection(): PDO
    {
        $config = Config::load();

        return new PDO(
            $config['database']['dsn'],
            $config['database']['username'],
            $config['database']['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
}