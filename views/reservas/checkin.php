<div class="card">
    <div class="card-header">
        <h3 class="card-title">Check-ins Pendentes</h3>
        <a href="<?php echo UrlHelper::base('reservas'); ?>" class="btn btn-secondary">
            <i>←</i> Voltar
        </a>
    </div>
    <div class="card-body">
        <?php if (empty($pendentes)): ?>
            <div style="text-align: center; padding: 40px; color: #666;">
                <div style="font-size: 3rem; margin-bottom: 10px;">✅</div>
                <p>Nenhum check-in pendente para hoje.</p>
                <small style="color: #666;">Todas as reservas confirmadas estão em dia.</small>
            </div>
        <?php else: ?>
            <div style="margin-bottom: 20px;">
                <div class="alert alert-info">
                    <strong>📋 Check-ins Pendentes:</strong> Existem <?php echo count($pendentes); ?> reserva(s) aguardando check-in.
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px;">
                <?php foreach ($pendentes as $reserva): ?>
                    <div class="card" style="border-left: 4px solid #28a745;">
                        <div class="card-body">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                                <div>
                                    <h5 style="margin: 0; color: #333;"><?php echo htmlspecialchars($reserva['casa_codigo']); ?></h5>
                                    <small style="color: #666;"><?php echo htmlspecialchars($reserva['casa_nome']); ?></small>
                                </div>
                                <span class="badge badge-success">Pendente</span>
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
                                    👥 <?php echo $reserva['capacidade']; ?> pessoa(s)
                                </small>
                            </div>
                            
                            <div style="margin-bottom: 15px;">
                                <strong style="color: #666;">Valor:</strong><br>
                                <span style="font-size: 1.1rem; font-weight: 600; color: #28a745;">
                                    €<?php echo number_format($reserva['valor_total'], 2, ',', ' '); ?>
                                </span>
                                <?php if ($reserva['valor_pago'] < $reserva['valor_total']): ?>
                                    <br><small style="color: #dc3545;">
                                        Pendente: €<?php echo number_format($reserva['valor_total'] - $reserva['valor_pago'], 2, ',', ' '); ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($reserva['observacoes']): ?>
                            <div style="margin-bottom: 15px;">
                                <strong style="color: #666;">Observações:</strong><br>
                                <small style="color: #666;"><?php echo nl2br(htmlspecialchars($reserva['observacoes'])); ?></small>
                            </div>
                            <?php endif; ?>
                            
                            <div style="display: flex; gap: 10px;">
                                <form method="POST" action="<?php echo UrlHelper::base('reservas/processarCheckin'); ?>" style="flex: 1;">
                                    <input type="hidden" name="id" value="<?php echo $reserva['id']; ?>">
                                    <button type="submit" class="btn btn-success" style="width: 100%;" 
                                            onclick="return confirm('Confirmar check-in para <?php echo htmlspecialchars($reserva['cliente_nome']); ?>?')">
                                        <i>✅</i> Fazer Check-in
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
