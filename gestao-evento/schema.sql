CREATE DATABASE IF NOT EXISTS gestao_evento DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestao_evento;

-- Tabela de Usuários (Acesso ao Sistema)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insere um usuário padrão (E-mail: admin@familia.com | Senha: admin)
INSERT INTO usuarios (nome, email, senha) 
VALUES ('Administrador', 'admin@familia.com', '$2y$10$8kS2G8QJ3nO4k1O3O/7fEuWvI.k6I4/84ZpI4e10bWkZ1kQ6fQ4KO');

-- Tabela de Participantes (Família)
CREATE TABLE participantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    patriarca VARCHAR(100) NOT NULL,
    valor_total DECIMAL(10,2) DEFAULT 352.00
);

-- Tabela de Lançamento de Parcelas
CREATE TABLE lancamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participante_id INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    data_pagamento DATE NOT NULL,
    FOREIGN KEY (participante_id) REFERENCES participantes(id) ON DELETE CASCADE
);