<div class="card">
    <div class="card-header">
        <h3 class="card-title">Relatório de Ocupação</h3>
        <div class="btn-group">
            <a href="<?php echo UrlHelper::base('relatorios/exportarOcupacao'); ?>?mes=<?php echo urlencode($mes); ?>&localizacao_id=<?php echo urlencode($localizacaoId ?? ''); ?>" 
               class="btn btn-info">
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
                <label class="form-label">Mês/Ano</label>
                <input type="month" name="mes" class="form-control" 
                       value="<?php echo htmlspecialchars($mes); ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Localização</label>
                <select name="localizacao_id" class="form-control">
                    <option value="">Todas</option>
                    <?php
                    $localizacaoModel = new LocalizacaoModel();
                    $localizacoes = $localizacaoModel->getAll();
                    foreach ($localizacoes as $localizacao):
                    ?>
                        <option value="<?php echo $localizacao['id']; ?>"
                                <?php echo (isset($localizacaoId) && $localizacaoId == $localizacao['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($localizacao['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="<?php echo UrlHelper::base('relatorios/ocupacao'); ?>" class="btn btn-secondary" style="margin-left: 10px;">Limpar</a>
            </div>
        </form>
        
        <!-- Resumo de Ocupação -->
        <div class="stats-grid" style="margin-bottom: 30px;">
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-value"><?php echo number_format($taxaOcupacao, 1); ?>%</div>
                <div class="stat-label">Taxa de Ocupação</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🏠</div>
                <div class="stat-value"><?php echo count($estatisticasCasas); ?></div>
                <div class="stat-label">Total de Casas</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-value"><?php echo formatCurrency(array_sum(array_column($estatisticasCasas, 'receita'))); ?></div>
                <div class="stat-label">Receita Total</div>
            </div>
        </div>
        
        <!-- Gráfico de Ocupação -->
        <div class="card" style="margin-bottom: 30px;">
            <div class="card-header">
                <h5 style="margin: 0; color: #333;">Taxa de Ocupação por Casa</h5>
            </div>
            <div class="card-body">
                <div style="height: 300px; position: relative;">
                    <canvas id="ocupacaoChart"></canvas>
                </div>
                <script>
                    // Gráfico de ocupação por casa
                    const ocupacaoData = <?php echo json_encode(array_slice($estatisticasCasas, 0, 10)); ?>;
                    
                    let ocupacaoChartHTML = '<div style="display: flex; align-items: flex-end; height: 250px; gap: 8px; padding: 10px; overflow-x: auto;">';
                    ocupacaoData.forEach(data => {
                        const height = data.taxa_ocupacao;
                        const color = height >= 80 ? '#28a745' : (height >= 50 ? '#ffc107' : '#dc3545');
                        ocupacaoChartHTML += `
                            <div style="flex: 0 0 60px; display: flex; flex-direction: column; align-items: center;">
                                <div style="width: 100%; background: ${color}; 
                                            height: ${height}%; border-radius: 4px 4px 0 0; position: relative;">
                                    <div style="position: absolute; top: -20px; left: 50%; transform: translateX(-50%); 
                                               font-size: 0.6rem; font-weight: bold;">${height}%</div>
                                </div>
                                <div style="margin-top: 8px; font-size: 0.5rem; text-align: center; word-break: break-all;">
                                    ${data.codigo}
                                </div>
                            </div>
                        `;
                    });
                    ocupacaoChartHTML += '</div>';
                    
                    document.getElementById('ocupacaoChart').innerHTML = ocupacaoChartHTML;
                </script>
            </div>
        </div>
        
        <!-- Tabela Detalhada -->
        <div class="card">
            <div class="card-header">
                <h5 style="margin: 0; color: #333;">Detalhes por Casa</h5>
            </div>
            <div class="card-body">
                <?php if (empty($estatisticasCasas)): ?>
                    <p style="text-align: center; color: #666; padding: 20px;">Nenhum dado encontrado para o período selecionado.</p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Casa</th>
                                    <th>Código</th>
                                    <th>Localização</th>
                                    <th>Tipologia</th>
                                    <th>Capacidade</th>
                                    <th>Dias Ocupados</th>
                                    <th>Taxa Ocupação</th>
                                    <th>Receita</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estatisticasCasas as $estatistica): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($estatistica['nome']); ?></td>
                                        <td><strong><?php echo htmlspecialchars($estatistica['codigo']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($estatistica['localizacao_nome']); ?></td>
                                        <td><?php echo htmlspecialchars($estatistica['tipologia']); ?></td>
                                        <td><?php echo $estatistica['capacidade']; ?> pessoa(s)</td>
                                        <td>
                                            <?php echo $estatistica['dias_ocupados']; ?> / <?php echo $estatistica['dias_disponiveis']; ?> dias
                                        </td>
                                        <td>
                                            <?php
                                            $cor = '#28a745';
                                            if ($estatistica['taxa_ocupacao'] < 50) {
                                                $cor = '#dc3545';
                                            } elseif ($estatistica['taxa_ocupacao'] < 80) {
                                                $cor = '#ffc107';
                                            }
                                            ?>
                                            <span style="color: <?php echo $cor; ?>; font-weight: 600;">
                                                <?php echo number_format($estatistica['taxa_ocupacao'], 1); ?>%
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?php echo formatCurrency($estatistica['receita']); ?></strong>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Resumo -->
                    <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                            <div>
                                <strong style="color: #666;">Total de Casas:</strong><br>
                                <span style="font-size: 1.1rem;"><?php echo count($estatisticasCasas); ?></span>
                            </div>
                            <div>
                                <strong style="color: #666;">Média de Ocupação:</strong><br>
                                <span style="font-size: 1.1rem;">
                                    <?php echo number_format(array_sum(array_column($estatisticasCasas, 'taxa_ocupacao')) / count($estatisticasCasas), 1); ?>%
                                </span>
                            </div>
                            <div>
                                <strong style="color: #666;">Receita Total:</strong><br>
                                <span style="font-size: 1.1rem; color: #28a745;">
                                    <?php echo formatCurrency(array_sum(array_column($estatisticasCasas, 'receita'))); ?>
                                </span>
                            </div>
                            <div>
                                <strong style="color: #666;">Receita Média por Casa:</strong><br>
                                <span style="font-size: 1.1rem;">
                                    <?php echo formatCurrency(array_sum(array_column($estatisticasCasas, 'receita')) / count($estatisticasCasas)); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
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
