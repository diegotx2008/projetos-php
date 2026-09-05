<?php

namespace App\Models\Animes;

use App\Config\Database;
use PDO;

class AnimeModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function listarMaisAssistidosPorGenero(int $usuarioId): array
    {
        $sql = "
            SELECT m.*, g.nome as genero_nome
            FROM midias_salvas m
            JOIN generos g ON m.genero_id = g.id
            WHERE m.tipo = 'anime' AND m.usuario_id = :usuario_id
            ORDER BY m.visualizacoes DESC LIMIT 12";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuarioId]);
        
        $animes = $stmt->fetchAll();
        $agrupados = [];
        foreach ($animes as $anime) {
            $agrupados[$anime['genero_nome']][] = $anime;
        }

        return $agrupados;
    }
}