-- Script para atualizar localizações no banco de dados
-- Substitui "Cidade" por "Cidade de Inhambane" em todo o sistema

-- Atualizar localização existente (se houver)
UPDATE localizacoes 
SET nome = 'Cidade de Inhambane', 
    cidade = 'Inhambane',
    endereco = 'Avenida Samora Machel, nº 123',
    descricao = 'Localização na cidade de Inhambane com excelente acesso'
WHERE nome = 'Cidade' OR nome LIKE '%Cidade%';

-- Atualizar casas associadas
UPDATE casas 
SET nome = REPLACE(nome, 'Cidade', 'Cidade de Inhambane'),
    descricao = REPLACE(descricao, 'cidade', 'cidade de Inhambane')
WHERE nome LIKE '%Cidade%' OR descricao LIKE '%cidade%';

-- Atualizar hóspedes (procedência)
UPDATE hospedes 
SET procedencia = REPLACE(procedencia, 'Maputo', 'Inhambane'),
    endereco = REPLACE(endereco, 'Maputo', 'Inhambane')
WHERE procedencia LIKE '%Maputo%' OR endereco LIKE '%Maputo%';

-- Atualizar clientes (se houver)
UPDATE clientes 
SET cidade = REPLACE(cidade, 'Maputo', 'Inhambane'),
    morada = REPLACE(morada, 'Maputo', 'Inhambane')
WHERE cidade LIKE '%Maputo%' OR morada LIKE '%Maputo%';

-- Verificar resultados
SELECT 'Localizações atualizadas:' as info;
SELECT * FROM localizacoes;

SELECT 'Casas atualizadas:' as info;
SELECT * FROM casas;

SELECT 'Hóspedes atualizados:' as info;
SELECT * FROM hospedes;
