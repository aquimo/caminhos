<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?php echo htmlspecialchars($utilizador['nome']); ?></h3>
        <div class="btn-group">
            <a href="index.php?route=utilizadores/editar&id=<?php echo $utilizador['id']; ?>" class="btn btn-primary">
                <i>✏️</i> Editar
            </a>
            <a href="index.php?route=utilizadores" class="btn btn-secondary">
                <i>←</i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Informações Pessoais -->
            <div>
                <h4 style="margin-bottom: 20px; color: #333;">Informações Pessoais</h4>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Nome Completo:</strong><br>
                    <span style="font-size: 1.1rem;"><?php echo htmlspecialchars($utilizador['nome']); ?></span>
                    <?php if ($utilizador['id'] == AuthHelper::getUserId()): ?>
                        <span class="badge badge-info" style="font-size: 0.7rem; margin-left: 10px;">Você</span>
                    <?php endif; ?>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Email:</strong><br>
                    <span style="font-size: 1rem;"><?php echo htmlspecialchars($utilizador['email']); ?></span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Perfil:</strong><br>
                    <?php
                    $badgeClass = 'badge-secondary';
                    $perfilText = AuthHelper::getProfileName($utilizador['perfil']);
                    
                    switch ($utilizador['perfil']) {
                        case 'gestor_geral':
                            $badgeClass = 'badge-danger';
                            break;
                        case 'secretaria':
                            $badgeClass = 'badge-success';
                            break;
                        case 'contabilidade':
                            $badgeClass = 'badge-warning';
                            break;
                        case 'gestor_condominios':
                            $badgeClass = 'badge-info';
                            break;
                    }
                    ?>
                    <span class="badge <?php echo $badgeClass; ?>" style="font-size: 0.9rem;">
                        <?php echo $perfilText; ?>
                    </span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Estado:</strong><br>
                    <?php if ($utilizador['ativo']): ?>
                        <span class="badge badge-success">Ativo</span>
                    <?php else: ?>
                        <span class="badge badge-secondary">Inativo</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Informações do Sistema -->
            <div>
                <h4 style="margin-bottom: 20px; color: #333;">Informações do Sistema</h4>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">ID do Utilizador:</strong><br>
                    <span style="font-family: monospace; background: #f8f9fa; padding: 2px 6px; border-radius: 3px;">
                        #<?php echo str_pad($utilizador['id'], 6, '0', STR_PAD_LEFT); ?>
                    </span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Data de Criação:</strong><br>
                    <?php echo date('d/m/Y H:i:s', strtotime($utilizador['data_criacao'])); ?>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Última Atualização:</strong><br>
                    <?php 
                    if ($utilizador['data_atualizacao'] != $utilizador['data_criacao']) {
                        echo date('d/m/Y H:i:s', strtotime($utilizador['data_atualizacao']));
                    } else {
                        echo '<span style="color: #666; font-style: italic;">Nunca atualizado</span>';
                    }
                    ?>
                </div>
                
                <!-- Permissões do Perfil -->
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666;">Permissões do Perfil:</strong><br>
                    <div style="margin-top: 10px;">
                        <?php
                        $permissoes = [];
                        switch ($utilizador['perfil']) {
                            case 'gestor_geral':
                                $permissoes = ['Acesso total ao sistema', 'Gestão de utilizadores', 'Gestão de casas', 'Gestão de reservas', 'Relatórios financeiros', 'Configurações'];
                                break;
                            case 'secretaria':
                                $permissoes = ['Gestão de reservas', 'Check-in e check-out', 'Visualizar casas', 'Visualizar clientes'];
                                break;
                            case 'contabilidade':
                                $permissoes = ['Gestão de pagamentos', 'Relatórios financeiros', 'Visualizar reservas', 'Visualizar casas'];
                                break;
                            case 'gestor_condominios':
                                $permissoes = ['Gestão de casas', 'Gestão de localizações', 'Visualizar reservas', 'Relatórios de ocupação'];
                                break;
                        }
                        
                        foreach ($permissoes as $permissao) {
                            echo '<div style="padding: 3px 0; color: #666;">✓ ' . htmlspecialchars($permissao) . '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Ações Rápidas -->
        <?php if ($utilizador['id'] != AuthHelper::getUserId()): ?>
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <h4 style="margin-bottom: 15px; color: #333;">Ações Rápidas</h4>
            <div class="btn-group">
                <a href="index.php?route=utilizadores/editar&id=<?php echo $utilizador['id']; ?>" class="btn btn-primary">
                    <i>✏️</i> Editar Utilizador
                </a>
                <?php if ($utilizador['ativo']): ?>
                    <button class="btn btn-warning" onclick="confirmarDesativar(<?php echo $utilizador['id']; ?>)">
                        <i>🔒</i> Desativar
                    </button>
                <?php else: ?>
                    <button class="btn btn-success" onclick="confirmarAtivar(<?php echo $utilizador['id']; ?>)">
                        <i>🔓</i> Ativar
                    </button>
                <?php endif; ?>
                <button class="btn btn-danger" onclick="confirmarApagar(<?php echo $utilizador['id']; ?>)">
                    <i>🗑️</i> Apagar
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmarDesativar(id) {
    if (confirm('Tem certeza que deseja desativar este utilizador? Ele não poderá aceder ao sistema.')) {
        window.location.href = 'index.php?route=utilizadores/editar&id=' + id + '&action=desativar';
    }
}

function confirmarAtivar(id) {
    if (confirm('Tem certeza que deseja ativar este utilizador? Ele poderá aceder ao sistema.')) {
        window.location.href = 'index.php?route=utilizadores/editar&id=' + id + '&action=ativar';
    }
}

function confirmarApagar(id) {
    if (confirm('Tem certeza que deseja apagar este utilizador? Esta ação não pode ser desfeita.')) {
        window.location.href = 'index.php?route=utilizadores/apagar&id=' + id;
    }
}
</script>

<style>
@media (max-width: 768px) {
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
