<?php

declare(strict_types=1);

namespace GeekPortal\Services;

final class TmdbApiService
{
    public function __construct(private readonly string $apiKey)
    {
    }
}