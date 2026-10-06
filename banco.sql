-- ============================================
-- JOGAÍ - Banco de dados MySQL
-- Rode este arquivo no phpMyAdmin (aba Importar) ou no terminal:
-- mysql -u root -p < banco.sql
-- ============================================

CREATE DATABASE IF NOT EXISTS jogai
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jogai;

-- Avatares que o usuário escolhe no cadastro
CREATE TABLE IF NOT EXISTS avatares (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  nome     VARCHAR(40)  NOT NULL,
  arquivo  VARCHAR(120) NOT NULL
) ENGINE=InnoDB;

-- Usuários
CREATE TABLE IF NOT EXISTS usuarios (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  usuario      VARCHAR(40)  NOT NULL UNIQUE,
  senha_hash   VARCHAR(255) NOT NULL,
  avatar_id    INT          NOT NULL DEFAULT 1,
  criado_em    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_usuario_avatar FOREIGN KEY (avatar_id) REFERENCES avatares(id)
) ENGINE=InnoDB;

-- Catálogo de jogos (pode ser preenchido pela API)
CREATE TABLE IF NOT EXISTS jogos (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  externo_id  VARCHAR(80)  NULL UNIQUE,   -- id do jogo na API
  titulo      VARCHAR(120) NOT NULL,
  categoria   VARCHAR(40)  NOT NULL,
  thumb       VARCHAR(255) NULL,
  url         VARCHAR(255) NULL           -- link/iframe do jogo
) ENGINE=InnoDB;

-- Favoritos
CREATE TABLE IF NOT EXISTS favoritos (
  usuario_id INT NOT NULL,
  jogo_id    INT NOT NULL,
  PRIMARY KEY (usuario_id, jogo_id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (jogo_id)    REFERENCES jogos(id)    ON DELETE CASCADE
) ENGINE=InnoDB;

-- Histórico: cada vez que a pessoa abre um jogo
CREATE TABLE IF NOT EXISTS historico (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  jogo_id    INT NOT NULL,
  jogado_em  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (jogo_id)    REFERENCES jogos(id)    ON DELETE CASCADE
) ENGINE=InnoDB;

-- Ranking: tempo (em segundos) que o jogador levou para terminar as fases
CREATE TABLE IF NOT EXISTS pontuacoes (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id     INT NOT NULL,
  jogo_id        INT NOT NULL,
  fases          INT NOT NULL,
  tempo_segundos INT NOT NULL,
  registrado_em  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (jogo_id)    REFERENCES jogos(id)    ON DELETE CASCADE,
  INDEX idx_ranking (jogo_id, fases DESC, tempo_segundos ASC)
) ENGINE=InnoDB;

-- ---------- Dados iniciais ----------
INSERT INTO avatares (id, nome, arquivo) VALUES
 (1,'Nyx','a1.jpg'),(2,'Luna','a2.jpg'),(3,'Sombra','a3.jpg'),
 (4,'Kitsune','a4.jpg'),(5,'Vertex','a5.jpg'),(6,'Aurora','a6.jpg'),
 (7,'Ghost','a7.jpg'),(8,'Pixie','a8.jpg'),(9,'Neo','a9.jpg'),
 (10,'Raven','a10.jpg'),(11,'Vega','a11.jpg'),(12,'Frost','a12.jpg'),
 (13,'Iris','a13.jpg'),(14,'Zero','a14.jpg'),(15,'Mika','a15.jpg')
ON DUPLICATE KEY UPDATE nome = VALUES(nome);

INSERT INTO jogos (titulo, categoria, url) VALUES
 ('Neon Runner','Corrida',NULL),
 ('Pixel Invaders','Ação',NULL),
 ('Cyber Puzzle','Puzzle',NULL),
 ('Street Kart','Corrida',NULL),
 ('Shadow Quest','Aventura',NULL),
 ('Arena Futebol','Esporte',NULL),
 ('Blocos Infinitos','Puzzle',NULL),
 ('Turbo Drift','Corrida',NULL),
 ('Dungeon Raid','Aventura',NULL),
 ('Basket Neon','Esporte',NULL),
 ('Laser Storm','Ação',NULL),
 ('Mind Grid','Puzzle',NULL);
