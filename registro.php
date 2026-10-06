<?php
// Página de CADASTRO com escolha de avatar
require_once __DIR__ . '/includes/auth.php';

if (estaLogado()) {
    header('Location: jogos.php');
    exit;
}

$avatares = $pdo->query('SELECT id, nome, arquivo FROM avatares ORDER BY id')->fetchAll();
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario'] ?? '');
    $senha    = $_POST['senha'] ?? '';
    $confirma = $_POST['confirma'] ?? '';
    $avatarId = (int) ($_POST['avatar_id'] ?? 0);

    if ($usuario === '' || $senha === '') {
        $erro = 'Preencha usuário e senha.';
    } elseif (mb_strlen($usuario) < 3) {
        $erro = 'O nome de usuário precisa ter pelo menos 3 letras.';
    } elseif (mb_strlen($senha) < 4) {
        $erro = 'A senha precisa ter pelo menos 4 caracteres.';
    } elseif ($senha !== $confirma) {
        $erro = 'As senhas não são iguais.';
    } elseif ($avatarId <= 0) {
        $erro = 'Escolha um avatar para continuar.';
    } else {
        $st = $pdo->prepare('SELECT id FROM usuarios WHERE usuario = ?');
        $st->execute([$usuario]);
        if ($st->fetch()) {
            $erro = 'Esse nome de usuário já existe.';
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $ins  = $pdo->prepare('INSERT INTO usuarios (usuario, senha_hash, avatar_id) VALUES (?,?,?)');
            $ins->execute([$usuario, $hash, $avatarId]);

            session_regenerate_id(true);
            $_SESSION['usuario_id'] = (int) $pdo->lastInsertId();
            header('Location: jogos.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>JogAí — Criar conta</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="tela-centro">

  <div class="painel largo">
    <span class="logo">JOG<span>AÍ</span></span>
    <h1 class="titulo">Criar conta</h1>
    <p class="sub">Escolha seu nome de jogador e o avatar que vai te representar no ranking</p>

    <?php if ($erro): ?>
      <p class="erro"><?= e($erro) ?></p>
    <?php endif; ?>

    <form method="post" id="form-registro">
      <div class="linha-3">
        <input class="campo" type="text" name="usuario" placeholder="Nome de usuário" required>
        <input class="campo" type="password" name="senha" placeholder="Senha" required>
        <input class="campo" type="password" name="confirma" placeholder="Confirmar senha" required>
      </div>

      <h2 class="secao">ESCOLHA SEU AVATAR</h2>
      <input type="hidden" name="avatar_id" id="avatar_id" value="">

      <div class="grade-avatares">
        <?php foreach ($avatares as $a): ?>
          <button type="button" class="avatar-op" data-id="<?= (int) $a['id'] ?>">
            <img src="assets/avatares/<?= e($a['arquivo']) ?>" alt="Avatar <?= e($a['nome']) ?>" loading="lazy">
            <span><?= e($a['nome']) ?></span>
          </button>
        <?php endforeach; ?>
      </div>

      <div class="acoes">
        <button class="btn" type="submit">CRIAR E JOGAR</button>
        <a class="link-suave" href="index.php">Já tenho conta</a>
      </div>
    </form>
  </div>

  <script src="js/app.js"></script>
</body>
</html>
