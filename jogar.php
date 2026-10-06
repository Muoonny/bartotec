<?php
// Abre o jogo (dentro de um iframe) e registra no histórico
require_once __DIR__ . '/includes/auth.php';
exigirLogin();

$eu = usuarioAtual($pdo);
$id = (int) ($_GET['id'] ?? 0);

$st = $pdo->prepare('SELECT * FROM jogos WHERE id = ?');
$st->execute([$id]);
$jogo = $st->fetch();

if (!$jogo) {
    header('Location: jogos.php');
    exit;
}

// grava no histórico
$pdo->prepare('INSERT INTO historico (usuario_id, jogo_id) VALUES (?,?)')
    ->execute([$eu['id'], $jogo['id']]);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($jogo['titulo']) ?> — JogAí</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="tela-centro">

<div class="painel largo">
  <div class="topo">
    <span class="logo">JOG<span>AÍ</span></span>
    <a class="link-suave" href="jogos.php">Voltar aos jogos</a>
  </div>

  <h1 class="titulo pequeno"><?= e($jogo['titulo']) ?></h1>

  <?php if (!empty($jogo['url'])): ?>
    <iframe class="frame-jogo" src="<?= e($jogo['url']) ?>" allowfullscreen title="<?= e($jogo['titulo']) ?>"></iframe>
  <?php else: ?>
    <p class="sub">Este jogo ainda não tem link. Conecte a API de jogos (veja o COMO-RODAR.md).</p>
  <?php endif; ?>

  <!-- Envio do tempo para o ranking -->
  <h2 class="secao">TERMINOU AS FASES? REGISTRE SEU TEMPO</h2>
  <form class="linha-3" method="post" action="salvar_tempo.php">
    <input type="hidden" name="jogo_id" value="<?= (int) $jogo['id'] ?>">
    <input class="campo" type="number" name="fases" min="1" placeholder="Fases concluídas" required>
    <input class="campo" type="text" name="tempo" placeholder="Tempo (mm:ss)" pattern="\d{1,3}:[0-5]\d" required>
    <button class="btn" type="submit">ENVIAR</button>
  </form>
</div>

<script src="js/app.js"></script>
</body>
</html>
