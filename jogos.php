<?php
// Página principal: lista de jogos, categorias, busca, favoritos e histórico
require_once __DIR__ . '/includes/auth.php';
exigirLogin();

$eu = usuarioAtual($pdo);

$aba       = $_GET['aba'] ?? 'todos';          // todos | favoritos | historico
$categoria = trim($_GET['categoria'] ?? '');
$busca     = trim($_GET['busca'] ?? '');

$categorias = $pdo->query('SELECT DISTINCT categoria FROM jogos ORDER BY categoria')
                  ->fetchAll(PDO::FETCH_COLUMN);

// Monta a consulta conforme a aba escolhida
$params = [$eu['id']];

if ($aba === 'favoritos') {
    $sql = 'SELECT j.*, 1 AS favorito FROM jogos j
            JOIN favoritos f ON f.jogo_id = j.id AND f.usuario_id = ?';
} elseif ($aba === 'historico') {
    $sql = 'SELECT j.*, (SELECT COUNT(*) FROM favoritos f WHERE f.jogo_id = j.id AND f.usuario_id = ?) AS favorito
            FROM jogos j
            JOIN historico h ON h.jogo_id = j.id AND h.usuario_id = ?';
    $params[] = $eu['id'];
} else {
    $sql = 'SELECT j.*, (SELECT COUNT(*) FROM favoritos f WHERE f.jogo_id = j.id AND f.usuario_id = ?) AS favorito
            FROM jogos j';
}

$where = [];
if ($categoria !== '') {
    $where[]  = 'j.categoria = ?';
    $params[] = $categoria;
}
if ($busca !== '') {
    $where[]  = 'j.titulo LIKE ?';
    $params[] = '%' . $busca . '%';
}
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' GROUP BY j.id ORDER BY j.titulo';

$st = $pdo->prepare($sql);
$st->execute($params);
$jogos = $st->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>JogAí — Todos os jogos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="app">
  <!-- MENU LATERAL -->
  <aside class="painel menu" id="menu">
    <span class="logo">JOG<span>AÍ</span></span>
    <nav>
      <a class="item <?= $aba === 'todos' ? 'ativo' : '' ?>" href="jogos.php?aba=todos">Todos os jogos</a>
      <a class="item <?= $aba === 'favoritos' ? 'ativo' : '' ?>" href="jogos.php?aba=favoritos">Favoritos</a>
      <a class="item <?= $aba === 'historico' ? 'ativo' : '' ?>" href="jogos.php?aba=historico">Histórico</a>
      <a class="item" href="ranking.php">Ranking</a>
    </nav>

    <p class="rotulo">CATEGORIAS</p>
    <div class="categorias-menu">
      <?php foreach ($categorias as $c): ?>
        <a class="cat <?= $categoria === $c ? 'ativo' : '' ?>"
           href="jogos.php?aba=<?= e($aba) ?>&categoria=<?= urlencode($c) ?>"><?= e($c) ?></a>
      <?php endforeach; ?>
    </div>

    <a class="link-suave sair" href="sair.php">Sair da conta</a>
  </aside>

  <!-- CONTEÚDO -->
  <main class="painel conteudo">
    <header class="topo">
      <button class="btn-menu" id="abrir-menu" aria-label="Abrir menu">☰</button>
      <div class="topo-titulo">
        <h1 class="titulo pequeno">TODOS OS JOGOS</h1>
        <p class="sub">Explore milhares de jogos incríveis!</p>
      </div>

      <form class="busca" method="get">
        <input type="hidden" name="aba" value="<?= e($aba) ?>">
        <input class="campo" type="search" name="busca" placeholder="Buscar jogos..." value="<?= e($busca) ?>">
      </form>

      <a class="perfil-chip" href="perfil.php">
        <img src="assets/avatares/<?= e($eu['avatar_arquivo']) ?>" alt="Meu avatar">
        <span><?= e($eu['usuario']) ?></span>
      </a>
    </header>

    <div class="chips">
      <?php foreach ($categorias as $c): ?>
        <a class="chip <?= $categoria === $c ? 'ativo' : '' ?>"
           href="jogos.php?aba=<?= e($aba) ?>&categoria=<?= $categoria === $c ? '' : urlencode($c) ?>"><?= e($c) ?></a>
      <?php endforeach; ?>
    </div>

    <section class="grade-jogos">
      <?php foreach ($jogos as $j): ?>
        <article class="card-jogo">
          <a href="jogar.php?id=<?= (int) $j['id'] ?>">
            <?php if (!empty($j['thumb'])): ?>
              <img class="capa" src="<?= e($j['thumb']) ?>" alt="<?= e($j['titulo']) ?>" loading="lazy">
            <?php else: ?>
              <div class="capa placeholder"></div>
            <?php endif; ?>
          </a>
          <button class="fav <?= $j['favorito'] ? 'ativo' : '' ?>"
                  data-jogo="<?= (int) $j['id'] ?>" aria-label="Favoritar">★</button>
          <h3><?= e($j['titulo']) ?></h3>
          <p class="sub"><?= e($j['categoria']) ?></p>
        </article>
      <?php endforeach; ?>
    </section>

    <?php if (!$jogos): ?>
      <p class="sub centro vazio">Nenhum jogo por aqui ainda.</p>
    <?php endif; ?>
  </main>
</div>

<script src="js/app.js"></script>
</body>
</html>
