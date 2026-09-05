<?php

declare(strict_types=1);

namespace GeekPortal\Config;

final class Config
{
    public static function load(): array
    {
        return [
            'database' => [
                'dsn' => getenv('DB_DSN') ?: 'mysql:host=127.0.0.1;dbname=geek_portal;charset=utf8mb4',
                'username' => getenv('DB_USERNAME') ?: 'root',
                'password' => getenv('DB_PASSWORD') ?: '',
            ],
            'tmdb_api_key' => getenv('TMDB_API_KEY') ?: '',
        ];
    }
}