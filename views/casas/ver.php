<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?php echo htmlspecialchars($casa['nome']); ?></h3>
        <div class="btn-group">
            <a href="index.php?route=casas/editar&id=<?php echo $casa['id']; ?>" class="btn btn-primary">
                <i>✏️</i> Editar
            </a>
            <a href="index.php?route=casas" class="btn btn-secondary">
                <i>←</i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Informações Principais -->
            <div>
                <h4 style="margin-bottom: 20px; color: #333;">Informações Principais</h4>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Código:</strong><br>
                    <span style="font-size: 1.1rem; font-weight: 600;"><?php echo htmlspecialchars($casa['codigo']); ?></span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Localização:</strong><br>
                    <?php echo htmlspecialchars($casa['localizacao_nome']); ?><br>
                    <small style="color: #666;"><?php echo htmlspecialchars($casa['endereco']); ?></small><br>
                    <small style="color: #666;"><?php echo htmlspecialchars($casa['cidade']); ?>, <?php echo htmlspecialchars($casa['codigo_postal'] ?? ''); ?></small>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Tipologia:</strong><br>
                    <?php echo htmlspecialchars($casa['tipologia']); ?>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Capacidade:</strong><br>
                    <?php echo $casa['capacidade']; ?> pessoa(s)
                </div>
                
                <?php if ($casa['area_decimal']): ?>
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Área:</strong><br>
                    <?php echo number_format($casa['area_decimal'], 2, ',', ' '); ?> m²
                </div>
                <?php endif; ?>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Estado:</strong><br>
                    <?php
                    $badgeClass = 'badge-success';
                    $badgeText = 'Disponível';
                    
                    switch ($casa['estado']) {
                        case 'ocupado':
                            $badgeClass = 'badge-danger';
                            $badgeText = 'Ocupado';
                            break;
                        case 'manutencao':
                            $badgeClass = 'badge-warning';
                            $badgeText = 'Manutenção';
                            break;
                        case 'indisponivel':
                            $badgeClass = 'badge-info';
                            $badgeText = 'Indisponível';
                            break;
                    }
                    ?>
                    <span class="badge <?php echo $badgeClass; ?>">
                        <?php echo $badgeText; ?>
                    </span>
                </div>
            </div>
            
            <!-- Preços -->
            <div>
                <h4 style="margin-bottom: 20px; color: #333;">Preços</h4>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Preço Diário:</strong><br>
                    <span style="font-size: 1.2rem; font-weight: 600; color: #28a745;">
                        <?php echo formatCurrency($casa['preco_diario']); ?>
                    </span>
                </div>
                
                <?php if ($casa['preco_semanal']): ?>
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Preço Semanal:</strong><br>
                    <span style="font-size: 1.1rem; font-weight: 600;">
                        <?php echo formatCurrency($casa['preco_semanal']); ?>
                    </span>
                    <small style="color: #666; display: block;">
                        (Economia: <?php echo formatCurrency(($casa['preco_diario'] * 7) - $casa['preco_semanal']); ?>)
                    </small>
                </div>
                <?php endif; ?>
                
                <?php if ($casa['preco_mensal']): ?>
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Preço Mensal:</strong><br>
                    <span style="font-size: 1.1rem; font-weight: 600;">
                        <?php echo formatCurrency($casa['preco_mensal']); ?>
                    </span>
                    <small style="color: #666; display: block;">
                        (Economia: <?php echo formatCurrency(($casa['preco_diario'] * 30) - $casa['preco_mensal']); ?>)
                    </small>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Descrição -->
        <?php if ($casa['descricao']): ?>
        <div style="margin-top: 30px;">
            <h4 style="margin-bottom: 15px; color: #333;">Descrição</h4>
            <p style="line-height: 1.6;"><?php echo nl2br(htmlspecialchars($casa['descricao'])); ?></p>
        </div>
        <?php endif; ?>
        
        <!-- Comodidades -->
        <?php 
        $comodidades = $casa['comodidades'];
        // Se já for array, usa diretamente, senão faz decode do JSON
        if (is_string($comodidades)) {
            $comodidades = json_decode($comodidades, true);
        }
        if (!empty($comodidades)): 
        ?>
        <div style="margin-top: 30px;">
            <h4 style="margin-bottom: 15px; color: #333;">Comodidades</h4>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                <?php foreach ($comodidades as $comodidade): ?>
                    <span class="badge badge-info" style="font-size: 0.9rem; padding: 6px 12px;">
                        <?php echo htmlspecialchars($comodidade); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Imagens -->
        <?php 
        $imagens = $casa['imagens'];
        // Se já for array, usa diretamente, senão faz decode do JSON
        if (is_string($imagens)) {
            $imagens = json_decode($imagens, true);
        }
        if (!empty($imagens)): 
        ?>
        <div style="margin-top: 30px;">
            <h4 style="margin-bottom: 15px; color: #333;">Imagens</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <?php foreach ($imagens as $imagem): ?>
                    <div style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                        <img src="<?php echo UrlHelper::asset($imagem); ?>" 
                             style="width: 100%; height: 200px; object-fit: cover; display: block;"
                             alt="Imagem da casa">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Informações do Sistema -->
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <small style="color: #666;">
                <strong>Criado em:</strong> <?php echo date('d/m/Y H:i', strtotime($casa['data_criacao'])); ?><br>
                <?php if ($casa['data_atualizacao'] != $casa['data_criacao']): ?>
                    <strong>Atualizado em:</strong> <?php echo date('d/m/Y H:i', strtotime($casa['data_atualizacao'])); ?>
                <?php endif; ?>
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
