<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detalhes da Reserva #<?php echo str_pad($reserva['id'], 6, '0', STR_PAD_LEFT); ?></h3>
        <div class="btn-group">
            <?php if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('secretaria')): ?>
                <?php if ($reserva['estado'] === 'confirmada'): ?>
                    <a href="<?php echo UrlHelper::base('reservas/cancelar?id=' . $reserva['id']); ?>" 
                       class="btn btn-warning" onclick="return confirm('Tem certeza que deseja cancelar esta reserva?')">
                        <i>❌</i> Cancelar Reserva
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            <a href="<?php echo UrlHelper::base('reservas'); ?>" class="btn btn-secondary">
                <i>←</i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Informações da Reserva -->
            <div>
                <h4 style="margin-bottom: 20px; color: #333;">Informações da Reserva</h4>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">ID da Reserva:</strong><br>
                    <span style="font-family: monospace; background: #f8f9fa; padding: 2px 6px; border-radius: 3px;">
                        #<?php echo str_pad($reserva['id'], 6, '0', STR_PAD_LEFT); ?>
                    </span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Estado:</strong><br>
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
                            $badgeText = 'Check-in Realizado';
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
                    <span class="badge <?php echo $badgeClass; ?>" style="font-size: 0.9rem;">
                        <?php echo $badgeText; ?>
                    </span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Período da Reserva:</strong><br>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div>
                            <strong>Check-in:</strong><br>
                            <?php echo date('d/m/Y', strtotime($reserva['data_checkin'])); ?>
                        </div>
                        <div style="font-size: 1.2rem;">→</div>
                        <div>
                            <strong>Check-out:</strong><br>
                            <?php echo date('d/m/Y', strtotime($reserva['data_checkout'])); ?>
                        </div>
                    </div>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Número de Noites:</strong><br>
                    <span style="font-size: 1.1rem;"><?php echo $reserva['numero_noites']; ?> noite(s)</span>
                </div>
                
                <?php if ($reserva['observacoes']): ?>
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Observações:</strong><br>
                    <p style="line-height: 1.6;"><?php echo nl2br(htmlspecialchars($reserva['observacoes'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Informações Financeiras -->
            <div>
                <h4 style="margin-bottom: 20px; color: #333;">Informações Financeiras</h4>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Valor Total:</strong><br>
                    <span style="font-size: 1.3rem; font-weight: 600; color: #28a745;">
                        €<?php echo number_format($reserva['valor_total'], 2, ',', ' '); ?>
                    </span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Valor Pago:</strong><br>
                    <span style="font-size: 1.1rem; font-weight: 600; color: <?php echo ($reserva['valor_pago'] >= $reserva['valor_total']) ? '#28a745' : '#dc3545'; ?>;">
                        €<?php echo number_format($reserva['valor_pago'], 2, ',', ' '); ?>
                    </span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Valor Pendente:</strong><br>
                    <span style="font-size: 1.1rem; font-weight: 600; color: <?php echo ($reserva['valor_pago'] < $reserva['valor_total']) ? '#dc3545' : '#28a745'; ?>;">
                        €<?php echo number_format($reserva['valor_total'] - $reserva['valor_pago'], 2, ',', ' '); ?>
                    </span>
                </div>
                
                <?php if ($reserva['valor_pago'] < $reserva['valor_total']): ?>
                <div style="margin-bottom: 15px;">
                    <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 10px;">
                        <small style="color: #856404;">
                            <strong>⚠️ Pagamento Pendente:</strong> Ainda falta receber €<?php echo number_format($reserva['valor_total'] - $reserva['valor_pago'], 2, ',', ' '); ?>
                        </small>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Informações do Cliente -->
        <div style="margin-top: 30px;">
            <h4 style="margin-bottom: 20px; color: #333;">Informações do Cliente</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <div>
                    <div style="margin-bottom: 15px;">
                        <strong style="color: #666;">Nome:</strong><br>
                        <?php echo htmlspecialchars($reserva['cliente_nome']); ?>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <strong style="color: #666;">Email:</strong><br>
                        <?php echo htmlspecialchars($reserva['cliente_email']); ?>
                    </div>
                    
                    <?php if ($reserva['cliente_telefone']): ?>
                    <div style="margin-bottom: 15px;">
                        <strong style="color: #666;">Telefone:</strong><br>
                        <?php echo htmlspecialchars($reserva['cliente_telefone']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div>
                    <div style="margin-bottom: 15px;">
                        <strong style="color: #666;">Casa Reservada:</strong><br>
                        <strong><?php echo htmlspecialchars($reserva['casa_codigo']); ?></strong> - 
                        <?php echo htmlspecialchars($reserva['casa_nome']); ?>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <strong style="color: #666;">Localização:</strong><br>
                        <?php echo htmlspecialchars($reserva['localizacao_nome']); ?><br>
                        <small style="color: #666;"><?php echo htmlspecialchars($reserva['cidade']); ?></small>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <strong style="color: #666;">Tipologia:</strong><br>
                        <?php echo htmlspecialchars($reserva['tipologia']); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Histórico de Check-in/Check-out -->
        <?php if ($reserva['data_checkin_realizado'] || $reserva['data_checkout_realizado']): ?>
        <div style="margin-top: 30px;">
            <h4 style="margin-bottom: 20px; color: #333;">Histórico de Operações</h4>
            
            <?php if ($reserva['data_checkin_realizado']): ?>
            <div style="margin-bottom: 15px; padding: 10px; background-color: #d1ecf1; border-radius: 5px;">
                <strong style="color: #0c5460;">✅ Check-in Realizado:</strong><br>
                <?php echo date('d/m/Y H:i:s', strtotime($reserva['data_checkin_realizado'])); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($reserva['data_checkout_realizado']): ?>
            <div style="margin-bottom: 15px; padding: 10px; background-color: #d4edda; border-radius: 5px;">
                <strong style="color: #155724;">🚪 Check-out Realizado:</strong><br>
                <?php echo date('d/m/Y H:i:s', strtotime($reserva['data_checkout_realizado'])); ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Informações do Sistema -->
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <small style="color: #666;">
                <strong>Data da Reserva:</strong> <?php echo date('d/m/Y H:i:s', strtotime($reserva['data_reserva'])); ?><br>
                <strong>ID Único:</strong> <?php echo md5($reserva['id'] . $reserva['data_reserva']); ?>
            </small>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
