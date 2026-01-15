<div class="stats-grid">
    <!-- Estatísticas de Casas -->
    <div class="stat-card">
        <div class="stat-icon">🏠</div>
        <div class="stat-value"><?php echo number_format($stats['total_casas']); ?></div>
        <div class="stat-label">Total de Casas</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-value"><?php echo number_format($stats['casas_disponiveis']); ?></div>
        <div class="stat-label">Casas Disponíveis</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">🔑</div>
        <div class="stat-value"><?php echo number_format($stats['casas_ocupadas']); ?></div>
        <div class="stat-label">Casas Ocupadas</div>
    </div>
    
    <!-- Estatísticas de Reservas -->
    <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-value"><?php echo number_format($stats['total_reservas']); ?></div>
        <div class="stat-label">Total de Reservas</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">📋</div>
        <div class="stat-value"><?php echo number_format($stats['reservas_ativas']); ?></div>
        <div class="stat-label">Reservas Ativas</div>
    </div>
    
    <?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('secretaria')): ?>
    <div class="stat-card">
        <div class="stat-icon">⏰</div>
        <div class="stat-value"><?php echo number_format($stats['checkins_pendentes']); ?></div>
        <div class="stat-label">Check-ins Pendentes</div>
    </div>
    <?php endif; ?>
    
    <!-- Estatísticas de Clientes -->
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-value"><?php echo number_format($stats['total_clientes']); ?></div>
        <div class="stat-label">Total de Clientes</div>
    </div>
    
    <!-- Estatísticas Financeiras -->
    <?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('contabilidade')): ?>
    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-value"><?php echo formatCurrency($stats['total_receitas']); ?></div>
        <div class="stat-label">Total de Receitas</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">📈</div>
        <div class="stat-value"><?php echo formatCurrency($stats['receitas_mes']); ?></div>
        <div class="stat-label">Receitas este Mês</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">💳</div>
        <div class="stat-value"><?php echo formatCurrency($stats['pagamentos_pendentes']); ?></div>
        <div class="stat-label">Pagamentos Pendentes</div>
    </div>
    <?php endif; ?>
    
    <!-- Estatísticas de Utilizadores -->
    <?php if (AuthHelper::hasProfile('gestor_geral')): ?>
    <div class="stat-card">
        <div class="stat-icon">👤</div>
        <div class="stat-value"><?php echo number_format($stats['total_utilizadores']); ?></div>
        <div class="stat-label">Total de Utilizadores</div>
    </div>
    <?php endif; ?>
    
    <!-- Taxa de Ocupação -->
    <?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('gestor_condominios')): ?>
    <div class="stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-value"><?php echo number_format($stats['taxa_ocupacao'] ?? 0, 1); ?>%</div>
        <div class="stat-label">Taxa de Ocupação</div>
    </div>
    <?php endif; ?>
</div>

<div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
    <!-- Atividades Recentes -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Atividades Recentes</h3>
        </div>
        <div class="card-body">
            <?php if (empty($recentActivities)): ?>
                <p style="text-align: center; color: #666; padding: 20px;">Sem atividades recentes.</p>
            <?php else: ?>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php foreach ($recentActivities as $activity): ?>
                        <div style="padding: 12px 0; border-bottom: 1px solid #eee;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="font-weight: 500; margin-bottom: 4px;">
                                        <?php echo htmlspecialchars($activity['descricao']); ?>
                                    </div>
                                    <div style="font-size: 0.8rem; color: #666;">
                                        <?php echo date('d/m/Y H:i', strtotime($activity['data'])); ?>
                                    </div>
                                </div>
                                <div>
                                    <?php
                                    $badgeClass = 'badge-info';
                                    if ($activity['tipo'] === 'reserva') {
                                        $badgeClass = $activity['estado'] === 'confirmada' ? 'badge-success' : 'badge-warning';
                                    } elseif ($activity['tipo'] === 'checkin' || $activity['tipo'] === 'pagamento') {
                                        $badgeClass = 'badge-success';
                                    }
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        <?php echo ucfirst($activity['tipo']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Gráfico de Reservas por Mês -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Reservas por Mês</h3>
        </div>
        <div class="card-body">
            <?php if (empty($charts['reservas_mes'])): ?>
                <p style="text-align: center; color: #666; padding: 20px;">Sem dados disponíveis.</p>
            <?php else: ?>
                <div style="height: 300px; position: relative;">
                    <canvas id="reservasChart"></canvas>
                </div>
                <script>
                    // Gráfico simples de barras usando CSS
                    const reservasData = <?php echo json_encode($charts['reservas_mes']); ?>;
                    const maxValue = Math.max(...reservasData.map(d => d.total));
                    
                    let chartHTML = '<div style="display: flex; align-items: flex-end; height: 250px; gap: 8px; padding: 10px;">';
                    reservasData.forEach(data => {
                        const height = (data.total / maxValue) * 100;
                        chartHTML += `
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                                <div style="width: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                                            height: ${height}%; border-radius: 4px 4px 0 0; position: relative;">
                                    <div style="position: absolute; top: -20px; left: 50%; transform: translateX(-50%); 
                                               font-size: 0.7rem; font-weight: bold;">${data.total}</div>
                                </div>
                                <div style="margin-top: 8px; font-size: 0.6rem; text-align: center;">
                                    ${data.mes.substring(5)}
                                </div>
                            </div>
                        `;
                    });
                    chartHTML += '</div>';
                    
                    document.getElementById('reservasChart').innerHTML = chartHTML;
                </script>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Gráficos Adicionais -->
<?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('gestor_condominios')): ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Estado das Casas</h3>
    </div>
    <div class="card-body">
        <?php if (empty($charts['ocupacao'])): ?>
            <p style="text-align: center; color: #666; padding: 20px;">Sem dados disponíveis.</p>
        <?php else: ?>
            <div style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 20px;">
                <?php foreach ($charts['ocupacao'] as $estado): ?>
                    <div style="text-align: center;">
                        <div style="width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; 
                                   justify-content: center; margin: 0 auto 10px; font-size: 1.5rem; font-weight: bold;
                                   <?php 
                                   $colors = [
                                       'Disponível' => 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)',
                                       'Ocupado' => 'linear-gradient(135deg, #eb3349 0%, #f45c43 100%)',
                                       'Manutenção' => 'linear-gradient(135deg, #f2994a 0%, #f2c94c 100%)',
                                       'Indisponível' => '#6c757d'
                                   ];
                                   echo 'background: ' . ($colors[$estado['estado_label']] ?? '#6c757d') . ';';
                                   echo 'color: white;';
                                   ?>">
                            <?php echo $estado['total']; ?>
                        </div>
                        <div style="font-weight: 500;"><?php echo $estado['estado_label']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Gráfico de Receitas -->
<?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('contabilidade')): ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Receitas por Mês</h3>
    </div>
    <div class="card-body">
        <?php if (empty($charts['receitas_mes'])): ?>
            <p style="text-align: center; color: #666; padding: 20px;">Sem dados disponíveis.</p>
        <?php else: ?>
            <div style="height: 300px; position: relative;">
                <canvas id="receitasChart"></canvas>
            </div>
            <script>
                // Gráfico de receitas
                const receitasData = <?php echo json_encode($charts['receitas_mes']); ?>;
                const maxReceita = Math.max(...receitasData.map(d => parseFloat(d.total)));
                
                let receitasChartHTML = '<div style="display: flex; align-items: flex-end; height: 250px; gap: 8px; padding: 10px;">';
                receitasData.forEach(data => {
                    const height = (parseFloat(data.total) / maxReceita) * 100;
                    receitasChartHTML += `
                        <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                            <div style="width: 100%; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); 
                                        height: ${height}%; border-radius: 4px 4px 0 0; position: relative;">
                                <div style="position: absolute; top: -25px; left: 50%; transform: translateX(-50%); 
                                           font-size: 0.7rem; font-weight: bold;">${parseFloat(data.total).toFixed(0)} MZN</div>
                            </div>
                            <div style="margin-top: 8px; font-size: 0.6rem; text-align: center;">
                                ${data.mes.substring(5)}
                            </div>
                        </div>
                    `;
                });
                receitasChartHTML += '</div>';
                
                document.getElementById('receitasChart').innerHTML = receitasChartHTML;
            </script>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<style>
.row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

@media (max-width: 768px) {
    .row {
        grid-template-columns: 1fr;
    }
}
</style>
