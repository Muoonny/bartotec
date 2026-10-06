<?php
// Funções de sessão / login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

function estaLogado(): bool
{
    return isset($_SESSION['usuario_id']);
}

function exigirLogin(): void
{
    if (!estaLogado()) {
        header('Location: index.php');
        exit;
    }
}

function usuarioAtual(PDO $pdo): ?array
{
    if (!estaLogado()) {
        return null;
    }
    $sql = 'SELECT u.id, u.usuario, a.nome AS avatar_nome, a.arquivo AS avatar_arquivo
            FROM usuarios u
            JOIN avatares a ON a.id = u.avatar_id
            WHERE u.id = ?';
    $st = $pdo->prepare($sql);
    $st->execute([$_SESSION['usuario_id']]);
    $u = $st->fetch();
    return $u ?: null;
}

function e(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}
