<div class="card">
    <div class="card-header">
        <h3 class="card-title">Criar Nova Casa</h3>
        <a href="<?php echo UrlHelper::base('casas'); ?>" class="btn btn-secondary">
            <i>←</i> Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data" id="casaForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="codigo" class="form-label">Código *</label>
                    <input type="text" id="codigo" name="codigo" class="form-control" 
                           placeholder="Ex: CASA001" required
                           value="<?php echo htmlspecialchars($data['codigo'] ?? ''); ?>"
                           pattern="[A-Z0-9]{3,10}" title="Apenas letras maiúsculas e números (3-10 caracteres)">
                    <small style="color: #666;">Apenas letras maiúsculas e números</small>
                </div>
                
                <div class="form-group">
                    <label for="localizacao_id" class="form-label">Localização *</label>
                    <select id="localizacao_id" name="localizacao_id" class="form-control" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($localizacoes as $localizacao): ?>
                            <option value="<?php echo $localizacao['id']; ?>"
                                    <?php echo (isset($data['localizacao_id']) && $data['localizacao_id'] == $localizacao['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($localizacao['nome'] . ' - ' . $localizacao['cidade']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="nome" class="form-label">Nome da Casa *</label>
                <input type="text" id="nome" name="nome" class="form-control" 
                       placeholder="Ex: Apartamento T1 Sol Poente" required
                       value="<?php echo htmlspecialchars($data['nome'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea id="descricao" name="descricao" class="form-control" rows="4" 
                          placeholder="Descreva as características da casa..."><?php echo htmlspecialchars($data['descricao'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="tipologia" class="form-label">Tipologia *</label>
                    <select id="tipologia" name="tipologia" class="form-control" required>
                        <option value="">Selecione...</option>
                        <option value="Studio" <?php echo (isset($data['tipologia']) && $data['tipologia'] == 'Studio') ? 'selected' : ''; ?>>Studio</option>
                        <option value="T0" <?php echo (isset($data['tipologia']) && $data['tipologia'] == 'T0') ? 'selected' : ''; ?>>T0</option>
                        <option value="T1" <?php echo (isset($data['tipologia']) && $data['tipologia'] == 'T1') ? 'selected' : ''; ?>>T1</option>
                        <option value="T2" <?php echo (isset($data['tipologia']) && $data['tipologia'] == 'T2') ? 'selected' : ''; ?>>T2</option>
                        <option value="T3" <?php echo (isset($data['tipologia']) && $data['tipologia'] == 'T3') ? 'selected' : ''; ?>>T3</option>
                        <option value="T4" <?php echo (isset($data['tipologia']) && $data['tipologia'] == 'T4') ? 'selected' : ''; ?>>T4</option>
                        <option value="T5+" <?php echo (isset($data['tipologia']) && $data['tipologia'] == 'T5+') ? 'selected' : ''; ?>>T5+</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="capacidade" class="form-label">Capacidade *</label>
                    <input type="number" id="capacidade" name="capacidade" class="form-control" 
                           placeholder="Ex: 4" required min="1" max="20"
                           value="<?php echo htmlspecialchars($data['capacidade'] ?? ''); ?>">
                    <small style="color: #666;">Número de pessoas</small>
                </div>
                
                <div class="form-group">
                    <label for="area_decimal" class="form-label">Área (m²)</label>
                    <input type="number" id="area_decimal" name="area_decimal" class="form-control" 
                           placeholder="Ex: 65.5" step="0.01" min="1"
                           value="<?php echo htmlspecialchars($data['area_decimal'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="preco_diario" class="form-label">Preço Diário (MZN) *</label>
                    <input type="number" id="preco_diario" name="preco_diario" class="form-control" 
                           placeholder="Ex: 75.00" required step="0.01" min="0.01"
                           value="<?php echo htmlspecialchars($data['preco_diario'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="preco_semanal" class="form-label">Preço Semanal (MZN)</label>
                    <input type="number" id="preco_semanal" name="preco_semanal" class="form-control" 
                           placeholder="Ex: 450.00" step="0.01" min="0.01"
                           value="<?php echo htmlspecialchars($data['preco_semanal'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="preco_mensal" class="form-label">Preço Mensal (MZN)</label>
                    <input type="number" id="preco_mensal" name="preco_mensal" class="form-control" 
                           placeholder="Ex: 1500.00" step="0.01" min="0.01"
                           value="<?php echo htmlspecialchars($data['preco_mensal'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="estado" class="form-label">Estado *</label>
                <select id="estado" name="estado" class="form-control" required>
                    <option value="disponivel" <?php echo (isset($data['estado']) && $data['estado'] == 'disponivel') ? 'selected' : ''; ?>>
                        Disponível
                    </option>
                    <option value="ocupado" <?php echo (isset($data['estado']) && $data['estado'] == 'ocupado') ? 'selected' : ''; ?>>
                        Ocupado
                    </option>
                    <option value="manutencao" <?php echo (isset($data['estado']) && $data['estado'] == 'manutencao') ? 'selected' : ''; ?>>
                        Manutenção
                    </option>
                    <option value="indisponivel" <?php echo (isset($data['estado']) && $data['estado'] == 'indisponivel') ? 'selected' : ''; ?>>
                        Indisponível
                    </option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Comodidades</label>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                    <?php
                    $comodidadesDisponiveis = [
                        'WiFi', 'TV', 'Ar Condicionado', 'Aquecimento Central',
                        'Máquina de Lavar Louça', 'Máquina de Lavar Roupa', 'Frigorífico',
                        'Micro-ondas', 'Fogão', 'Varanda', 'Terraço', 'Estacionamento',
                        'Piscina', 'Ginásio', 'Segurança 24h', 'Elevador'
                    ];
                    
                    $comodidadesSelecionadas = $data['comodidades'] ?? [];
                    ?>
                    
                    <?php foreach ($comodidadesDisponiveis as $comodidade): ?>
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="comodidades[]" value="<?php echo $comodidade; ?>"
                                   <?php echo in_array($comodidade, $comodidadesSelecionadas) ? 'checked' : ''; ?>
                                   style="margin-right: 8px;">
                            <?php echo htmlspecialchars($comodidade); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="imagens" class="form-label">Imagens</label>
                <input type="file" id="imagens" name="imagens[]" class="form-control" 
                       multiple accept="image/*">
                <small style="color: #666;">Pode selecionar várias imagens (JPG, PNG, GIF)</small>
                
                <div id="imagePreview" style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 10px;"></div>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <i>💾</i> Criar Casa
                </button>
                <a href="<?php echo UrlHelper::base('casas'); ?>" class="btn btn-secondary">
                    <i>❌</i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Preview de imagens
document.getElementById('imagens').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    const files = Array.from(e.target.files);
    
    files.forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.style.cssText = 'position: relative; width: 100px; height: 100px; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width: 100%; height: 100%; object-fit: cover;';
                
                div.appendChild(img);
                preview.appendChild(div);
            };
            
            reader.readAsDataURL(file);
        }
    });
});

// Auto-calcular preços sugeridos
document.getElementById('preco_diario').addEventListener('input', function() {
    const diario = parseFloat(this.value);
    if (!isNaN(diario) && diario > 0) {
        // Sugerir preço semanal (6x diário + 1 dia grátis)
        document.getElementById('preco_semanal').value = (diario * 6).toFixed(2);
        
        // Sugerir preço mensal (25x diário + 5 dias grátis)
        document.getElementById('preco_mensal').value = (diario * 25).toFixed(2);
    }
});
</script>
