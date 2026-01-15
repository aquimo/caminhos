<div class="card">
    <div class="card-header">
        <h3 class="card-title">Check-outs Pendentes</h3>
        <a href="<?php echo UrlHelper::base('reservas'); ?>" class="btn btn-secondary">
            <i>←</i> Voltar
        </a>
    </div>
    <div class="card-body">
        <?php if (empty($pendentes) && empty($ativas)): ?>
            <div style="text-align: center; padding: 40px; color: #666;">
                <div style="font-size: 3rem; margin-bottom: 10px;">🏠</div>
                <p>Nenhuma reserva ativa no momento.</p>
                <small style="color: #666;">Todas as casas estão disponíveis.</small>
            </div>
        <?php else: ?>
            <!-- Check-outs Pendentes (para hoje) -->
            <?php if (!empty($pendentes)): ?>
                <div style="margin-bottom: 30px;">
                    <h4 style="color: #dc3545; margin-bottom: 15px;">
                        🚪 Check-outs para Hoje (<?php echo count($pendentes); ?>)
                    </h4>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px;">
                        <?php foreach ($pendentes as $reserva): ?>
                            <div class="card" style="border-left: 4px solid #dc3545;">
                                <div class="card-body">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                                        <div>
                                            <h5 style="margin: 0; color: #333;"><?php echo htmlspecialchars($reserva['casa_codigo']); ?></h5>
                                            <small style="color: #666;"><?php echo htmlspecialchars($reserva['casa_nome']); ?></small>
                                        </div>
                                        <span class="badge badge-warning">Check-out Hoje</span>
                                    </div>
                                    
                                    <div style="margin-bottom: 15px;">
                                        <strong style="color: #666;">Cliente:</strong><br>
                                        <?php echo htmlspecialchars($reserva['cliente_nome']); ?><br>
                                        <small style="color: #666;">
                                            📧 <?php echo htmlspecialchars($reserva['cliente_email']); ?><br>
                                            <?php if ($reserva['cliente_telefone']): ?>
                                                📱 <?php echo htmlspecialchars($reserva['cliente_telefone']); ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    
                                    <div style="margin-bottom: 15px;">
                                        <strong style="color: #666;">Período:</strong><br>
                                        📅 <?php echo date('d/m/Y', strtotime($reserva['data_checkin'])); ?> → 
                                        <strong><?php echo date('d/m/Y', strtotime($reserva['data_checkout'])); ?></strong><br>
                                        <small style="color: #666;">
                                            🏠 <?php echo htmlspecialchars($reserva['tipologia']); ?> • 
                                            👥 <?php echo $reserva['capacidade']; ?> pessoa(s) •
                                            🌙 <?php echo $reserva['numero_noites']; ?> noites
                                        </small>
                                    </div>
                                    
                                    <div style="margin-bottom: 15px;">
                                        <strong style="color: #666;">Valor:</strong><br>
                                        <span style="font-size: 1.1rem; font-weight: 600; color: #28a745;">
                                            €<?php echo number_format($reserva['valor_total'], 2, ',', ' '); ?>
                                        </span>
                                        <?php if ($reserva['valor_pago'] < $reserva['valor_total']): ?>
                                            <br><small style="color: #dc3545;">
                                                ⚠️ Pendente: €<?php echo number_format($reserva['valor_total'] - $reserva['valor_pago'], 2, ',', ' '); ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div style="display: flex; gap: 10px;">
                                        <form method="POST" action="<?php echo UrlHelper::base('reservas/processarCheckout'); ?>" style="flex: 1;">
                                            <input type="hidden" name="id" value="<?php echo $reserva['id']; ?>">
                                            <button type="submit" class="btn btn-warning" style="width: 100%;" 
                                                    onclick="return confirm('Confirmar check-out para <?php echo htmlspecialchars($reserva['cliente_nome']); ?>?')">
                                                <i>🚪</i> Fazer Check-out
                                            </button>
                                        </form>
                                        
                                        <a href="<?php echo UrlHelper::base('reservas/ver?id=' . $reserva['id']); ?>" 
                                           class="btn btn-secondary" title="Ver Detalhes">
                                            <i>👁️</i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Reservas Ativas (check-in realizado) -->
            <?php if (!empty($ativas)): ?>
                <div>
                    <h4 style="color: #17a2b8; margin-bottom: 15px;">
                        🏠 Reservas Ativas (<?php echo count($ativas); ?>)
                    </h4>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px;">
                        <?php foreach ($ativas as $reserva): ?>
                            <div class="card" style="border-left: 4px solid #17a2b8;">
                                <div class="card-body">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                                        <div>
                                            <h5 style="margin: 0; color: #333;"><?php echo htmlspecialchars($reserva['casa_codigo']); ?></h5>
                                            <small style="color: #666;"><?php echo htmlspecialchars($reserva['casa_nome']); ?></small>
                                        </div>
                                        <span class="badge badge-info">Ativa</span>
                                    </div>
                                    
                                    <div style="margin-bottom: 15px;">
                                        <strong style="color: #666;">Cliente:</strong><br>
                                        <?php echo htmlspecialchars($reserva['cliente_nome']); ?><br>
                                        <small style="color: #666;">
                                            📧 <?php echo htmlspecialchars($reserva['cliente_email']); ?><br>
                                            <?php if ($reserva['cliente_telefone']): ?>
                                                📱 <?php echo htmlspecialchars($reserva['cliente_telefone']); ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    
                                    <div style="margin-bottom: 15px;">
                                        <strong style="color: #666;">Período:</strong><br>
                                        📅 <?php echo date('d/m/Y', strtotime($reserva['data_checkin'])); ?> → 
                                        <?php echo date('d/m/Y', strtotime($reserva['data_checkout'])); ?><br>
                                        <small style="color: #666;">
                                            🏠 <?php echo htmlspecialchars($reserva['tipologia']); ?> • 
                                            👥 <?php echo $reserva['capacidade']; ?> pessoa(s) •
                                            🌙 <?php echo $reserva['numero_noites']; ?> noites
                                        </small>
                                    </div>
                                    
                                    <div style="margin-bottom: 15px;">
                                        <strong style="color: #666;">Check-out:</strong><br>
                                        <?php
                                        $diasAteCheckout = floor((strtotime($reserva['data_checkout']) - strtotime(date('Y-m-d'))) / (60 * 60 * 24));
                                        if ($diasAteCheckout == 0) {
                                            echo '<span style="color: #dc3545; font-weight: 600;">Hoje</span>';
                                        } elseif ($diasAteCheckout == 1) {
                                            echo '<span style="color: #ffc107; font-weight: 600;">Amanhã</span>';
                                        } else {
                                            echo '<span style="color: #666;">Em ' . $diasAteCheckout . ' dias</span>';
                                        }
                                        ?>
                                        <br>
                                        <small style="color: #666;"><?php echo date('d/m/Y', strtotime($reserva['data_checkout'])); ?></small>
                                    </div>
                                    
                                    <div style="display: flex; gap: 10px;">
                                        <a href="<?php echo UrlHelper::base('reservas/ver?id=' . $reserva['id']); ?>" 
                                           class="btn btn-secondary" style="flex: 1;" title="Ver Detalhes">
                                            <i>👁️</i> Ver Detalhes
                                        </a>
                                        
                                        <?php if ($diasAteCheckout <= 1): ?>
                                            <form method="POST" action="<?php echo UrlHelper::base('reservas/processarCheckout'); ?>">
                                                <input type="hidden" name="id" value="<?php echo $reserva['id']; ?>">
                                                <button type="submit" class="btn btn-warning" 
                                                        onclick="return confirm('Confirmar check-out para <?php echo htmlspecialchars($reserva['cliente_nome']); ?>?')">
                                                    <i>🚪</i> Check-out
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
@media (max-width: 768px) {
    div[style*="grid-template-columns: repeat(auto-fit, minmax(350px, 1fr))"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
