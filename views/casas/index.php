<div class="card">
    <div class="card-header">
        <h3 class="card-title">Gestão de Casas</h3>
        <div class="btn-group">
            <a href="<?php echo UrlHelper::base('casas/criar'); ?>" class="btn btn-primary">
                <i>➕</i> Nova Casa
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtros -->
        <form method="GET" class="form-row" style="margin-bottom: 20px;">
            <div class="form-group">
                <label class="form-label">Pesquisar</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Nome, código ou descrição..." 
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Localização</label>
                <select name="localizacao_id" class="form-control">
                    <option value="">Todas</option>
                    <?php foreach ($localizacoes as $localizacao): ?>
                        <option value="<?php echo $localizacao['id']; ?>" 
                                <?php echo (isset($_GET['localizacao_id']) && $_GET['localizacao_id'] == $localizacao['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($localizacao['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Tipologia</label>
                <select name="tipologia" class="form-control">
                    <option value="">Todas</option>
                    <?php foreach ($tipologias as $tipologia): ?>
                        <option value="<?php echo $tipologia; ?>" 
                                <?php echo (isset($_GET['tipologia']) && $_GET['tipologia'] == $tipologia) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tipologia); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="disponivel" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'disponivel') ? 'selected' : ''; ?>>
                        Disponível
                    </option>
                    <option value="ocupado" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'ocupado') ? 'selected' : ''; ?>>
                        Ocupado
                    </option>
                    <option value="manutencao" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'manutencao') ? 'selected' : ''; ?>>
                        Manutenção
                    </option>
                    <option value="indisponivel" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'indisponivel') ? 'selected' : ''; ?>>
                        Indisponível
                    </option>
                </select>
            </div>
            
            <div class="form-group" style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="<?php echo UrlHelper::base('casas'); ?>" class="btn btn-secondary" style="margin-left: 10px;">Limpar</a>
            </div>
        </form>
        
        <!-- Tabela de Casas -->
        <?php if (empty($casas)): ?>
            <div style="text-align: center; padding: 40px; color: #666;">
                <div style="font-size: 3rem; margin-bottom: 10px;">🏠</div>
                <p>Nenhuma casa encontrada.</p>
                <a href="<?php echo UrlHelper::base('casas/criar'); ?>" class="btn btn-primary">Criar Primeira Casa</a>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table" id="casasTable">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nome</th>
                            <th>Localização</th>
                            <th>Tipologia</th>
                            <th>Capacidade</th>
                            <th>Preço Diário</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($casas as $casa): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($casa['codigo']); ?></strong></td>
                                <td><?php echo htmlspecialchars($casa['nome']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($casa['localizacao_nome']); ?><br>
                                    <small style="color: #666;"><?php echo htmlspecialchars($casa['cidade']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($casa['tipologia']); ?></td>
                                <td><?php echo $casa['capacidade']; ?> pessoa(s)</td>
                                <td><?php echo formatCurrency($casa['preco_diario']); ?></td>
                                <td>
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
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="index.php?route=casas/ver&id=<?php echo $casa['id']; ?>" 
                                           class="btn btn-sm btn-secondary" title="Ver Detalhes">
                                            <i>👁️</i>
                                        </a>
                                        <a href="index.php?route=casas/editar&id=<?php echo $casa['id']; ?>" 
                                           class="btn btn-sm btn-primary" title="Editar">
                                            <i>✏️</i>
                                        </a>
                                        <a href="index.php?route=casas/apagar&id=<?php echo $casa['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Tem certeza que deseja apagar esta casa?')" 
                                           title="Apagar">
                                            <i>🗑️</i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 20px; text-align: center; color: #666;">
                <small>Total de <?php echo count($casas); ?> casa(s)</small>
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
