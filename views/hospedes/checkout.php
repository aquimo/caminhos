<div class="card">
    <div class="card-header">
        <h3 class="card-title">Check-out de Hóspede</h3>
        <a href="index.php?route=hospedes" class="btn btn-secondary">
            <i>←</i> Voltar
        </a>
    </div>
    <div class="card-body">
        <?php if (SessionHelper::hasFlash('error')): ?>
            <div class="alert alert-error">
                <?php echo SessionHelper::getFlash('error'); ?>
            </div>
        <?php endif; ?>
        
        <?php if (SessionHelper::hasFlash('success')): ?>
            <div class="alert alert-success">
                <?php echo SessionHelper::getFlash('success'); ?>
            </div>
        <?php endif; ?>
        
        <!-- Informações do Hóspede -->
        <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 30px;">
            <h4 style="margin-bottom: 20px; color: #333;">Dados do Hóspede</h4>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <strong>Nome:</strong> <?php echo htmlspecialchars($hospede['nome']); ?><br>
                    <strong>Procedência:</strong> <?php echo htmlspecialchars($hospede['procedencia']); ?><br>
                    <strong>Contacto:</strong> <?php echo htmlspecialchars($hospede['contacto']); ?>
                </div>
                
                <div>
                    <strong>Casa:</strong> <?php echo htmlspecialchars($hospede['casa_codigo'] . ' - ' . $hospede['casa_nome']); ?><br>
                    <strong>Check-in:</strong> <?php echo date('d/m/Y H:i', strtotime($hospede['data_checkin'])); ?><br>
                    <strong>Permanência:</strong> <?php echo htmlspecialchars($hospede['previsao_permanencia']); ?>
                </div>
            </div>
        </div>
        
        <!-- Formulário de Check-out -->
        <form method="POST" id="checkoutForm">
            <div style="background-color: #fff3cd; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                <h4 style="margin-bottom: 20px; color: #333;">Resumo Financeiro</h4>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div style="text-align: center;">
                        <strong style="color: #666;">Valor a Pagar</strong><br>
                        <span style="font-size: 1.5rem; font-weight: 600; color: #dc3545;">
                            <?php echo formatCurrency($hospede['valor_pagar']); ?>
                        </span>
                    </div>
                    
                    <div style="text-align: center;">
                        <strong style="color: #666;">Valor Pago Anterior</strong><br>
                        <span style="font-size: 1.2rem; font-weight: 600; color: #28a745;">
                            <?php echo formatCurrency($hospede['valor_pago']); ?>
                        </span>
                    </div>
                    
                    <div style="text-align: center;">
                        <strong style="color: #666;">Saldo a Pagar</strong><br>
                        <span style="font-size: 1.5rem; font-weight: 600; color: #dc3545;">
                            <?php echo formatCurrency($hospede['valor_pagar'] - $hospede['valor_pago']); ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div style="background-color: #d1ecf1; padding: 20px; border-radius: 5px;">
                <h4 style="margin-bottom: 20px; color: #333;">Pagamento no Check-out</h4>
                
                <div class="form-group">
                    <label for="valor_pago" class="form-label">Valor Pago no Check-out (MZN) *</label>
                    <input type="number" id="valor_pago" name="valor_pago" class="form-control" 
                           placeholder="Digite o valor recebido" required step="0.01" min="0"
                           value="<?php echo htmlspecialchars($_POST['valor_pago'] ?? ($hospede['valor_pagar'] - $hospede['valor_pago'])); ?>">
                    <small style="color: #666; display: block; margin-top: 5px;">
                        Valor total que o hóspede está a pagar no momento do check-out
                    </small>
                </div>
                
                <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
                    <h5 style="margin-bottom: 15px; color: #333;">Resumo do Pagamento</h5>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <strong>Total da Hospedagem:</strong><br>
                            <span><?php echo formatCurrency($hospede['valor_pagar']); ?></span>
                        </div>
                        
                        <div>
                            <strong>Total Pago:</strong><br>
                            <span id="totalPago" style="font-weight: 600; color: #28a745;">
                                <?php echo formatCurrency($hospede['valor_pago'] + (float)($_POST['valor_pago'] ?? 0)); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <strong style="font-size: 1.2rem;">Saldo Final:</strong>
                            <span id="saldoFinal" style="font-size: 1.3rem; font-weight: 600; color: #28a745;">
                                <?php 
                                $valor_pago_checkout = (float)($_POST['valor_pago'] ?? 0);
                                $total_pago = $hospede['valor_pago'] + $valor_pago_checkout;
                                $saldo = $hospede['valor_pagar'] - $total_pago;
                                echo formatCurrency($saldo);
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <button type="submit" class="btn btn-warning" style="padding: 12px 40px;">
                    <i>🚪</i> Realizar Check-out
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const valorPagoInput = document.getElementById('valor_pago');
    const totalPagoSpan = document.getElementById('totalPago');
    const saldoFinalSpan = document.getElementById('saldoFinal');
    
    const valorPagar = <?php echo $hospede['valor_pagar']; ?>;
    const valorPagoAnterior = <?php echo $hospede['valor_pago']; ?>;
    
    function atualizarResumo() {
        const valorPagoCheckout = parseFloat(valorPagoInput.value) || 0;
        const totalPago = valorPagoAnterior + valorPagoCheckout;
        const saldo = valorPagar - totalPago;
        
        totalPagoSpan.textContent = formatCurrency(totalPago);
        saldoFinalSpan.textContent = formatCurrency(saldo);
        
        // Atualizar cores
        if (saldo <= 0) {
            saldoFinalSpan.style.color = '#28a745';
        } else {
            saldoFinalSpan.style.color = '#dc3545';
        }
    }
    
    valorPagoInput.addEventListener('input', atualizarResumo);
    
    // Função de formatação de moeda
    function formatCurrency(valor) {
        return valor.toLocaleString('pt-MZ', {
            style: 'currency',
            currency: 'MZN',
            minimumFractionDigits: 2
        }).replace('MZN', '') + ' MZN';
    }
    
    // Calcular automaticamente o valor restante
    valorPagoInput.value = (valorPagar - valorPagoAnterior).toFixed(2);
    atualizarResumo();
});
</script>
