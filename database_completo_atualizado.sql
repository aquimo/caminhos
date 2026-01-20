-- Sistema de Gestão de Casas para Hospedagem
-- Base de Dados MySQL - Versão Completa Atualizada
-- Autor: Oscar Massangaia - Universidade Aberta ISCED
-- Data: 20/01/2026

-- Limpar banco existente (para nova instalação limpa)
DROP DATABASE IF EXISTS caminhos_hospedagem;
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
('Administrador', 'admin@caminhos.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'gestor_geral'),
('Secretária', 'secretaria@caminhos.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'secretaria'),
('Contabilidade', 'financeiro@caminhos.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'contabilidade');

-- Inserir localizações atualizadas (apenas Cidade de Inhambane e Tofo)
INSERT INTO localizacoes (nome, endereco, cidade, codigo_postal, descricao) VALUES 
('Cidade de Inhambane', 'Avenida Samora Machel, nº 123', 'Inhambane', '1100', 'Localização na cidade de Inhambane com excelente acesso'),
('Tofo', 'Estrada Nacional, nº 456', 'Tofo', '2100', 'Localização em Tofo com vista para o mar');

-- Inserir casas atualizadas (apenas Cidade de Inhambane e Tofo) - Valores em MZN
INSERT INTO casas (codigo, localizacao_id, nome, descricao, tipologia, capacidade, area_decimal, preco_diario, preco_semanal, preco_mensal, estado, comodidades, imagens) VALUES 
('CASA001', 1, 'Apartamento T1 Cidade de Inhambane', 'Apartamento T1 com varanda e vista para a cidade de Inhambane', 'T1', 2, 45.50, 2500.00, 15000.00, 45000.00, 'disponivel', '["WiFi", "TV", "Ar Condicionado", "Máquina de Lavar Louça"]', '[]'),
('CASA002', 1, 'Apartamento T2 Cidade de Inhambane', 'Apartamento T2 espaçoso com 2 quartos na cidade de Inhambane', 'T2', 4, 65.00, 3500.00, 21000.00, 63000.00, 'disponivel', '["WiFi", "TV", "Ar Condicionado", "Máquina de Lavar Louça", "Máquina de Lavar Roupa"]', '[]'),
('CASA003', 1, 'Apartamento T3 Cidade de Inhambane', 'Apartamento T3 familiar na cidade de Inhambane', 'T3', 6, 85.00, 4500.00, 27000.00, 81000.00, 'disponivel', '["WiFi", "TV", "Ar Condicionado", "Máquina de Lavar Louça", "Máquina de Lavar Roupa", "Frigorífico"]', '[]'),
('CASA004', 2, 'Casa de Praia Tofo', 'Casa de 3 quartos com vista para o mar em Tofo', 'T3', 6, 120.00, 6500.00, 39000.00, 117000.00, 'disponivel', '["WiFi", "TV", "Ar Condicionado", "Cozinha Equipada", "Churrasqueiro", "Piscina"]', '[]'),
('CASA005', 2, 'Apartamento T2 Tofo', 'Apartamento T2 em Tofo perto da praia', 'T2', 4, 55.00, 4000.00, 24000.00, 72000.00, 'disponivel', '["WiFi", "TV", "Ar Condicionado", "Varanda"]', '[]'),
('CASA006', 1, 'Apartamento T0 Cidade de Inhambane', 'Quarto individual na cidade de Inhambane', 'T0', 1, 25.00, 1500.00, 9000.00, 27000.00, 'disponivel', '["WiFi", "TV", "Ar Condicionado"]', '[]'),
('CASA007', 2, 'Apartamento T1 Tofo', 'Apartamento T1 em Tofo com vista para o mar', 'T1', 2, 40.00, 2000.00, 12000.00, 36000.00, 'disponivel', '["WiFi", "TV", "Ar Condicionado", "Varanda"]', '[]'),
('CASA008', 1, 'Apartamento T4 Cidade de Inhambane', 'Apartamento T4 espaçoso para famílias grandes na cidade de Inhambane', 'T4', 8, 110.00, 5500.00, 33000.00, 99000.00, 'disponivel', '["WiFi", "TV", "Ar Condicionado", "Máquina de Lavar Roupa", "Frigorífico", "Cozinha Equipada"]', '[]');

-- Inserir clientes de exemplo (apenas Cidade de Inhambane e Tofo)
INSERT INTO clientes (nome, email, telefone, nif, data_nascimento, morada, codigo_postal, cidade, pais, documento_tipo, documento_numero) VALUES 
('João Silva', 'joao.silva@email.com', '+258841234567', '123456789', '1990-05-15', 'Avenida Eduardo Mondlane, nº 456', '1100', 'Inhambane', 'Moçambique', 'bi', '123456789A'),
('Maria Santos', 'maria.santos@email.com', '+258847654321', '987654321', '1985-08-22', 'Rua da República, nº 789', '2100', 'Tofo', 'Moçambique', 'bi', '987654321B'),
('Pedro Nhamptave', 'pedro.n@email.com', '+258842345678', '456789123', '1992-12-10', 'Avenida Samora Machel, nº 123', '1100', 'Inhambane', 'Moçambique', 'bi', '456789123C');

-- Inserir algumas reservas de exemplo - Valores em MZN
INSERT INTO reservas (casa_id, cliente_id, data_checkin, data_checkout, numero_noites, valor_total, valor_pago, estado, observacoes) VALUES 
(1, 1, '2026-01-25', '2026-01-28', 3, 7500.00, 7500.00, 'confirmada', 'Reserva para fim de semana'),
(4, 2, '2026-02-01', '2026-02-07', 6, 39000.00, 19500.00, 'confirmada', 'Férias de verão'),
(6, 3, '2026-01-20', '2026-01-23', 3, 4500.00, 0.00, 'confirmada', 'Viagem de negócios');

-- Inserir pagamentos de exemplo - Valores em MZN
INSERT INTO pagamentos (reserva_id, valor, metodo_pagamento, referencia, utilizador_id) VALUES 
(1, 7500.00, 'transferencia_bancaria', 'TRF001', 2),
(2, 19500.00, 'mpesa', 'MPS002', 2);

-- Inserir hóspedes de exemplo
INSERT INTO hospedes (nome, procedencia, endereco, contacto, previsao_permanencia, data_checkin, casa_id, senha, numero_conta, nome_conta, valor_pagar, valor_pago, utilizador_checkin) VALUES 
('António Manhiça', 'Maputo', 'Avenida Julius Nyerere, nº 1000', '+258841112223', '7 dias', '2026-01-15 10:00:00', 2, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456', 'António Manhiça', 1050.00, 1050.00, 2),
('Isabel Chissano', 'Beira', 'Rua da Praia, nº 500', '+258823344556', '5 dias', '2026-01-18 14:30:00', 5, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '234567', 'Isabel Chissano', 750.00, 0.00, 2);

-- Inserir algumas despesas de exemplo
INSERT INTO despesas (casa_id, descricao, valor, data_despesa, categoria, fornecedor, documento_fiscal, utilizador_id) VALUES 
(1, 'Limpeza mensal', 50.00, '2026-01-15', 'limpeza', 'Serviços de Limpeza Lda', 'FAT001', 3),
(4, 'Reparação ar condicionado', 150.00, '2026-01-10', 'manutencao', 'Ar Frio Lda', 'FAT002', 3),
(2, 'Conta de electricidade', 200.00, '2026-01-05', 'utilidades', 'EDM', 'FAT003', 3);

-- Índices para melhor performance
CREATE INDEX idx_casas_localizacao ON casas(localizacao_id);
CREATE INDEX idx_casas_estado ON casas(estado);
CREATE INDEX idx_casas_tipologia ON casas(tipologia);
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
CREATE INDEX idx_despesas_categoria ON despesas(categoria);
CREATE INDEX idx_logs_utilizador ON logs_sistema(utilizador_id);
CREATE INDEX idx_logs_data ON logs_sistema(data_acao);
CREATE INDEX idx_localizacoes_cidade ON localizacoes(cidade);
CREATE INDEX idx_localizacoes_nome ON localizacoes(nome);

-- Views para consultas frequentes
CREATE VIEW vw_casas_detalhadas AS
SELECT 
    c.id, c.codigo, c.nome, c.descricao, c.tipologia, c.capacidade, 
    c.area_decimal, c.preco_diario, c.preco_semanal, c.preco_mensal, c.estado,
    l.nome as localizacao_nome, l.cidade, l.endereco as localizacao_endereco,
    c.comodidades, c.imagens, c.data_criacao
FROM casas c
JOIN localizacoes l ON c.localizacao_id = l.id;

CREATE VIEW vw_reservas_detalhadas AS
SELECT 
    r.id, r.data_checkin, r.data_checkout, r.numero_noites, 
    r.valor_total, r.valor_pago, r.estado, r.observacoes,
    c.codigo as casa_codigo, c.nome as casa_nome, c.tipologia,
    cl.nome as cliente_nome, cl.email as cliente_email,
    l.nome as localizacao_nome, l.cidade
FROM reservas r
JOIN casas c ON r.casa_id = c.id
JOIN clientes cl ON r.cliente_id = cl.id
JOIN localizacoes l ON c.localizacao_id = l.id;

CREATE VIEW vw_hospedes_detalhados AS
SELECT 
    h.id, h.nome, h.procedencia, h.contacto, h.previsao_permanencia,
    h.data_checkin, h.data_checkout, h.estado,
    h.valor_pagar, h.valor_pago, h.numero_conta,
    c.codigo as casa_codigo, c.nome as casa_nome, c.tipologia,
    l.nome as localizacao_nome, l.cidade,
    uc.nome as utilizador_checkin_nome,
    uco.nome as utilizador_checkout_nome
FROM hospedes h
JOIN casas c ON h.casa_id = c.id
JOIN localizacoes l ON c.localizacao_id = l.id
LEFT JOIN utilizadores uc ON h.utilizador_checkin = uc.id
LEFT JOIN utilizadores uco ON h.utilizador_checkout = uco.id;

-- Procedimentos armazenados úteis
DELIMITER //

CREATE PROCEDURE sp_verificar_disponibilidade(
    IN p_data_checkin DATE,
    IN p_data_checkout DATE,
    IN p_localizacao_id INT
)
BEGIN
    SELECT 
        c.id, c.codigo, c.nome, c.tipologia, c.capacidade, 
        c.preco_diario, c.preco_semanal, c.preco_mensal,
        l.nome as localizacao_nome, l.cidade
    FROM casas c
    JOIN localizacoes l ON c.localizacao_id = l.id
    WHERE c.estado = 'disponivel'
    AND (p_localizacao_id IS NULL OR c.localizacao_id = p_localizacao_id)
    AND c.id NOT IN (
        SELECT DISTINCT casa_id 
        FROM reservas 
        WHERE estado IN ('confirmada', 'checkin_realizado')
        AND (
            (p_data_checkin BETWEEN data_checkin AND DATE_SUB(data_checkout, INTERVAL 1 DAY))
            OR (p_data_checkout BETWEEN DATE_ADD(data_checkin, INTERVAL 1 DAY) AND data_checkout)
            OR (p_data_checkin <= data_checkin AND p_data_checkout >= data_checkout)
        )
    )
    ORDER BY c.tipologia, c.preco_diario;
END //

CREATE PROCEDURE sp_estatisticas_ocupacao(IN p_ano INT)
BEGIN
    SELECT 
        l.nome as localizacao,
        COUNT(c.id) as total_casas,
        SUM(CASE WHEN r.estado = 'checkin_realizado' THEN 1 ELSE 0 END) as casas_ocupadas,
        ROUND(
            SUM(CASE WHEN r.estado = 'checkin_realizado' THEN 1 ELSE 0 END) * 100.0 / 
            NULLIF(COUNT(c.id), 0), 
            2
        ) as taxa_ocupacao,
        SUM(CASE WHEN r.estado = 'checkin_realizado' THEN r.valor_total ELSE 0 END) as receita_total
    FROM localizacoes l
    LEFT JOIN casas c ON l.id = c.localizacao_id
    LEFT JOIN reservas r ON c.id = r.casa_id 
        AND r.estado IN ('checkin_realizado')
        AND YEAR(r.data_checkin) = p_ano
    GROUP BY l.id, l.nome
    ORDER BY l.nome;
END //

DELIMITER ;

-- Inserir logs iniciais
INSERT INTO logs_sistema (utilizador_id, acao, tabela, registo_id, descricao, ip_address) VALUES 
(1, 'INSERT', 'utilizadores', 1, 'Criação do utilizador administrador', '127.0.0.1'),
(1, 'INSERT', 'localizacoes', 1, 'Criação da localização Cidade de Inhambane', '127.0.0.1'),
(1, 'INSERT', 'casas', 1, 'Criação da casa CASA001', '127.0.0.1');

-- Configurações finais
SET FOREIGN_KEY_CHECKS = 0;
SET FOREIGN_KEY_CHECKS = 1;

-- Mensagem de conclusão
SELECT 'Banco de dados caminhos_hospedagem criado com sucesso!' as mensagem;
SELECT 'Localizações: Cidade de Inhambane, Tofo' as localizacoes;
SELECT 'Total de casas: 8' as total_casas;
SELECT 'Total de clientes: 3' as total_clientes;
SELECT 'Total de reservas: 3' as total_reservas;
SELECT 'Total de hóspedes: 2' as total_hospedes;
