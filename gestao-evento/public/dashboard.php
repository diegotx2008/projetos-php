<?php
require_once __DIR__ . '/../src/bootstrap.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = getPDO();

// Consulta detalhada consolidando o total pago de cada um
$query = "
    SELECT 
        p.id, 
        p.nome, 
        p.patriarca, 
        p.valor_total,
        COALESCE(SUM(l.valor), 0) AS pago,
        (p.valor_total - COALESCE(SUM(l.valor), 0)) AS pendente
    FROM participantes p
    LEFT JOIN lancamentos l ON p.id = l.participante_id
    GROUP BY p.id
    ORDER BY p.patriarca, p.nome
";

$participantes = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Cálculo dos indicadores principais
$totais = [
    'previsto' => array_sum(array_column($participantes, 'valor_total')),
    'pago'     => array_sum(array_column($participantes, 'pago')),
    'pendente' => array_sum(array_column($participantes, 'pendente'))
];

echo $twig->render('dashboard.html.twig', [
    'participantes' => $participantes,
    'totais' => $totais
]);