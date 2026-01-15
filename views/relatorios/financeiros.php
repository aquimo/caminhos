<div class="card">
    <div class="card-header">
        <h3 class="card-title">Relatórios Financeiros</h3>
        <div class="btn-group">
            <a href="<?php echo UrlHelper::base('relatorios/exportarFinanceiro'); ?>?data_inicio=<?php echo urlencode($dataInicio); ?>&data_fim=<?php echo urlencode($dataFim); ?>" 
               class="btn btn-success">
                <i>📥</i> Exportar CSV
            </a>
            <a href="<?php echo UrlHelper::base('relatorios'); ?>" class="btn btn-secondary">
                <i>←</i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtros -->
        <form method="GET" class="form-row" style="margin-bottom: 30px;">
            <div class="form-group">
                <label class="form-label">Data Início</label>
                <input type="date" name="data_inicio" class="form-control" 
                       value="<?php echo htmlspecialchars($dataInicio); ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Data Fim</label>
                <input type="date" name="data_fim" class="form-control" 
                       value="<?php echo htmlspecialchars($dataFim); ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Método Pagamento</label>
                <select name="metodo" class="form-control">
                    <option value="">Todos</option>
                    <option value="dinheiro" <?php echo (isset($metodo) && $metodo == 'dinheiro') ? 'selected' : ''; ?>>
                        Dinheiro
                    </option>
                    <option value="mpesa" <?php echo (isset($metodo) && $metodo == 'mpesa') ? 'selected' : ''; ?>>
                        M-Pesa
                    </option>
                    <option value="emola" <?php echo (isset($metodo) && $metodo == 'emola') ? 'selected' : ''; ?>>
                        Emola
                    </option>
                    <option value="mkesh" <?php echo (isset($metodo) && $metodo == 'mkesh') ? 'selected' : ''; ?>>
                        M-Kesh
                    </option>
                    <option value="cartao" <?php echo (isset($metodo) && $metodo == 'cartao') ? 'selected' : ''; ?>>
                        Cartão
                    </option>
                    <option value="numerario" <?php echo (isset($metodo) && $metodo == 'numerario') ? 'selected' : ''; ?>>
                        Númerario
                    </option>
                    <option value="transferencia_bancaria" <?php echo (isset($metodo) && $metodo == 'transferencia_bancaria') ? 'selected' : ''; ?>>
                        Transferência Bancária
                    </option>
                    <option value="outro" <?php echo (isset($metodo) && $metodo == 'outro') ? 'selected' : ''; ?>>
                        Outro
                    </option>
                </select>
            </div>
            
            <div class="form-group" style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="<?php echo UrlHelper::base('relatorios/financeiros'); ?>" class="btn btn-secondary" style="margin-left: 10px;">Limpar</a>
            </div>
        </form>
        
        <!-- Resumo Financeiro -->
        <div class="stats-grid" style="margin-bottom: 30px;">
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-value"><?php echo formatCurrency($receitasPeriodo); ?></div>
                <div class="stat-label">Receitas no Período</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">💳</div>
                <div class="stat-value"><?php echo count($pagamentosPendentes); ?></div>
                <div class="stat-label">Pagamentos Pendentes</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-value"><?php echo formatCurrency(array_sum(array_column($pagamentosPendentes, 'pendente'))); ?></div>
                <div class="stat-label">Valor Pendente</div>
            </div>
        </div>
        
        <!-- Gráfico de Receitas por Mês -->
        <div class="card" style="margin-bottom: 30px;">
            <div class="card-header">
                <h5 style="margin: 0; color: #333;">Evolução de Receitas (Últimos 12 Meses)</h5>
            </div>
            <div class="card-body">
                <div style="height: 300px; position: relative;">
                    <canvas id="receitasChart"></canvas>
                </div>
                <script>
                    // Gráfico de receitas por mês
                    const receitasData = <?php echo json_encode($receitasPorMes); ?>;
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
            </div>
        </div>
        
        <!-- Receitas por Método -->
        <div class="card" style="margin-bottom: 30px;">
            <div class="card-header">
                <h5 style="margin: 0; color: #333;">Receitas por Método de Pagamento</h5>
            </div>
            <div class="card-body">
                <?php if (empty($receitasPorMetodo)): ?>
                    <p style="text-align: center; color: #666; padding: 20px;">Nenhum dado encontrado para o período selecionado.</p>
                <?php else: ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <?php foreach ($receitasPorMetodo as $metodo): ?>
                            <div style="text-align: center; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
                                <div style="font-size: 1.2rem; font-weight: 600; margin-bottom: 5px;">
                                    <?php echo formatCurrency($metodo['total']); ?>
                                </div>
                                <div style="color: #666; font-size: 0.9rem;">
                                    <?php
                                    $metodoLabels = [
                                        'dinheiro' => 'Dinheiro',
                                        'mpesa' => 'M-Pesa',
                                        'emola' => 'Emola',
                                        'mkesh' => 'M-Kesh',
                                        'cartao' => 'Cartão',
                                        'numerario' => 'Númerario',
                                        'transferencia_bancaria' => 'Transferência Bancária',
                                        'outro' => 'Outro'
                                    ];
                                    echo $metodoLabels[$metodo['metodo_pagamento']] ?? $metodo['metodo_pagamento'];
                                    ?>
                                </div>
                                <div style="color: #999; font-size: 0.8rem; margin-top: 5px;">
                                    <?php echo $metodo['count']; ?> pagamento(s)
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Pagamentos Pendentes -->
        <?php if (!empty($pagamentosPendentes)): ?>
        <div class="card">
            <div class="card-header">
                <h5 style="margin: 0; color: #333;">Pagamentos Pendentes</h5>
            </div>
            <div class="card-body">
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Casa</th>
                                <th>Valor Total</th>
                                <th>Valor Pago</th>
                                <th>Valor Pendente</th>
                                <th>Data Check-in</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pagamentosPendentes as $pagamento): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($pagamento['cliente_nome']); ?></td>
                                    <td><?php echo htmlspecialchars($pagamento['casa_codigo']); ?></td>
                                    <td><?php echo formatCurrency($pagamento['valor_total']); ?></td>
                                    <td><?php echo formatCurrency($pagamento['valor_pago']); ?></td>
                                    <td>
                                        <strong style="color: #dc3545;">
                                            <?php echo formatCurrency($pagamento['pendente']); ?>
                                        </strong>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($pagamento['data_checkin'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: end;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    div[style*="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr))"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
