<?php
// ============================================
// IMPORTAR JOGOS DA API
// Abra no navegador: http://localhost/jogai/importar_api.php
// Ele busca os jogos na API e salva na tabela "jogos".
// ============================================
require_once __DIR__ . '/includes/db.php';

header('Content-Type: text/plain; charset=utf-8');

if (API_JOGOS_KEY === 'COLE_SUA_CHAVE_AQUI') {
    exit("Abra includes/config.php e coloque a URL e a chave da sua API primeiro.\n");
}

// ---- 1) Chama a API ----
$ch = curl_init(API_JOGOS_URL);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 20,
    CURLOPT_HTTPHEADER     => [
        'Accept: application/json',
        // Ajuste o cabeçalho conforme a documentação da SUA api.
        // Exemplos comuns:
        'Authorization: Bearer ' . API_JOGOS_KEY,
        // 'X-RapidAPI-Key: ' . API_JOGOS_KEY,
    ],
]);
$resposta = curl_exec($ch);
$status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$erroCurl = curl_error($ch);
curl_close($ch);

if ($resposta === false) {
    exit("Erro de conexão: $erroCurl\n");
}
if ($status !== 200) {
    exit("A API respondeu com status $status:\n$resposta\n");
}

$dados = json_decode($resposta, true);
if (!is_array($dados)) {
    exit("A resposta não é um JSON válido.\n");
}

// Algumas APIs devolvem { "data": [...] } ou { "games": [...] }.
// Descomente a linha que combinar com a sua:
// $dados = $dados['data'];
// $dados = $dados['games'];

// ---- 2) Salva no banco ----
$sql = 'INSERT INTO jogos (externo_id, titulo, categoria, thumb, url)
        VALUES (:externo_id, :titulo, :categoria, :thumb, :url)
        ON DUPLICATE KEY UPDATE
          titulo = VALUES(titulo), categoria = VALUES(categoria),
          thumb  = VALUES(thumb),  url = VALUES(url)';
$st = $pdo->prepare($sql);

$total = 0;
foreach ($dados as $g) {
    // AJUSTE OS NOMES DOS CAMPOS conforme a sua API:
    $st->execute([
        ':externo_id' => (string) ($g['id']        ?? ''),
        ':titulo'     => (string) ($g['title']     ?? $g['name'] ?? 'Sem título'),
        ':categoria'  => (string) ($g['category']  ?? $g['genre'] ?? 'Ação'),
        ':thumb'      => (string) ($g['thumbnail'] ?? $g['image'] ?? ''),
        ':url'        => (string) ($g['url']       ?? $g['game_url'] ?? ''),
    ]);
    $total++;
}

echo "Pronto! $total jogos importados/atualizados.\n";
