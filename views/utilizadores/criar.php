<div class="card">
    <div class="card-header">
        <h3 class="card-title">Criar Novo Utilizador</h3>
        <a href="<?php echo UrlHelper::base('utilizadores'); ?>" class="btn btn-secondary">
            <i>←</i> Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" id="utilizadorForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="nome" class="form-label">Nome Completo *</label>
                    <input type="text" id="nome" name="nome" class="form-control" 
                           placeholder="Ex: João Silva" required
                           value="<?php echo htmlspecialchars($data['nome'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           placeholder="Ex: joao@exemplo.com" required
                           value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="senha" class="form-label">Senha *</label>
                    <input type="password" id="senha" name="senha" class="form-control" 
                           placeholder="Mínimo 6 caracteres" required minlength="6">
                    <small style="color: #666;">Mínimo 6 caracteres</small>
                </div>
                
                <div class="form-group">
                    <label for="confirmar_senha" class="form-label">Confirmar Senha *</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" 
                           class="form-control" placeholder="Repita a senha" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="perfil" class="form-label">Perfil *</label>
                    <select id="perfil" name="perfil" class="form-control" required>
                        <option value="">Selecione...</option>
                        <option value="gestor_geral" <?php echo (isset($data['perfil']) && $data['perfil'] == 'gestor_geral') ? 'selected' : ''; ?>>
                            Gestor Geral
                        </option>
                        <option value="secretaria" <?php echo (isset($data['perfil']) && $data['perfil'] == 'secretaria') ? 'selected' : ''; ?>>
                            Secretaria
                        </option>
                        <option value="contabilidade" <?php echo (isset($data['perfil']) && $data['perfil'] == 'contabilidade') ? 'selected' : ''; ?>>
                            Contabilidade
                        </option>
                        <option value="gestor_condominios" <?php echo (isset($data['perfil']) && $data['perfil'] == 'gestor_condominios') ? 'selected' : ''; ?>>
                            Gestor de Condomínios
                        </option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Estado</label>
                    <div style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" name="ativo" value="1" checked style="margin-right: 8px;">
                            Ativo
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" name="ativo" value="0" style="margin-right: 8px;">
                            Inativo
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Informações sobre Perfis -->
            <div class="card" style="margin-top: 20px; background-color: #f8f9fa;">
                <div class="card-header">
                    <h5 style="margin: 0; color: #666;">Informações sobre Perfis</h5>
                </div>
                <div class="card-body" style="padding: 15px;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                        <div>
                            <strong style="color: #dc3545;">Gestor Geral:</strong>
                            <p style="margin: 5px 0; font-size: 0.9rem;">Acesso total a todas as funcionalidades do sistema.</p>
                        </div>
                        <div>
                            <strong style="color: #28a745;">Secretaria:</strong>
                            <p style="margin: 5px 0; font-size: 0.9rem;">Gestão de reservas, check-in e check-out.</p>
                        </div>
                        <div>
                            <strong style="color: #ffc107;">Contabilidade:</strong>
                            <p style="margin: 5px 0; font-size: 0.9rem;">Gestão de pagamentos e relatórios financeiros.</p>
                        </div>
                        <div>
                            <strong style="color: #17a2b8;">Gestor de Condomínios:</strong>
                            <p style="margin: 5px 0; font-size: 0.9rem;">Gestão de casas e localizações.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="btn-group" style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">
                    <i>💾</i> Criar Utilizador
                </button>
                <a href="<?php echo UrlHelper::base('utilizadores'); ?>" class="btn btn-secondary">
                    <i>❌</i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('utilizadorForm').addEventListener('submit', function(e) {
    const senha = document.getElementById('senha').value;
    const confirmarSenha = document.getElementById('confirmar_senha').value;
    
    if (senha !== confirmarSenha) {
        e.preventDefault();
        alert('As senhas não coincidem. Por favor, verifique.');
        return false;
    }
    
    if (senha.length < 6) {
        e.preventDefault();
        alert('A senha deve ter pelo menos 6 caracteres.');
        return false;
    }
});
</script>
