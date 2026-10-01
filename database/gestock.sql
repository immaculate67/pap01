CREATE DATABASE IF NOT EXISTS gestock CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestock;

CREATE TABLE utilizadores (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE clientes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  contacto VARCHAR(40) DEFAULT NULL,
  email VARCHAR(160) DEFAULT NULL,
  notas TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_clientes_nome (nome)
) ENGINE=InnoDB;

CREATE TABLE produtos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  descricao VARCHAR(255) DEFAULT NULL,
  preco DECIMAL(10,2) NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_produtos_nome (nome),
  INDEX idx_produtos_stock (stock)
) ENGINE=InnoDB;

CREATE TABLE vendas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT UNSIGNED DEFAULT NULL,
  data_venda TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  CONSTRAINT fk_vendas_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE itens_venda (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  venda_id INT UNSIGNED NOT NULL,
  produto_id INT UNSIGNED NOT NULL,
  quantidade INT NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_itens_venda FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE CASCADE,
  CONSTRAINT fk_itens_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO utilizadores (nome, username, password_hash) VALUES
('Lakshit Battan', 'admin', '$2y$12$dP/HTXkguo8wFhb1ngSzoO1.GcVSxNjHXtTBsB8ZfwHX9oRkD7qou');

INSERT INTO clientes (nome, contacto, email) VALUES
('João Silva','912 000 001','joao@example.com'),
('Marta Costa','913 000 002','marta@example.com'),
('Rui Martins','914 000 003','rui@example.com');

INSERT INTO produtos (nome, descricao, preco, stock) VALUES
('Produto A','Produto de demonstração',12.50,42),
('Produto B','Produto de demonstração',24.90,7),
('Produto C','Produto de demonstração',8.75,18),
('Produto D','Produto de demonstração',39.90,3),
('Produto E','Produto de demonstração',15.00,25);

INSERT INTO vendas (cliente_id,total) VALUES (1,240.00),(2,85.00),(3,120.00);
INSERT INTO itens_venda (venda_id,produto_id,quantidade,preco_unitario,subtotal) VALUES
(1,1,10,12.50,125.00),(1,3,5,8.75,43.75),(1,5,4,15.00,60.00),(1,2,1,11.25,11.25),
(2,2,2,24.90,49.80),(2,3,4,8.80,35.20),
(3,4,3,39.90,119.70);
