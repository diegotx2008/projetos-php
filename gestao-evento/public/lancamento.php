<?php
require_once __DIR__ . '/../src/bootstrap.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = getPDO();
$sucesso = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $participante_id = $_POST['participante_id'];
    $valor = $_POST['valor'];
    $data_pagamento = $_POST['data_pagamento'];

    $stmt = $pdo->prepare("INSERT INTO lancamentos (participante_id, valor, data_pagamento) VALUES (?, ?, ?)");
    if ($stmt->execute([$participante_id, $valor, $data_pagamento])) {
        $sucesso = "Lançamento efetuado com sucesso!";
    }
}

$participantes = $pdo->query("SELECT id, nome, patriarca FROM participantes ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);

echo $twig->render('lancamento.html.twig', [
    'participantes' => $participantes,
    'sucesso' => $sucesso
]);