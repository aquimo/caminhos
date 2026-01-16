<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?php echo htmlspecialchars($hospede['nome']); ?></h3>
        <div class="btn-group">
            <a href="index.php?route=hospedes" class="btn btn-secondary">
                <i>←</i> Voltar
            </a>
            <?php if ($hospede['estado'] == 'ativo'): ?>
                <a href="index.php?route=hospedes/checkout&id=<?php echo $hospede['id']; ?>" class="btn btn-warning">
                    <i>🚪</i> Realizar Check-out
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Informações Pessoais -->
            <div>
                <h4 style="margin-bottom: 20px; color: #333;">Informações Pessoais</h4>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Nome Completo:</strong><br>
                    <span style="font-size: 1.1rem;"><?php echo htmlspecialchars($hospede['nome']); ?></span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Procedência:</strong><br>
                    <span><?php echo htmlspecialchars($hospede['procedencia']); ?></span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Endereço:</strong><br>
                    <span><?php echo nl2br(htmlspecialchars($hospede['endereco'])); ?></span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Contacto:</strong><br>
                    <span><?php echo htmlspecialchars($hospede['contacto']); ?></span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Previsão de Permanência:</strong><br>
                    <span><?php echo htmlspecialchars($hospede['previsao_permanencia']); ?></span>
                </div>
            </div>
            
            <!-- Informações da Hospedagem -->
            <div>
                <h4 style="margin-bottom: 20px; color: #333;">Informações da Hospedagem</h4>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Casa Alocada:</strong><br>
                    <span style="font-size: 1.1rem; font-weight: 600;">
                        <?php echo htmlspecialchars($hospede['casa_codigo'] . ' - ' . $hospede['casa_nome']); ?>
                    </span>
                    <br>
                    <small style="color: #666;"><?php echo htmlspecialchars($hospede['localizacao_nome']); ?></small>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Data de Check-in:</strong><br>
                    <span><?php echo date('d/m/Y H:i', strtotime($hospede['data_checkin'])); ?></span>
                </div>
                
                <?php if ($hospede['data_checkout']): ?>
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Data de Check-out:</strong><br>
                    <span><?php echo date('d/m/Y H:i', strtotime($hospede['data_checkout'])); ?></span>
                </div>
                <?php endif; ?>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Estado:</strong><br>
                    <?php
                    $badgeClass = 'badge-success';
                    $badgeText = 'Ativo';
                    
                    switch ($hospede['estado']) {
                        case 'checkout_realizado':
                            $badgeClass = 'badge-secondary';
                            $badgeText = 'Checkout Realizado';
                            break;
                        case 'cancelado':
                            $badgeClass = 'badge-danger';
                            $badgeText = 'Cancelado';
                            break;
                    }
                    ?>
                    <span class="badge <?php echo $badgeClass; ?>" style="font-size: 0.9rem;">
                        <?php echo $badgeText; ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Informações Financeiras -->
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <h4 style="margin-bottom: 20px; color: #333;">Informações Financeiras</h4>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Valor a Pagar:</strong><br>
                    <span style="font-size: 1.3rem; font-weight: 600; color: #dc3545;">
                        <?php echo formatCurrency($hospede['valor_pagar']); ?>
                    </span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Valor Pago:</strong><br>
                    <span style="font-size: 1.3rem; font-weight: 600; color: <?php echo ($hospede['valor_pago'] >= $hospede['valor_pagar']) ? '#28a745' : '#dc3545'; ?>;">
                        <?php echo formatCurrency($hospede['valor_pago']); ?>
                    </span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Saldo Pendente:</strong><br>
                    <span style="font-size: 1.3rem; font-weight: 600; color: <?php echo ($hospede['valor_pago'] < $hospede['valor_pagar']) ? '#dc3545' : '#28a745'; ?>;">
                        <?php echo formatCurrency($hospede['valor_pagar'] - $hospede['valor_pago']); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Dados da Conta -->
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <h4 style="margin-bottom: 20px; color: #333;">Dados da Conta Corrente</h4>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Número da Conta:</strong><br>
                    <span style="font-family: monospace; font-size: 1.1rem;">
                        <?php echo htmlspecialchars($hospede['numero_conta']); ?>
                    </span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Nome da Conta:</strong><br>
                    <span><?php echo htmlspecialchars($hospede['nome_conta']); ?></span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Senha de Acesso:</strong><br>
                    <span style="font-family: monospace; background-color: #f8f9fa; padding: 5px 10px; border-radius: 3px;">
                        [Configurada no registo]
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Informações de Sistema -->
        <?php if ($hospede['utilizador_checkin_nome'] || $hospede['utilizador_checkout_nome']): ?>
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <h4 style="margin-bottom: 20px; color: #333;">Registo de Operações</h4>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <?php if ($hospede['utilizador_checkin_nome']): ?>
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Check-in realizado por:</strong><br>
                    <span><?php echo htmlspecialchars($hospede['utilizador_checkin_nome']); ?></span>
                    <br>
                    <small style="color: #666;"><?php echo date('d/m/Y H:i', strtotime($hospede['data_criacao'])); ?></small>
                </div>
                <?php endif; ?>
                
                <?php if ($hospede['utilizador_checkout_nome']): ?>
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Check-out realizado por:</strong><br>
                    <span><?php echo htmlspecialchars($hospede['utilizador_checkout_nome']); ?></span>
                    <br>
                    <small style="color: #666;"><?php echo date('d/m/Y H:i', strtotime($hospede['data_checkout'])); ?></small>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
