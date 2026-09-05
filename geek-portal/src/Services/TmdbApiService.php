<?php

namespace App\Services;

use GuzzleHttp\Client;

class TmdbApiService
{
    private Client $client;
    private string $apiKey;
    private string $baseUrl = 'https://api.themoviedb.org/3/';
    private string $imageBaseUrl = 'https://image.tmdb.org/t/p/w500';

    public function __construct()
    {
        $this->apiKey = $_ENV['TMDB_API_KEY'] ?? '';
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => 5.0,
        ]);
    }

    public function buscarMidia(string $query, string $type = 'movie', int $year = null): array
    {
        $endpoint = "search/{$type}";
        $params = [
            'api_key'  => $this->apiKey,
            'query'    => $query,
            'language' => 'pt-BR',
        ];

        if ($year) {
            $params['year'] = $year;
        }

        try {
            $response = $this->client->get($endpoint, ['query' => $params]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return ['results' => []];
        }
    }

    public function obterDetalhes(int $tmdbId, string $type = 'movie'): array
    {
        $endpoint = "{$type}/{$tmdbId}";
        try {
            $response = $this->client->get($endpoint, [
                'query' => [
                    'api_key'  => $this->apiKey,
                    'language' => 'pt-BR',
                    'append_to_response' => 'credits,watch/providers'
                ]
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getImageFullUrl(?string $path): string
    {
        if (!$path) {
            return 'https://via.placeholder.com/500x750?text=Sem+Capa';
        }
        return $this->imageBaseUrl . $path;
    }
}