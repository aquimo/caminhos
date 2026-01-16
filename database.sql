-- Sistema de Gestão de Casas para Hospedagem
-- Base de Dados MySQL

CREATE DATABASE IF NOT EXISTS caminhos_hospedagem CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE caminhos_hospedagem;

-- Tabela de utilizadores
CREATE TABLE utilizadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('gestor_geral', 'secretaria', 'contabilidade', 'gestor_condominios') NOT NULL,
    ativo TINYINT(1) DEFAULT 1,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de localizações/condomínios
CREATE TABLE localizacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    codigo_postal VARCHAR(20) NOT NULL,
    pais VARCHAR(50) DEFAULT 'Moçambique',
    descricao TEXT,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de casas
CREATE TABLE casas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    localizacao_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    tipologia VARCHAR(50) NOT NULL, -- T1, T2, T3, etc.
    capacidade INT NOT NULL,
    area_decimal DECIMAL(8,2),
    preco_diario DECIMAL(10,2) NOT NULL,
    preco_semanal DECIMAL(10,2),
    preco_mensal DECIMAL(10,2),
    estado ENUM('disponivel', 'ocupado', 'manutencao', 'indisponivel') DEFAULT 'disponivel',
    comodidades JSON, -- WiFi, TV, Ar Condicionado, etc.
    imagens JSON, -- URLs das imagens
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (localizacao_id) REFERENCES localizacoes(id) ON DELETE RESTRICT
);

-- Tabela de clientes
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefone VARCHAR(20),
    nif VARCHAR(20),
    data_nascimento DATE,
    morada VARCHAR(255),
    codigo_postal VARCHAR(20),
    cidade VARCHAR(100),
    pais VARCHAR(50) DEFAULT 'Moçambique',
    documento_tipo ENUM('bi', 'passaporte', 'outro') NOT NULL,
    documento_numero VARCHAR(50) NOT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de reservas
CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    casa_id INT NOT NULL,
    cliente_id INT NOT NULL,
    data_checkin DATE NOT NULL,
    data_checkout DATE NOT NULL,
    numero_noites INT NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL,
    valor_pago DECIMAL(10,2) DEFAULT 0,
    estado ENUM('confirmada', 'checkin_realizado', 'checkout_realizado', 'cancelada') DEFAULT 'confirmada',
    observacoes TEXT,
    data_reserva DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_checkin_realizado DATETIME,
    data_checkout_realizado DATETIME,
    utilizador_checkin INT,
    utilizador_checkout INT,
    FOREIGN KEY (casa_id) REFERENCES casas(id) ON DELETE RESTRICT,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT,
    FOREIGN KEY (utilizador_checkin) REFERENCES utilizadores(id) ON DELETE SET NULL,
    FOREIGN KEY (utilizador_checkout) REFERENCES utilizadores(id) ON DELETE SET NULL
);

-- Tabela de pagamentos
CREATE TABLE pagamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    data_pagamento DATETIME DEFAULT CURRENT_TIMESTAMP,
    metodo_pagamento ENUM('dinheiro', 'mpesa', 'emola', 'mkesh', 'cartao', 'numerario', 'transferencia_bancaria', 'outro') NOT NULL,
    referencia VARCHAR(100),
    observacoes TEXT,
    utilizador_id INT NOT NULL,
    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE RESTRICT
);

-- Tabela de despesas
CREATE TABLE despesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    casa_id INT,
    descricao VARCHAR(255) NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    data_despesa DATE NOT NULL,
    categoria ENUM('limpeza', 'manutencao', 'utilidades', 'impostos', 'outro') NOT NULL,
    fornecedor VARCHAR(100),
    documento_fiscal VARCHAR(100),
    observacoes TEXT,
    utilizador_id INT NOT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (casa_id) REFERENCES casas(id) ON DELETE SET NULL,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE RESTRICT
);

-- Tabela de hóspedes
CREATE TABLE hospedes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    procedencia VARCHAR(100) NOT NULL,
    endereco TEXT NOT NULL,
    contacto VARCHAR(50) NOT NULL,
    previsao_permanencia VARCHAR(50) NOT NULL,
    data_checkin DATETIME NOT NULL,
    casa_id INT NOT NULL,
    senha VARCHAR(255) NOT NULL,
    numero_conta VARCHAR(50) NOT NULL,
    nome_conta VARCHAR(100) NOT NULL,
    valor_pagar DECIMAL(10,2) NOT NULL,
    valor_pago DECIMAL(10,2) DEFAULT 0,
    estado ENUM('ativo', 'checkout_realizado', 'cancelado') DEFAULT 'ativo',
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_checkout DATETIME,
    utilizador_checkin INT,
    utilizador_checkout INT,
    FOREIGN KEY (casa_id) REFERENCES casas(id) ON DELETE RESTRICT,
    FOREIGN KEY (utilizador_checkin) REFERENCES utilizadores(id) ON DELETE SET NULL,
    FOREIGN KEY (utilizador_checkout) REFERENCES utilizadores(id) ON DELETE SET NULL
);

-- Tabela de logs do sistema
CREATE TABLE logs_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT,
    acao VARCHAR(100) NOT NULL,
    tabela VARCHAR(50),
    registo_id INT,
    descricao TEXT,
    ip_address VARCHAR(45),
    data_acao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE SET NULL
);

-- Inserir utilizador administrador padrão
INSERT INTO utilizadores (nome, email, senha, perfil) VALUES 
('Administrador', 'admin@caminhos.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'gestor_geral');

-- Inserir localização de exemplo
INSERT INTO localizacoes (nome, endereco, cidade, codigo_postal, descricao) VALUES 
('Cidade', 'Avenida Principal, nº 123', 'Maputo', '1100', 'Localização na cidade com excelente acesso'),
('Tofo', 'Estrada Nacional, nº 456', 'Tofo', '2100', 'Localização em Tofo com vista para o mar');

-- Inserir casas de exemplo
INSERT INTO casas (codigo, localizacao_id, nome, descricao, tipologia, capacidade, area_decimal, preco_diario, preco_semanal, preco_mensal, estado, comodidades, imagens) VALUES 
('CASA001', 1, 'Apartamento T1 Cidade', 'Apartamento T1 com varanda e vista para a cidade', 'T1', 2, 45.50, 75.00, 450.00, 'disponivel', '["WiFi", "TV", "Ar Condicionado", "Máquina de Lavar Louça"]', '[]'),
('CASA002', 1, 'Apartamento T2 Cidade', 'Apartamento T2 espaçoso com 2 quartos', 'T2', 4, 65.00, 95.00, 570.00, 'disponivel', '["WiFi", "TV", "Ar Condicionado", "Máquina de Lavar Louça", "Máquina de Lavar Roupa"]', '[]'),
('CASA003', 2, 'Casa de Praia Tofo', 'Casa de 3 quartos com vista para o mar', 'T3', 6, 120.00, 180.00, 1080.00, 'disponivel', '["WiFi", "TV", "Ar Condicionado", "Cozinha Equipada", "Churrasqueiro", "Piscina"]', '[]');

-- Índices para melhor performance
CREATE INDEX idx_casas_localizacao ON casas(localizacao_id);
CREATE INDEX idx_casas_estado ON casas(estado);
CREATE INDEX idx_reservas_casa ON reservas(casa_id);
CREATE INDEX idx_reservas_cliente ON reservas(cliente_id);
CREATE INDEX idx_reservas_datas ON reservas(data_checkin, data_checkout);
CREATE INDEX idx_reservas_estado ON reservas(estado);
CREATE INDEX idx_pagamentos_reserva ON pagamentos(reserva_id);
CREATE INDEX idx_hospedes_casa ON hospedes(casa_id);
CREATE INDEX idx_hospedes_estado ON hospedes(estado);
CREATE INDEX idx_hospedes_checkin ON hospedes(data_checkin);
CREATE INDEX idx_despesas_casa ON despesas(casa_id);
CREATE INDEX idx_despesas_data ON despesas(data_despesa);
CREATE INDEX idx_logs_utilizador ON logs_sistema(utilizador_id);
CREATE INDEX idx_logs_data ON logs_sistema(data_acao);
