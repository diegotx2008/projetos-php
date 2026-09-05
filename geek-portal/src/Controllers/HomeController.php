<?php

declare(strict_types=1);

namespace GeekPortal\Controllers;

use GeekPortal\Core\View;

final class HomeController
{
    public function index(): void
    {
        View::render('home');
    }
}