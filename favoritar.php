<?php
// Endpoint chamado pelo JavaScript para favoritar / desfavoritar
require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

if (!estaLogado()) {
    http_response_code(401);
    echo json_encode(['erro' => 'nao_logado']);
    exit;
}

$usuarioId = (int) $_SESSION['usuario_id'];
$jogoId    = (int) ($_POST['jogo_id'] ?? 0);

if ($jogoId <= 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'jogo_invalido']);
    exit;
}

$st = $pdo->prepare('SELECT 1 FROM favoritos WHERE usuario_id = ? AND jogo_id = ?');
$st->execute([$usuarioId, $jogoId]);

if ($st->fetch()) {
    $pdo->prepare('DELETE FROM favoritos WHERE usuario_id = ? AND jogo_id = ?')
        ->execute([$usuarioId, $jogoId]);
    echo json_encode(['favorito' => false]);
} else {
    $pdo->prepare('INSERT INTO favoritos (usuario_id, jogo_id) VALUES (?,?)')
        ->execute([$usuarioId, $jogoId]);
    echo json_encode(['favorito' => true]);
}
