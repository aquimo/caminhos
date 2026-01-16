<div class="card">
    <div class="card-header">
        <h3 class="card-title">Hóspedes</h3>
        <div class="btn-group">
            <a href="index.php?route=hospedes/criar" class="btn btn-primary">
                <i>➕</i> Registar Hóspede
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Estatísticas -->
        <div class="stats-grid" style="margin-bottom: 30px;">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-value"><?php echo $total; ?></div>
                <div class="stat-label">Total de Hóspedes</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🏠</div>
                <div class="stat-value"><?php echo $estatisticas['hospedes_ativos']; ?></div>
                <div class="stat-label">Hóspedes Ativos</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-value"><?php echo formatCurrency($estatisticas['valor_pendente']); ?></div>
                <div class="stat-label">Valor Pendente</div>
            </div>
        </div>
        
        <!-- Filtros -->
        <div style="margin-bottom: 20px;">
            <form method="GET" style="display: flex; gap: 15px; align-items: center;">
                <input type="hidden" name="route" value="hospedes">
                
                <div class="form-group" style="margin: 0;">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Pesquisar hóspede..." 
                           value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                           style="width: 250px;">
                </div>
                
                <div class="form-group" style="margin: 0;">
                    <select name="estado" class="form-control" style="width: 150px;">
                        <option value="">Todos</option>
                        <option value="ativo" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'ativo') ? 'selected' : ''; ?>>
                            Ativos
                        </option>
                        <option value="checkout_realizado" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'checkout_realizado') ? 'selected' : ''; ?>>
                            Checkout Realizado
                        </option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-secondary">Filtrar</button>
                <a href="index.php?route=hospedes" class="btn btn-secondary">Limpar</a>
            </form>
        </div>
        
        <!-- Tabela de Hóspedes -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Procedência</th>
                        <th>Contacto</th>
                        <th>Casa</th>
                        <th>Check-in</th>
                        <th>Permanência</th>
                        <th>Valor a Pagar</th>
                        <th>Valor Pago</th>
                        <th>Estado</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($hospedes)): ?>
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 40px; color: #666;">
                                Nenhum hóspede encontrado.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($hospedes as $hospede): ?>
                            <tr>
                                <td><strong>#<?php echo $hospede['id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($hospede['nome']); ?></td>
                                <td><?php echo htmlspecialchars($hospede['procedencia']); ?></td>
                                <td><?php echo htmlspecialchars($hospede['contacto']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($hospede['casa_codigo']); ?><br>
                                    <small style="color: #666;"><?php echo htmlspecialchars($hospede['localizacao_nome']); ?></small>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($hospede['data_checkin'])); ?></td>
                                <td><?php echo htmlspecialchars($hospede['previsao_permanencia']); ?></td>
                                <td>
                                    <strong><?php echo formatCurrency($hospede['valor_pagar']); ?></strong>
                                </td>
                                <td>
                                    <?php if ($hospede['valor_pago'] > 0): ?>
                                        <strong style="color: #28a745;"><?php echo formatCurrency($hospede['valor_pago']); ?></strong>
                                    <?php else: ?>
                                        <span style="color: #dc3545;">Não pago</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $badgeClass = 'badge-success';
                                    $badgeText = 'Ativo';
                                    
                                    switch ($hospede['estado']) {
                                        case 'checkout_realizado':
                                            $badgeClass = 'badge-secondary';
                                            $badgeText = 'Checkout';
                                            break;
                                        case 'cancelado':
                                            $badgeClass = 'badge-danger';
                                            $badgeText = 'Cancelado';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        <?php echo $badgeText; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="index.php?route=hospedes/ver&id=<?php echo $hospede['id']; ?>" 
                                           class="btn btn-sm btn-secondary" title="Ver Detalhes">
                                            <i>👁️</i>
                                        </a>
                                        <?php if ($hospede['estado'] == 'ativo'): ?>
                                            <a href="index.php?route=hospedes/checkout&id=<?php echo $hospede['id']; ?>" 
                                               class="btn btn-sm btn-warning" title="Realizar Check-out">
                                                <i>🚪</i> Checkout
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginação -->
        <?php if ($totalPages > 1): ?>
        <div style="text-align: center; margin-top: 20px;">
            <?php
            $currentUrl = $_SERVER['REQUEST_URI'];
            $urlParts = parse_url($currentUrl);
            parse_str($urlParts['query'] ?? '', $queryParams);
            unset($queryParams['page']);
            $baseUrl = $urlParts['path'] . '?' . http_build_query($queryParams);
            ?>
            
            <?php if ($page > 1): ?>
                <a href="<?php echo $baseUrl . '&page=' . ($page - 1); ?>" class="btn btn-secondary">« Anterior</a>
            <?php endif; ?>
            
            <span style="margin: 0 10px; font-weight: bold;">
                Página <?php echo $page; ?> de <?php echo $totalPages; ?>
            </span>
            
            <?php if ($page < $totalPages): ?>
                <a href="<?php echo $baseUrl . '&page=' . ($page + 1); ?>" class="btn btn-secondary">Próximo »</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
