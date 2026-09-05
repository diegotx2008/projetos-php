<?php

namespace App\Controllers\Filmes;

use App\Models\Filmes\FilmeModel;
use App\Models\GeneroModel;
use App\Models\ListaModel;
use App\Services\TmdbApiService;
use App\Core\View;

class FilmeController
{
    private FilmeModel $filmeModel;
    private TmdbApiService $tmdbApi;

    public function __construct()
    {
        $this->filmeModel = new FilmeModel();
        $this->tmdbApi = new TmdbApiService();
    }

    public function index(): void
    {
        $usuarioId = $_SESSION['user_id'];
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $perPage;

        $filtros = [
            'busca' => $_GET['busca'] ?? '',
            'genero_id' => $_GET['genero_id'] ?? '',
            'nota_imdb' => $_GET['nota_imdb'] ?? ''
        ];

        // 1. Busca no Banco Local
        $filmesSalvos = $this->filmeModel->listarComFiltros($usuarioId, $filtros, $perPage, $offset);
        $totalSalvos = $this->filmeModel->contarTotal($usuarioId, $filtros);

        // 2. Se houver busca e poucos resultados no banco local, complementa via API Externa
        $filmesApi = [];
        if (!empty($filtros['busca'])) {
            $resultadoApi = $this->tmdbApi->buscarMidia($filtros['busca'], 'movie');
            foreach ($resultadoApi['results'] as $item) {
                $filmesApi[] = [
                    'id' => $item['id'],
                    'titulo' => $item['title'],
                    'ano' => !empty($item['release_date']) ? substr($item['release_date'], 0, 4) : 'N/A',
                    'capa_url' => $this->tmdbApi->getImageFullUrl($item['poster_path']),
                    'nota_imdb' => $item['vote_average'],
                    'sinopse' => $item['overview'],
                    'eh_externo' => true
                ];
            }
        }

        $generoModel = new GeneroModel();
        $listaModel = new ListaModel();

        View::render('filmes/catalogo.html.twig', [
            'filmes_salvos' => $filmesSalvos,
            'filmes_api'    => $filmesApi,
            'generos'       => $generoModel->listarTodos(),
            'listas'        => $listaModel->listarPorUsuario($usuarioId),
            'pagination'    => [
                'current'  => $page,
                'per_page' => $perPage,
                'pages'    => ceil($totalSalvos / $perPage)
            ],
            'filters'       => $filtros
        ]);
    }

    public function salvarNaLista(): void
    {
        $usuarioId = $_SESSION['user_id'];
        $tmdbId = (int)$_POST['tmdb_id'];
        $listaId = (int)$_POST['lista_id'];
        $generoId = (int)$_POST['genero_id'];
        $notaUsuario = (int)$_POST['nota_usuario'];

        $detalhes = $this->tmdbApi->obterDetalhes($tmdbId, 'movie');

        $diretores = [];
        if (isset($detalhes['credits']['crew'])) {
            foreach ($detalhes['credits']['crew'] as $crew) {
                if ($crew['job'] === 'Director') {
                    $diretores[] = $crew['name'];
                }
            }
        }

        $midiaId = $this->filmeModel->salvar([
            'usuario_id'   => $usuarioId,
            'genero_id'    => $generoId,
            'tmdb_id'      => $tmdbId,
            'titulo'       => $detalhes['title'] ?? 'Sem Título',
            'sinopse'      => $detalhes['overview'] ?? '',
            'diretores'    => implode(', ', $diretores),
            'capa_url'     => $this->tmdbApi->getImageFullUrl($detalhes['poster_path'] ?? null),
            'ano'          => isset($detalhes['release_date']) ? (int)substr($detalhes['release_date'], 0, 4) : 0,
            'nota_usuario' => $notaUsuario,
            'nota_imdb'    => $detalhes['vote_average'] ?? 0.0,
            'idioma'       => $detalhes['original_language'] ?? 'en'
        ]);

        $this->filmeModel->vincularLista($midiaId, $listaId);

        header('Location: /filmes?status=success');
        exit;
    }
}