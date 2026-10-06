<?php
// Recebe o tempo do jogador e guarda para o ranking
require_once __DIR__ . '/includes/auth.php';
exigirLogin();

$eu     = usuarioAtual($pdo);
$jogoId = (int) ($_POST['jogo_id'] ?? 0);
$fases  = (int) ($_POST['fases'] ?? 0);
$tempo  = trim($_POST['tempo'] ?? '');

// converte "mm:ss" em segundos
$segundos = 0;
if (preg_match('/^(\d{1,3}):([0-5]\d)$/', $tempo, $m)) {
    $segundos = ((int) $m[1]) * 60 + (int) $m[2];
}

if ($jogoId > 0 && $fases > 0 && $segundos > 0) {
    $pdo->prepare('INSERT INTO pontuacoes (usuario_id, jogo_id, fases, tempo_segundos) VALUES (?,?,?,?)')
        ->execute([$eu['id'], $jogoId, $fases, $segundos]);
}

header('Location: ranking.php');
exit;
