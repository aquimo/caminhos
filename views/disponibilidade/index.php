<!DOCTYPE html>
<html lang="pt-MZ">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casas Disponíveis - Bairro Ferroviário</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .header h1 {
            color: var(--primary-color);
            margin: 0;
            font-size: 2.5rem;
        }
        
        .header p {
            color: #666;
            margin: 10px 0 0 0;
            font-size: 1.1rem;
        }
        
        .search-form {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid var(--primary-color);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        
        .form-group select,
        .form-group input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(11, 91, 54, 0.1);
        }
        
        .btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: var(--primary-light);
            transform: translateY(-1px);
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        
        .houses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .house-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e0e0e0;
        }
        
        .house-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        }
        
        .house-image {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: bold;
        }
        
        .house-content {
            padding: 20px;
        }
        
        .house-title {
            margin: 0 0 10px 0;
            color: var(--primary-color);
            font-size: 1.3rem;
        }
        
        .house-description {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .house-features {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .feature-tag {
            background: #e9ecef;
            color: #495057;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 0.8rem;
        }
        
        .house-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .house-price small {
            font-size: 0.9rem;
            color: #666;
            font-weight: normal;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: color 0.3s ease;
            padding: 10px 15px;
            border: 2px solid var(--primary-color);
            border-radius: 5px;
            background: transparent;
        }
        
        .back-link:hover {
            color: var(--primary-light);
            background: var(--primary-color);
            color: white;
        }
        
        .back-link i {
            margin-right: 8px;
        }
        
        .floating-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .floating-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        .floating-btn:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        .floating-btn.secondary {
            background: #6c757d;
        }
        
        .floating-btn.secondary:hover {
            background: #5a6268;
        }
        
        .no-results {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .no-results i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 10px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .houses-grid {
                grid-template-columns: 1fr;
            }
            
            .floating-buttons {
                top: 10px;
                right: 10px;
            }
            
            .floating-btn {
                padding: 10px 15px;
                font-size: 0.8rem;
            }
            
            .floating-btn i {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🏠 Casas Disponíveis</h1>
            <p>Encontre a casa perfeita para a sua estadia em Inhambane</p>
        </div>
        
        <!-- Link para voltar -->
        <a href="<?= UrlHelper::base() ?>" class="back-link">
            <i>←</i> Voltar para a Página Inicial
        </a>
        
        <!-- Formulário de Busca -->
        <div class="search-form">
            <form method="GET" action="<?= UrlHelper::base('disponibilidade') ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label for="localizacao">Localização</label>
                        <select id="localizacao" name="localizacao">
                            <option value="">Todas as Localizações</option>
                            <?php foreach ($localizacoes as $localizacao): ?>
                                <option value="<?= htmlspecialchars($localizacao['id']) ?>" 
                                    <?= (isset($_GET['localizacao']) && $_GET['localizacao'] == $localizacao['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($localizacao['nome']) ?> - <?= htmlspecialchars($localizacao['cidade']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="data_checkin">Data de Check-in</label>
                        <input type="date" id="data_checkin" name="data_checkin" 
                               value="<?= htmlspecialchars($_GET['data_checkin'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="data_checkout">Data de Check-out</label>
                        <input type="date" id="data_checkout" name="data_checkout" 
                               value="<?= htmlspecialchars($_GET['data_checkout'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="tipo_casa">Tipo de Casa</label>
                        <select id="tipo_casa" name="tipo_casa">
                            <option value="">Todos os Tipos</option>
                            <option value="T0" <?= (isset($_GET['tipo_casa']) && $_GET['tipo_casa'] == 'T0') ? 'selected' : '' ?>>T0 (Quarto Individual)</option>
                            <option value="T1" <?= (isset($_GET['tipo_casa']) && $_GET['tipo_casa'] == 'T1') ? 'selected' : '' ?>>T1 (Um Quarto)</option>
                            <option value="T2" <?= (isset($_GET['tipo_casa']) && $_GET['tipo_casa'] == 'T2') ? 'selected' : '' ?>>T2 (Dois Quartos)</option>
                            <option value="T3" <?= (isset($_GET['tipo_casa']) && $_GET['tipo_casa'] == 'T3') ? 'selected' : '' ?>>T3 (Três Quartos)</option>
                            <option value="T4" <?= (isset($_GET['tipo_casa']) && $_GET['tipo_casa'] == 'T4') ? 'selected' : '' ?>>T4 (Quatro Quartos)</option>
                        </select>
                    </div>
                </div>
                
                <div style="text-align: center;">
                    <button type="submit" class="btn">🔍 Buscar Casas Disponíveis</button>
                </div>
            </form>
        </div>
        
        <!-- Mensagens de Erro ou Sucesso -->
        <?php if ($erro): ?>
            <div class="error">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['data_checkin']) && isset($_GET['data_checkout']) && !$erro && !empty($casas_disponiveis)): ?>
            <div class="success">
                🎉 Encontramos <?= count($casas_disponiveis) ?> casa(s) disponível(is) para as suas datas!
            </div>
        <?php endif; ?>
        
        <!-- Lista de Casas Disponíveis -->
        <?php if (!empty($casas_disponiveis)): ?>
            <div class="houses-grid">
                <?php foreach ($casas_disponiveis as $casa): ?>
                    <div class="house-card">
                        <div class="house-image" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: bold; height: 200px;">
                            <?= htmlspecialchars($casa['tipologia']) ?>
                        </div>
                        <div class="house-content">
                            <h3 class="house-title"><?= htmlspecialchars($casa['nome']) ?></h3>
                            <p class="house-description">
                                <?= htmlspecialchars($casa['descricao']) ?>
                            </p>
                            
                            <div class="house-features">
                                <span class="feature-tag">👥 <?= $casa['capacidade'] ?> pessoa(s)</span>
                                <span class="feature-tag">📐 <?= $casa['area_decimal'] ?> m²</span>
                                <span class="feature-tag">📍 <?= htmlspecialchars($casa['localizacao_nome']) ?></span>
                            </div>
                            
                            <div class="house-price">
                                <?= formatCurrency($casa['preco_diario']) ?>
                                <small>/dia</small>
                            </div>
                            
                            <?php if ($casa['preco_semanal']): ?>
                                <div style="font-size: 0.9rem; color: #666; margin-bottom: 10px;">
                                    Semanal: <?= formatCurrency($casa['preco_semanal']) ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($casa['preco_mensal']): ?>
                                <div style="font-size: 0.9rem; color: #666; margin-bottom: 15px;">
                                    Mensal: <?= formatCurrency($casa['preco_mensal']) ?>
                                </div>
                            <?php endif; ?>
                            
                            <div style="text-align: center;">
                                <a href="<?= UrlHelper::base() ?>?route=reservas/criar&casa_id=<?= $casa['id'] ?>&data_checkin=<?= urlencode($_GET['data_checkin']) ?>&data_checkout=<?= urlencode($_GET['data_checkout']) ?>" 
                                   class="btn" style="text-decoration: none; display: inline-block;">
                                    📅 Fazer Reserva
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif (isset($_GET['data_checkin']) && isset($_GET['data_checkout']) && !$erro): ?>
            <div class="no-results">
                <div style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;">🔍</div>
                <h3>Nenhuma casa encontrada</h3>
                <p>Não encontramos casas disponíveis para as datas e critérios selecionados.</p>
                <p>Tente:</p>
                <ul style="text-align: left; display: inline-block;">
                    <li>Alterar as datas</li>
                    <li>Escolher outra localização</li>
                    <li>Selecionar outro tipo de casa</li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Botões Flutuantes de Navegação -->
    <div class="floating-buttons">
        <a href="<?= UrlHelper::base() ?>" class="floating-btn">
            <i>🏠</i> Página Inicial
        </a>
        <a href="<?= UrlHelper::base() ?>?route=reservas" class="floating-btn secondary">
            <i>📋</i> Minhas Reservas
        </a>
    </div>
    
    <script>
        // Definir data mínima como hoje
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('data_checkin').setAttribute('min', today);
        document.getElementById('data_checkout').setAttribute('min', today);
        
        // Atualizar data mínima de checkout quando check-in mudar
        document.getElementById('data_checkin').addEventListener('change', function() {
            const checkinDate = new Date(this.value);
            const minCheckout = new Date(checkinDate);
            minCheckout.setDate(minCheckout.getDate() + 1);
            document.getElementById('data_checkout').setAttribute('min', minCheckout.toISOString().split('T')[0]);
        });
    </script>
</body>
</html>
