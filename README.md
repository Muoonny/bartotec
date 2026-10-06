# JogAí — como rodar no seu notebook

## 1. Instalar o servidor
Baixe e instale o **XAMPP** (https://www.apachefriends.org).
Abra o painel do XAMPP e clique em **Start** no **Apache** e no **MySQL**.

## 2. Colocar a pasta no lugar certo
Copie a pasta `jogai-php` inteira para dentro de:

- Windows: `C:\xampp\htdocs\`
- Mac: `/Applications/XAMPP/htdocs/`

Renomeie a pasta para `jogai` (fica mais curto).

## 3. Criar o banco de dados
1. Abra `http://localhost/phpmyadmin`
2. Clique na aba **Importar**
3. Escolha o arquivo `banco.sql` e clique em **Executar**

Isso cria o banco `jogai` com as tabelas de usuários, avatares, jogos,
favoritos, histórico e pontuações (ranking), já com os 15 avatares cadastrados.

## 4. Conferir a senha do banco
Abra `includes/config.php`. No XAMPP o padrão já está certo:
usuário `root` e senha vazia. Se a sua for diferente, troque ali.

## 5. Abrir o site
`http://localhost/jogai/`

Clique em **criar**, faça seu cadastro, escolha um avatar e você cai direto na
página de jogos.

---

## Estrutura dos arquivos

| Arquivo | Para que serve |
|---|---|
| `index.php` | Página de login |
| `registro.php` | Cadastro + escolha dos 15 avatares |
| `jogos.php` | Página principal: menu, categorias, busca, favoritos, histórico |
| `jogar.php` | Abre o jogo e permite enviar o tempo das fases |
| `salvar_tempo.php` | Recebe o tempo e grava para o ranking |
| `ranking.php` | Ranking por fases concluídas e menor tempo |
| `perfil.php` | Perfil do jogador e troca de avatar |
| `favoritar.php` | Chamado pelo JavaScript ao clicar na estrela |
| `sair.php` | Sai da conta |
| `includes/config.php` | Dados do banco e da API |
| `includes/db.php` | Conexão com o MySQL (PDO) |
| `includes/auth.php` | Sessão e login |
| `css/style.css` | Todo o visual neon roxo |
| `js/app.js` | Escolha de avatar, menu, favoritos e busca |
| `banco.sql` | Cria o banco e as tabelas |
| `importar_api.php` | Traz os jogos da API para o banco |
| `assets/avatares/` | Os 15 avatares |

---

## 6. Conectar a API de jogos

1. Abra `includes/config.php` e preencha:

```php
define('API_JOGOS_URL', 'https://a-url-da-sua-api.com/games');
define('API_JOGOS_KEY', 'sua-chave-aqui');
```

2. Veja na documentação da sua API como ela pede a chave. As duas formas mais
   comuns já estão no `importar_api.php`; deixe descomentada só a sua:

```php
'Authorization: Bearer ' . API_JOGOS_KEY,   // maioria das APIs
// 'X-RapidAPI-Key: ' . API_JOGOS_KEY,      // APIs da RapidAPI
```

3. Veja como vem o JSON da API (abra a URL no navegador ou no Postman). Se os
   jogos vierem dentro de uma chave, descomente a linha certa:

```php
// $dados = $dados['data'];
// $dados = $dados['games'];
```

4. Ajuste os nomes dos campos no `importar_api.php` para os nomes que a sua API
   usa (`title`/`name`, `thumbnail`/`image`, `game_url`/`url`, etc.).

5. Abra no navegador: `http://localhost/jogai/importar_api.php`
   Vai aparecer algo como "Pronto! 120 jogos importados/atualizados."

6. Volte para `http://localhost/jogai/jogos.php` — os jogos da API aparecem com
   as capas, e ao clicar o jogo abre dentro do site.

> Se um jogo não abrir dentro do site, é porque o serviço bloqueia iframe. Nesse
> caso troque o `<iframe>` do `jogar.php` por um link `target="_blank"`.

---

## 7. Ranking

O ranking usa a tabela `pontuacoes`. Hoje o jogador registra o tempo na tela do
jogo (campos "fases concluídas" e "tempo mm:ss"). Se a sua API/jogo avisar o fim
da fase por `postMessage`, dá para enviar automático — me chame que eu monto
essa parte.
