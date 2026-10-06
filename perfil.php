<?php
// PERFIL: dados do jogador e troca de avatar
require_once __DIR__ . '/includes/auth.php';
exigirLogin();

$eu = usuarioAtual($pdo);
$avatares = $pdo->query('SELECT id, nome, arquivo FROM avatares ORDER BY id')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo = (int) ($_POST['avatar_id'] ?? 0);
    if ($novo > 0) {
        $pdo->prepare('UPDATE usuarios SET avatar_id = ? WHERE id = ?')->execute([$novo, $eu['id']]);
        header('Location: perfil.php');
        exit;
    }
}

$st = $pdo->prepare('SELECT MAX(fases) AS fases, MIN(tempo_segundos) AS melhor
                     FROM pontuacoes WHERE usuario_id = ?');
$st->execute([$eu['id']]);
$stats = $st->fetch();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>JogAí — Meu perfil</title>
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

  <div class="perfil-cabecalho">
    <img src="assets/avatares/<?= e($eu['avatar_arquivo']) ?>" alt="Meu avatar">
    <div>
      <h1 class="titulo pequeno"><?= e($eu['usuario']) ?></h1>
      <p class="sub">
        Avatar <?= e($eu['avatar_nome']) ?> ·
        <?= (int) ($stats['fases'] ?? 0) ?> fases ·
        melhor tempo
        <?= $stats['melhor'] ? sprintf('%02d:%02d', intdiv((int)$stats['melhor'], 60), (int)$stats['melhor'] % 60) : '--:--' ?>
      </p>
    </div>
  </div>

  <h2 class="secao">TROCAR AVATAR</h2>
  <form method="post" id="form-perfil">
    <input type="hidden" name="avatar_id" id="avatar_id" value="">
    <div class="grade-avatares">
      <?php foreach ($avatares as $a): ?>
        <button type="submit" name="avatar_id" value="<?= (int) $a['id'] ?>" class="avatar-op">
          <img src="assets/avatares/<?= e($a['arquivo']) ?>" alt="Avatar <?= e($a['nome']) ?>" loading="lazy">
          <span><?= e($a['nome']) ?></span>
        </button>
      <?php endforeach; ?>
    </div>
  </form>

  <a class="link-suave sair" href="sair.php">Sair da conta</a>
</div>

<script src="js/app.js"></script>
</body>
</html>
