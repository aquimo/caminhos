<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Bairro Ferroviário - Sistema de Gestão</title>
    <link rel="stylesheet" href="<?php echo UrlHelper::asset('css/style.css'); ?>">
    <link rel="icon" type="image/x-icon" href="<?php echo UrlHelper::asset('images/favicon.ico'); ?>">
</head>
<body>
    <?php if (AuthHelper::isLoggedIn()): ?>
    <div class="admin-container">
        <!-- Menu Lateral -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1>Bairro Ferroviário</h1>
                <p>Gestão de Hospedagem</p>
                <small style="color: rgba(255,255,255,0.7); font-size: 0.7rem;">Desenvolvido por: Oscar Massangaia</small>
            </div>
            
            <nav class="sidebar-menu">
                <?php if (AuthHelper::hasProfile('gestor_geral')): ?>
                <div class="menu-subtitle">Geral</div>
                <a href="<?php echo UrlHelper::base('dashboard'); ?>" class="menu-item <?php echo UrlHelper::isCurrent('dashboard') ? 'active' : ''; ?>">
                    <i>📊</i> Dashboard
                </a>
                <a href="<?php echo UrlHelper::base('utilizadores'); ?>" class="menu-item <?php echo UrlHelper::isCurrent('utilizadores') ? 'active' : ''; ?>">
                    <i>👥</i> Utilizadores
                </a>
                <?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('secretaria')): ?>
                <div class="menu-subtitle">Hospedagem</div>
                <a href="<?php echo UrlHelper::base('hospedes'); ?>" class="menu-item <?php echo UrlHelper::isCurrent('hospedes') ? 'active' : ''; ?>">
                    <i>👥</i> Hóspedes
                </a>
                <a href="<?php echo UrlHelper::base('reservas'); ?>" class="menu-item <?php echo UrlHelper::isCurrent('reservas') ? 'active' : ''; ?>">
                    <i>📅</i> Reservas
                </a>
                <?php endif; ?>
                <?php endif; ?>
                
                <?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('gestor_condominios')): ?>
                <div class="menu-subtitle">Gestão</div>
                <a href="<?php echo UrlHelper::base('casas'); ?>" class="menu-item <?php echo UrlHelper::isCurrent('casas') ? 'active' : ''; ?>">
                    <i>🏠</i> Casas
                </a>
                <?php endif; ?>
                
                <?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('secretaria')): ?>
                <div class="menu-subtitle">Reservas</div>
                <a href="<?php echo UrlHelper::base('reservas'); ?>" class="menu-item <?php echo UrlHelper::isCurrent('reservas') ? 'active' : ''; ?>">
                    <i>📅</i> Reservas
                </a>
                <a href="<?php echo UrlHelper::base('reservas/checkin'); ?>" class="menu-item <?php echo UrlHelper::isCurrent('reservas/checkin') ? 'active' : ''; ?>">
                    <i>✅</i> Check-in
                </a>
                <a href="<?php echo UrlHelper::base('reservas/checkout'); ?>" class="menu-item <?php echo UrlHelper::isCurrent('reservas/checkout') ? 'active' : ''; ?>">
                    <i>🚪</i> Check-out
                </a>
                <?php endif; ?>
                
                <?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('contabilidade')): ?>
                <div class="menu-subtitle">Financeiro</div>
                <a href="<?php echo UrlHelper::base('relatorios/financeiros'); ?>" class="menu-item <?php echo UrlHelper::isCurrent('relatorios/financeiros') ? 'active' : ''; ?>">
                    <i>💰</i> Relatórios Financeiros
                </a>
                <?php endif; ?>
                
                <?php if (AuthHelper::hasProfile('gestor_geral')): ?>
                <div class="menu-subtitle">Relatórios</div>
                <a href="<?php echo UrlHelper::base('relatorios'); ?>" class="menu-item <?php echo UrlHelper::isCurrent('relatorios') ? 'active' : ''; ?>">
                    <i>📈</i> Relatórios Gerais
                </a>
                <a href="<?php echo UrlHelper::base('relatorios/ocupacao'); ?>" class="menu-item <?php echo UrlHelper::isCurrent('relatorios/ocupacao') ? 'active' : ''; ?>">
                    <i>📊</i> Taxa de Ocupação
                </a>
                <?php endif; ?>
            </nav>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="main-content">
            <!-- Cabeçalho -->
            <header class="header">
                <div class="header-title">
                    <?php echo isset($page_title) ? $page_title : 'Dashboard'; ?>
                </div>
                
                <div class="header-actions">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr(AuthHelper::getUser()['nome'], 0, 2)); ?>
                        </div>
                        <div class="user-details">
                            <div class="user-name"><?php echo AuthHelper::getUser()['nome']; ?></div>
                            <div class="user-profile"><?php echo AuthHelper::getProfileName(AuthHelper::getProfile()); ?></div>
                        </div>
                    </div>
                    
                    <a href="<?php echo UrlHelper::base('logout'); ?>" class="btn btn-danger btn-sm">
                        <i>🚪</i> Sair
                    </a>
                </div>
            </header>

            <!-- Conteúdo da Página -->
            <div class="content">
                <?php if (SessionHelper::hasFlash('success')): ?>
                    <div class="alert alert-success">
                        <?php echo SessionHelper::getFlash('success'); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (SessionHelper::hasFlash('error')): ?>
                    <div class="alert alert-error">
                        <?php echo SessionHelper::getFlash('error'); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (SessionHelper::hasFlash('warning')): ?>
                    <div class="alert alert-warning">
                        <?php echo SessionHelper::getFlash('warning'); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (SessionHelper::hasFlash('info')): ?>
                    <div class="alert alert-info">
                        <?php echo SessionHelper::getFlash('info'); ?>
                    </div>
                <?php endif; ?>
                
                <?php echo $content; ?>
            </div>

            <!-- Rodapé -->
            <footer class="footer">
                <p>&copy; <?php echo date('Y'); ?> Sistema de Gestão de Casas para Hospedagem. Todos os direitos reservados.</p>
            </footer>
        </main>
    </div>
    <?php else: ?>
        <!-- Layout de Login -->
        <div class="login-container">
            <?php echo $content; ?>
        </div>
    <?php endif; ?>
    
    <script src="<?php echo UrlHelper::asset('js/script.js'); ?>"></script>
</body>
</html>
