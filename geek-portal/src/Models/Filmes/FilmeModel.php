<?php

namespace App\Models\Filmes;

use App\Config\Database;
use PDO;

class FilmeModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function listarComFiltros(int $usuarioId, array $filtros, int $limit, int $offset): array
    {
        $sql = "SELECT f.*, g.nome as genero_nome 
                FROM midias_salvas f
                JOIN generos g ON f.genero_id = g.id
                WHERE f.usuario_id = :usuario_id AND f.tipo = 'filme'";

        $params = [':usuario_id' => $usuarioId];

        if (!empty($filtros['busca'])) {
            $sql .= " AND (f.titulo LIKE :busca OR f.ano_lancamento = :busca_ano)";
            $params[':busca'] = '%' . $filtros['busca'] . '%';
            $params[':busca_ano'] = (int)$filtros['busca'];
        }

        if (!empty($filtros['genero_id'])) {
            $sql .= " AND f.genero_id = :genero_id";
            $params[':genero_id'] = $filtros['genero_id'];
        }

        if (!empty($filtros['nota_imdb'])) {
            $sql .= " AND f.nota_imdb >= :nota_imdb";
            $params[':nota_imdb'] = $filtros['nota_imdb'];
        }

        $sql .= " ORDER BY f.id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function contarTotal(int $usuarioId, array $filtros): int
    {
        $sql = "SELECT COUNT(*) FROM midias_salvas WHERE usuario_id = :usuario_id AND tipo = 'filme'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuarioId]);
        return (int)$stmt->fetchColumn();
    }

    public function salvar(array $dados): int
    {
        $sql = "INSERT INTO midias_salvas 
                (usuario_id, genero_id, tmdb_id, tipo, titulo, sinopse, diretores_autores, capa_url, ano_lancamento, nota_usuario, nota_imdb, idioma_original)
                VALUES 
                (:usuario_id, :genero_id, :tmdb_id, 'filme', :titulo, :sinopse, :diretores, :capa_url, :ano, :nota_usuario, :nota_imdb, :idioma)
                ON DUPLICATE KEY UPDATE nota_usuario = :nota_usuario";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id'    => $dados['usuario_id'],
            ':genero_id'     => $dados['genero_id'],
            ':tmdb_id'       => $dados['tmdb_id'],
            ':titulo'        => $dados['titulo'],
            ':sinopse'       => $dados['sinopse'],
            ':diretores'     => $dados['diretores'],
            ':capa_url'      => $dados['capa_url'],
            ':ano'           => $dados['ano'],
            ':nota_usuario'  => $dados['nota_usuario'],
            ':nota_imdb'     => $dados['nota_imdb'],
            ':idioma'        => $dados['idioma'],
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function vincularLista(int $midiaId, int $listaId): void
    {
        $sql = "INSERT IGNORE INTO rel_midias_listas (midia_id, lista_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$midiaId, $listaId]);
    }
}