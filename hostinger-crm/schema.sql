-- EdusIA Painel Comercial — schema MySQL para Hostinger (hospedagem compartilhada)
-- Importe este arquivo em hPanel > Bancos de dados > phpMyAdmin, na base já criada.

CREATE TABLE IF NOT EXISTS contacts (
  id VARCHAR(40) PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  empresa VARCHAR(255) DEFAULT '',
  cargo VARCHAR(255) DEFAULT '',
  telefone VARCHAR(60) NOT NULL,
  email VARCHAR(255) DEFAULT '',
  origem VARCHAR(60) NOT NULL,
  disc VARCHAR(10) DEFAULT '',
  vendedor VARCHAR(120) NOT NULL,
  tags TEXT,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS sellers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO sellers (nome) VALUES
  ('Eduardo Mendes'),
  ('Marcelly Lemos'),
  ('Eduardo Carvalho');

CREATE TABLE IF NOT EXISTS interactions (
  id VARCHAR(40) PRIMARY KEY,
  contact_id VARCHAR(40) NOT NULL,
  data_hora DATETIME NOT NULL,
  canal VARCHAR(60) NOT NULL,
  resumo TEXT NOT NULL,
  temperatura VARCHAR(20) NOT NULL,
  proxima_acao VARCHAR(255) DEFAULT '',
  data_followup DATE NULL,
  pilar VARCHAR(60) NOT NULL,
  produto VARCHAR(255) NOT NULL,
  valor DECIMAL(12,2) DEFAULT 0,
  estagio VARCHAR(30) NOT NULL,
  vendedor_registro VARCHAR(120) DEFAULT '',
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_interactions_contact FOREIGN KEY (contact_id)
    REFERENCES contacts(id) ON DELETE CASCADE,
  INDEX idx_interactions_contact (contact_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
