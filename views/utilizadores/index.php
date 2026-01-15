<div class="card">
    <div class="card-header">
        <h3 class="card-title">Gestão de Utilizadores</h3>
        <div class="btn-group">
            <a href="<?php echo UrlHelper::base('utilizadores/criar'); ?>" class="btn btn-primary">
                <i>➕</i> Novo Utilizador
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtros -->
        <form method="GET" class="form-row" style="margin-bottom: 20px;">
            <div class="form-group">
                <label class="form-label">Pesquisar</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Nome ou email..." 
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Perfil</label>
                <select name="perfil" class="form-control">
                    <option value="">Todos</option>
                    <option value="gestor_geral" <?php echo (isset($_GET['perfil']) && $_GET['perfil'] == 'gestor_geral') ? 'selected' : ''; ?>>
                        Gestor Geral
                    </option>
                    <option value="secretaria" <?php echo (isset($_GET['perfil']) && $_GET['perfil'] == 'secretaria') ? 'selected' : ''; ?>>
                        Secretaria
                    </option>
                    <option value="contabilidade" <?php echo (isset($_GET['perfil']) && $_GET['perfil'] == 'contabilidade') ? 'selected' : ''; ?>>
                        Contabilidade
                    </option>
                    <option value="gestor_condominios" <?php echo (isset($_GET['perfil']) && $_GET['perfil'] == 'gestor_condominios') ? 'selected' : ''; ?>>
                        Gestor de Condomínios
                    </option>
                </select>
            </div>
            
            <div class="form-group" style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="<?php echo UrlHelper::base('utilizadores'); ?>" class="btn btn-secondary" style="margin-left: 10px;">Limpar</a>
            </div>
        </form>
        
        <!-- Tabela de Utilizadores -->
        <?php if (empty($utilizadores)): ?>
            <div style="text-align: center; padding: 40px; color: #666;">
                <div style="font-size: 3rem; margin-bottom: 10px;">👥</div>
                <p>Nenhum utilizador encontrado.</p>
                <a href="<?php echo UrlHelper::base('utilizadores/criar'); ?>" class="btn btn-primary">Criar Primeiro Utilizador</a>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table" id="utilizadoresTable">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Perfil</th>
                            <th>Estado</th>
                            <th>Data de Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilizadores as $utilizador): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($utilizador['nome']); ?></strong>
                                    <?php if ($utilizador['id'] == AuthHelper::getUserId()): ?>
                                        <span class="badge badge-info" style="font-size: 0.7rem;">Você</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($utilizador['email']); ?></td>
                                <td>
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
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        <?php echo $perfilText; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($utilizador['ativo']): ?>
                                        <span class="badge badge-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($utilizador['data_criacao'])); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="index.php?route=utilizadores/ver&id=<?php echo $utilizador['id']; ?>" 
                                           class="btn btn-sm btn-secondary" title="Ver Detalhes">
                                            <i>👁️</i>
                                        </a>
                                        <a href="index.php?route=utilizadores/editar&id=<?php echo $utilizador['id']; ?>" 
                                           class="btn btn-sm btn-primary" title="Editar">
                                            <i>✏️</i>
                                        </a>
                                        <?php if ($utilizador['id'] != AuthHelper::getUserId()): ?>
                                            <a href="index.php?route=utilizadores/apagar&id=<?php echo $utilizador['id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Tem certeza que deseja apagar este utilizador?')" 
                                               title="Apagar">
                                                <i>🗑️</i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 20px; text-align: center; color: #666;">
                <small>Total de <?php echo count($utilizadores); ?> utilizador(es)</small>
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
