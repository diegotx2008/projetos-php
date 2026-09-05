<?php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View
{
    public static function render(string $template, array $data = []): void
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../views');
        $twig = new Environment($loader, [
            'cache' => false,
            'debug' => true,
        ]);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $twig->addGlobal('session', $_SESSION);

        echo $twig->render($template, $data);
    }
}