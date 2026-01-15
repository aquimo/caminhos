<div class="card">
    <div class="card-header">
        <h3 class="card-title">Relatórios Gerais</h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <!-- Relatórios Financeiros -->
            <?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('contabilidade')): ?>
            <div class="card" style="border-left: 4px solid #28a745;">
                <div class="card-body" style="text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 15px;">💰</div>
                    <h4 style="margin-bottom: 15px; color: #333;">Relatórios Financeiros</h4>
                    <p style="color: #666; margin-bottom: 20px;">
                        Análise detalhada de receitas, pagamentos e fluxo de caixa.
                    </p>
                    <a href="<?php echo UrlHelper::base('relatorios/financeiros'); ?>" class="btn btn-success">
                        <i>📊</i> Acessar Relatórios
                    </a>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Relatórios de Ocupação -->
            <?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('gestor_condominios')): ?>
            <div class="card" style="border-left: 4px solid #17a2b8;">
                <div class="card-body" style="text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 15px;">📈</div>
                    <h4 style="margin-bottom: 15px; color: #333;">Taxa de Ocupação</h4>
                    <p style="color: #666; margin-bottom: 20px;">
                        Monitorização da ocupação das casas e desempenho por localização.
                    </p>
                    <a href="<?php echo UrlHelper::base('relatorios/ocupacao'); ?>" class="btn btn-info">
                        <i>📊</i> Ver Ocupação
                    </a>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Estatísticas Gerais -->
            <?php if (AuthHelper::hasProfile('gestor_geral')): ?>
            <div class="card" style="border-left: 4px solid #ffc107;">
                <div class="card-body" style="text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 15px;">📋</div>
                    <h4 style="margin-bottom: 15px; color: #333;">Estatísticas Gerais</h4>
                    <p style="color: #666; margin-bottom: 20px;">
                        Visão geral completa do sistema e desempenho global.
                    </p>
                    <a href="<?php echo UrlHelper::base('dashboard'); ?>" class="btn btn-warning">
                        <i>📊</i> Ver Dashboard
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Resumo Rápido -->
        <div style="margin-top: 40px;">
            <h4 style="margin-bottom: 20px; color: #333;">Resumo Rápido do Sistema</h4>
            
            <?php
            // Obter estatísticas rápidas
            global $db;
            
            $stats = [];
            
            // Total de casas
            $stmt = $db->query("SELECT COUNT(*) as total FROM casas");
            $stats['casas'] = $stmt->fetch()['total'];
            
            // Total de reservas este mês
            $stmt = $db->query("
                SELECT COUNT(*) as total FROM reservas 
                WHERE MONTH(data_reserva) = MONTH(CURRENT_DATE) 
                AND YEAR(data_reserva) = YEAR(CURRENT_DATE)
            ");
            $stats['reservas_mes'] = $stmt->fetch()['total'];
            
            // Total de clientes
            $stmt = $db->query("SELECT COUNT(*) as total FROM clientes");
            $stats['clientes'] = $stmt->fetch()['total'];
            
            // Receitas este mês
            if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('contabilidade')) {
                $stmt = $db->query("
                    SELECT SUM(valor) as total FROM pagamentos 
                    WHERE MONTH(data_pagamento) = MONTH(CURRENT_DATE) 
                    AND YEAR(data_pagamento) = YEAR(CURRENT_DATE)
                ");
                $stats['receitas_mes'] = $stmt->fetch()['total'] ?? 0;
            }
            ?>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div class="stat-card">
                    <div class="stat-icon">🏠</div>
                    <div class="stat-value"><?php echo $stats['casas']; ?></div>
                    <div class="stat-label">Total de Casas</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">📅</div>
                    <div class="stat-value"><?php echo $stats['reservas_mes']; ?></div>
                    <div class="stat-label">Reservas este Mês</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-value"><?php echo $stats['clientes']; ?></div>
                    <div class="stat-label">Total de Clientes</div>
                </div>
                
                <?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('contabilidade')): ?>
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-value"><?php echo formatCurrency($stats['receitas_mes']); ?></div>
                    <div class="stat-label">Receitas este Mês</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Ações Rápidas -->
        <div style="margin-top: 40px;">
            <h4 style="margin-bottom: 20px; color: #333;">Ações Rápidas</h4>
            
            <div class="btn-group">
                <?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('secretaria')): ?>
                    <a href="<?php echo UrlHelper::base('reservas/criar'); ?>" class="btn btn-primary">
                        <i>➕</i> Nova Reserva
                    </a>
                <?php endif; ?>
                
                <?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('gestor_condominios')): ?>
                    <a href="<?php echo UrlHelper::base('casas/criar'); ?>" class="btn btn-secondary">
                        <i>🏠</i> Nova Casa
                    </a>
                <?php endif; ?>
                
                <?php if (AuthHelper::hasProfile('gestor_geral')): ?>
                    <a href="<?php echo UrlHelper::base('utilizadores/criar'); ?>" class="btn btn-warning">
                        <i>👤</i> Novo Utilizador
                    </a>
                <?php endif; ?>
                
                <?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('secretaria')): ?>
                    <a href="<?php echo UrlHelper::base('reservas/checkin'); ?>" class="btn btn-success">
                        <i>✅</i> Check-ins
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    div[style*="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr))"],
    div[style*="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr))"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
