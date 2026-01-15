<div class="card">
    <div class="card-header">
        <h3 class="card-title">Gestão de Reservas</h3>
        <div class="btn-group">
            <?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('secretaria')): ?>
                <a href="<?php echo UrlHelper::base('reservas/criar'); ?>" class="btn btn-primary">
                    <i>➕</i> Nova Reserva
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtros -->
        <form method="GET" class="form-row" style="margin-bottom: 20px;">
            <div class="form-group">
                <label class="form-label">Pesquisar</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Cliente, casa ou código..." 
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="confirmada" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'confirmada') ? 'selected' : ''; ?>>
                        Confirmada
                    </option>
                    <option value="checkin_realizado" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'checkin_realizado') ? 'selected' : ''; ?>>
                        Check-in Realizado
                    </option>
                    <option value="checkout_realizado" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'checkout_realizado') ? 'selected' : ''; ?>>
                        Check-out Realizado
                    </option>
                    <option value="cancelada" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'cancelada') ? 'selected' : ''; ?>>
                        Cancelada
                    </option>
                </select>
            </div>
            
            <div class="form-group" style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="<?php echo UrlHelper::base('reservas'); ?>" class="btn btn-secondary" style="margin-left: 10px;">Limpar</a>
            </div>
        </form>
        
        <!-- Tabela de Reservas -->
        <?php if (empty($reservas)): ?>
            <div style="text-align: center; padding: 40px; color: #666;">
                <div style="font-size: 3rem; margin-bottom: 10px;">📅</div>
                <p>Nenhuma reserva encontrada.</p>
                <?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('secretaria')): ?>
                    <a href="<?php echo UrlHelper::base('reservas/criar'); ?>" class="btn btn-primary">Criar Primeira Reserva</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table" id="reservasTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Casa</th>
                            <th>Período</th>
                            <th>Noites</th>
                            <th>Valor Total</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $reserva): ?>
                            <tr>
                                <td><strong>#<?php echo str_pad($reserva['id'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                                <td>
                                    <?php echo htmlspecialchars($reserva['cliente_nome']); ?><br>
                                    <small style="color: #666;"><?php echo htmlspecialchars($reserva['cliente_email']); ?></small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($reserva['casa_codigo']); ?><br>
                                    <small style="color: #666;"><?php echo htmlspecialchars($reserva['casa_nome']); ?></small>
                                </td>
                                <td>
                                    <?php echo date('d/m/Y', strtotime($reserva['data_checkin'])); ?><br>
                                    <small style="color: #666;">até <?php echo date('d/m/Y', strtotime($reserva['data_checkout'])); ?></small>
                                </td>
                                <td><?php echo $reserva['numero_noites']; ?></td>
                                <td>
                                    <strong>€<?php echo number_format($reserva['valor_total'], 2, ',', ' '); ?></strong><br>
                                    <?php if ($reserva['valor_pago'] < $reserva['valor_total']): ?>
                                        <small style="color: #dc3545;">
                                            Pendente: €<?php echo number_format($reserva['valor_total'] - $reserva['valor_pago'], 2, ',', ' '); ?>
                                        </small>
                                    <?php else: ?>
                                        <small style="color: #28a745;">Pago</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $badgeClass = 'badge-secondary';
                                    $badgeText = 'Confirmada';
                                    
                                    switch ($reserva['estado']) {
                                        case 'confirmada':
                                            $badgeClass = 'badge-success';
                                            $badgeText = 'Confirmada';
                                            break;
                                        case 'checkin_realizado':
                                            $badgeClass = 'badge-info';
                                            $badgeText = 'Check-in';
                                            break;
                                        case 'checkout_realizado':
                                            $badgeClass = 'badge-warning';
                                            $badgeText = 'Finalizada';
                                            break;
                                        case 'cancelada':
                                            $badgeClass = 'badge-danger';
                                            $badgeText = 'Cancelada';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        <?php echo $badgeText; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?php echo UrlHelper::base('reservas/ver?id=' . $reserva['id']); ?>" 
                                           class="btn btn-sm btn-secondary" title="Ver Detalhes">
                                            <i>👁️</i>
                                        </a>
                                        <?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('secretaria')): ?>
                                            <?php if ($reserva['estado'] === 'confirmada'): ?>
                                                <a href="<?php echo UrlHelper::base('reservas/cancelar?id=' . $reserva['id']); ?>" 
                                                   class="btn btn-sm btn-warning" 
                                                   onclick="return confirm('Tem certeza que deseja cancelar esta reserva?')" 
                                                   title="Cancelar">
                                                    <i>❌</i>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 20px; text-align: center; color: #666;">
                <small>Total de <?php echo count($reservas); ?> reserva(s)</small>
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
}
</style>
