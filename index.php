<?php
// Página de LOGIN
require_once __DIR__ . '/includes/auth.php';

if (estaLogado()) {
    header('Location: jogos.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = $_POST['senha'] ?? '';

    if ($usuario === '' || $senha === '') {
        $erro = 'Preencha usuário e senha.';
    } else {
        $st = $pdo->prepare('SELECT id, senha_hash FROM usuarios WHERE usuario = ?');
        $st->execute([$usuario]);
        $u = $st->fetch();

        if ($u && password_verify($senha, $u['senha_hash'])) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $u['id'];
            header('Location: jogos.php');
            exit;
        }
        $erro = 'Usuário ou senha incorretos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>JogAí — Entrar</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="tela-centro">

  <div class="painel login-grid">
    <div class="login-arte">
      <span class="logo">JOG<span>AÍ</span></span>
      <img src="assets/login-art.jpg" alt="Controle de videogame neon">
    </div>

    <div class="login-form">
      <h1 class="titulo">Bem vindo de volta</h1>
      <p class="sub">Entre na sua conta e continue jogando</p>

      <?php if ($erro): ?>
        <p class="erro"><?= e($erro) ?></p>
      <?php endif; ?>

      <form method="post" novalidate>
        <input class="campo" type="text" name="usuario" placeholder="Nome de usuário" required>
        <input class="campo" type="password" name="senha" placeholder="Senha" required>
        <button class="btn" type="submit">ENTRAR</button>
      </form>

      <p class="sub centro">
        Ainda não possui uma conta? <a href="registro.php">criar</a>
      </p>
    </div>
  </div>

  <script src="js/app.js"></script>
</body>
</html>
