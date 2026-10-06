<?php
// RANKING: quem terminou mais fases no menor tempo
require_once __DIR__ . '/includes/auth.php';
exigirLogin();

$eu = usuarioAtual($pdo);
$jogoId = (int) ($_GET['jogo'] ?? 0);

$jogos = $pdo->query('SELECT id, titulo FROM jogos ORDER BY titulo')->fetchAll();

$sql = 'SELECT u.usuario, a.arquivo AS avatar, j.titulo AS jogo,
               p.fases, p.tempo_segundos
        FROM pontuacoes p
        JOIN usuarios u ON u.id = p.usuario_id
        JOIN avatares a ON a.id = u.avatar_id
        JOIN jogos    j ON j.id = p.jogo_id';
$params = [];
if ($jogoId > 0) {
    $sql .= ' WHERE p.jogo_id = ?';
    $params[] = $jogoId;
}
$sql .= ' ORDER BY p.fases DESC, p.tempo_segundos ASC LIMIT 20';

$st = $pdo->prepare($sql);
$st->execute($params);
$linhas = $st->fetchAll();

function formataTempo(int $s): string
{
    return sprintf('%02d:%02d', intdiv($s, 60), $s % 60);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>JogAí — Ranking</title>
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

  <h1 class="titulo pequeno">RANKING — MELHORES TEMPOS</h1>

  <form method="get" class="filtro-ranking">
    <select class="campo" name="jogo" onchange="this.form.submit()">
      <option value="0">Todos os jogos</option>
      <?php foreach ($jogos as $j): ?>
        <option value="<?= (int) $j['id'] ?>" <?= $jogoId === (int) $j['id'] ? 'selected' : '' ?>>
          <?= e($j['titulo']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </form>

  <ol class="ranking">
    <?php foreach ($linhas as $i => $r): ?>
      <li>
        <span class="pos">#<?= $i + 1 ?></span>
        <img src="assets/avatares/<?= e($r['avatar']) ?>" alt="" loading="lazy">
        <div class="quem">
          <strong><?= e($r['usuario']) ?></strong>
          <span class="sub"><?= e($r['jogo']) ?> · <?= (int) $r['fases'] ?> fases</span>
        </div>
        <span class="tempo"><?= formataTempo((int) $r['tempo_segundos']) ?></span>
      </li>
    <?php endforeach; ?>
  </ol>

  <?php if (!$linhas): ?>
    <p class="sub centro vazio">Ninguém registrou tempo ainda. Jogue e seja o primeiro!</p>
  <?php endif; ?>
</div>

<script src="js/app.js"></script>
</body>
</html>
