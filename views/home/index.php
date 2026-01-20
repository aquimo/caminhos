<!DOCTYPE html>
<html lang="pt-MZ">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bairro Ferroviário - Sistema de Gestão de Casas</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <style>
        .home-container {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        }
        
        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 0;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .logo img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }
        
        .nav-menu {
            display: flex;
            gap: 2rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .nav-menu a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 25px;
        }
        
        .nav-menu a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .hero-section {
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            background-image: url('<?= ASSETS_PATH ?>images/home/hero-background.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 20px;
            position: relative;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(11, 91, 54, 0.7);
            border-radius: 20px;
            z-index: 1;
        }
        
        .hero-content {
            color: white;
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            animation: fadeInLeft 1s ease;
            color: white;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            line-height: 1.6;
            animation: fadeInLeft 1s ease 0.2s both;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .hero-image {
            text-align: center;
            animation: fadeInRight 1s ease;
            position: relative;
            z-index: 2;
        }
        
        .hero-image img {
            max-width: 100%;
            height: auto;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .availability-section {
            background: white;
            padding: 3rem 2rem;
            margin: 2rem auto;
            max-width: 1200px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .section-title {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .availability-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            color: #333;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .form-group input,
        .form-group select {
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(11, 91, 54, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(11, 91, 54, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(11, 91, 54, 0.6);
        }
        
        .houses-section {
            padding: 4rem 2rem;
            background: #f8f9fa;
        }
        
        .houses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .house-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .house-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }
        
        .house-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .house-info {
            padding: 1.5rem;
        }
        
        .house-title {
            color: #333;
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .house-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .house-features {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .feature-tag {
            background: #e8f4fd;
            color: var(--primary-color);
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .contact-section {
            background: white;
            padding: 4rem 2rem;
        }
        
        .contact-content {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }
        
        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .contact-card {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 15px;
            border-left: 4px solid var(--primary-color);
        }
        
        .contact-card h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .contact-card p {
            color: #666;
            line-height: 1.6;
        }
        
        .login-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            padding: 1rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .login-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @media (max-width: 768px) {
            .hero-section {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .nav-menu {
                display: none;
            }
            
            .availability-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="home-container">
        <!-- Header -->
        <header class="header">
            <div class="logo-container">
                <div class="logo">
            <img src="<?= ASSETS_PATH ?>images/logo-bairro-ferroviario.png" alt="Bairro Ferroviário">
        </div>
                <nav>
                    <ul class="nav-menu">
                        <li><a href="#home">Início</a></li>
                        <li><a href="#services">Serviços</a></li>
                        <li><a href="#houses">Nossas Casas</a></li>
                        <li><a href="#contact">Contactos</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section id="home" class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">Bem Vindos ao Bairro Ferroviário</h1>
                <p class="hero-subtitle">
                    Oferecemos casas confortáveis e seguras para a sua estadia em Inhambane. 
                    Com localização privilegiada e infraestrutura moderna, garantimos o seu conforto 
                    e bem-estar durante toda a sua permanência.
                </p>
            </div>
            <div class="hero-image">
                <img src="<?= ASSETS_PATH ?>images/hero-image.jpg" alt="Bairro Ferroviário" onerror="this.style.display='none'">
            </div>
        </section>

        <!-- Availability Section -->
        <section class="availability-section">
            <h2 class="section-title">Verificar Disponibilidade</h2>
            <form class="availability-form" method="GET" action="index.php">
                <input type="hidden" name="route" value="disponibilidade">
                
                <div class="form-group">
                    <label for="localizacao">Localização</label>
                    <select id="localizacao" name="localizacao" required>
                        <option value="">Selecione...</option>
                        <?php if (isset($localizacoes) && !empty($localizacoes)): ?>
                            <?php foreach ($localizacoes as $localizacao): ?>
                                <option value="<?= htmlspecialchars($localizacao['id']) ?>">
                                    <?= htmlspecialchars($localizacao['nome']) ?> - <?= htmlspecialchars($localizacao['cidade']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="1">Cidade de Inhambane</option>
                            <option value="2">Tofo</option>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="data_checkin">Data de Check-in</label>
                    <input type="date" id="data_checkin" name="data_checkin" required>
                </div>
                
                <div class="form-group">
                    <label for="data_checkout">Data de Check-out</label>
                    <input type="date" id="data_checkout" name="data_checkout" required>
                </div>
                
                <div class="form-group">
                    <label for="tipo_casa">Tipo de Casa</label>
                    <select id="tipo_casa" name="tipo_casa" required>
                        <option value="">Selecione...</option>
                        <option value="T0">T0 (Quarto Individual)</option>
                        <option value="T1">T1 (Um Quarto)</option>
                        <option value="T2">T2 (Dois Quartos)</option>
                        <option value="T3">T3 (Três Quartos)</option>
                        <option value="T4">T4 (Quatro Quartos)</option>
                    </select>
                </div>
                
                <div class="form-group" style="align-self: flex-end;">
                    <button type="submit" class="btn-primary">Verificar Disponibilidade</button>
                </div>
            </form>
        </section>

        <!-- Houses Section -->
        <section id="houses" class="houses-section">
            <h2 class="section-title">Nossas Casas</h2>
            <div class="houses-grid">
                <?php if (isset($casas_disponiveis) && !empty($casas_disponiveis)): ?>
                    <?php foreach ($casas_disponiveis as $casa): ?>
                        <div class="house-card">
                            <div class="house-image"><?= htmlspecialchars($casa['tipologia']) ?></div>
                            <div class="house-info">
                                <h3 class="house-title">Apartamento <?= htmlspecialchars($casa['tipologia']) ?></h3>
                                <p class="house-description">
                                    <?php
                                    $descricoes = [
                                        'T0' => 'Perfeito para estudantes e profissionais que buscam conforto e privacidade.',
                                        'T1' => 'Ideal para casais ou profissionais que necessitam de mais espaço.',
                                        'T2' => 'Espaçoso e confortável, perfeito para pequenas famílias.',
                                        'T3' => 'Amplo e bem distribuído, ideal para famílias maiores.',
                                        'T4' => 'Muito espaçoso, ideal para famílias grandes ou grupos.'
                                    ];
                                    echo $descricoes[$casa['tipologia']] ?? 'Apartamento confortável e bem equipado.';
                                    ?>
                                </p>
                                <div class="house-features">
                                    <span class="feature-tag"><?= htmlspecialchars($casa['quantidade']) ?> Disponíveis</span>
                                    <?php if ($casa['preco_medio']): ?>
                                        <span class="feature-tag">MZN <?= number_format($casa['preco_medio'], 2) ?>/dia</span>
                                    <?php endif; ?>
                                    <span class="feature-tag">Wi-Fi</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Cards padrão caso não haja dados -->
                    <div class="house-card">
                        <div class="house-image">T0</div>
                        <div class="house-info">
                            <h3 class="house-title">Quarto Individual</h3>
                            <p class="house-description">
                                Perfeito para estudantes e profissionais que buscam conforto e privacidade.
                            </p>
                            <div class="house-features">
                                <span class="feature-tag">1 Cama</span>
                                <span class="feature-tag">Casa de Banho</span>
                                <span class="feature-tag">Wi-Fi</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="house-card">
                        <div class="house-image">T1</div>
                        <div class="house-info">
                            <h3 class="house-title">Apartamento T1</h3>
                            <p class="house-description">
                                Ideal para casais ou profissionais que necessitam de mais espaço.
                            </p>
                            <div class="house-features">
                                <span class="feature-tag">1 Quarto</span>
                                <span class="feature-tag">Sala</span>
                                <span class="feature-tag">Cozinha</span>
                                <span class="feature-tag">Wi-Fi</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="house-card">
                        <div class="house-image">T2</div>
                        <div class="house-info">
                            <h3 class="house-title">Apartamento T2</h3>
                            <p class="house-description">
                                Espaçoso e confortável, perfeito para pequenas famílias.
                            </p>
                            <div class="house-features">
                                <span class="feature-tag">2 Quartos</span>
                                <span class="feature-tag">Sala</span>
                                <span class="feature-tag">Cozinha</span>
                                <span class="feature-tag">Wi-Fi</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="house-card">
                        <div class="house-image">T3</div>
                        <div class="house-info">
                            <h3 class="house-title">Apartamento T3</h3>
                            <p class="house-description">
                                Amplo e bem distribuído, ideal para famílias maiores.
                            </p>
                            <div class="house-features">
                                <span class="feature-tag">3 Quartos</span>
                                <span class="feature-tag">Sala</span>
                                <span class="feature-tag">Cozinha</span>
                                <span class="feature-tag">Wi-Fi</span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="contact-section">
            <div class="contact-content">
                <h2 class="section-title">Contactos</h2>
                <div class="contact-info">
                    <div class="contact-card">
                        <h3>📍 Sede Principal</h3>
                        <p>
                            <strong>Cidade de Inhambane</strong><br>
                            Av. Samora Machel, Praça dos Trabalhadores<br>
                            Inhambane, Bairro Balane-2, Casa nº 49<br>
                            <strong>Tel.:</strong> (+258) 29 320 453<br>
                            <strong>Fax:</strong> (+258) 29 320 822
                        </p>
                    </div>
                    
                    <div class="contact-card">
                        <h3>📞 Contactos Gerais</h3>
                        <p>
                            <strong>CFM (Maputo)</strong><br>
                            <strong>Tel.:</strong> (+258) 82 544 8100<br>
                            <strong>Tel.:</strong> (+258) 84 237 0323<br>
                            <strong>Email:</strong> gci@cfm.co.mz
                        </p>
                    </div>
                    
                    <div class="contact-card">
                        <h3>🕐 Horário de Funcionamento</h3>
                        <p>
                            <strong>Segunda a Sexta:</strong> 08:00 - 18:00<br>
                            <strong>Sábado:</strong> 08:00 - 13:00<br>
                            <strong>Domingo:</strong> Encerrado<br>
                            <strong>Emergências:</strong> 24/7
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Login Button -->
        <button class="login-btn" onclick="window.location.href='index.php?route=login'">
            Área Administrativa
        </button>
    </div>

    <script>
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('data_checkin').setAttribute('min', today);
        document.getElementById('data_checkout').setAttribute('min', today);
        
        // Update checkout minimum date when checkin changes
        document.getElementById('data_checkin').addEventListener('change', function() {
            document.getElementById('data_checkout').setAttribute('min', this.value);
        });
        
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
