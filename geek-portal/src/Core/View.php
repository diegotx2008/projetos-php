<?php

declare(strict_types=1);

namespace GeekPortal\Core;

final class View
{
    public static function render(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../../views/' . $template . '.html.twig';
    }
}