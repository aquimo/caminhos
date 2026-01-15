<div class="card">
    <div class="card-header">
        <h3 class="card-title">Criar Nova Reserva</h3>
        <a href="<?php echo UrlHelper::base('reservas'); ?>" class="btn btn-secondary">
            <i>←</i> Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" id="reservaForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="data_checkin" class="form-label">Data de Check-in *</label>
                    <input type="date" id="data_checkin" name="data_checkin" class="form-control" 
                           required data-min-today
                           value="<?php echo htmlspecialchars($data['data_checkin'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="data_checkout" class="form-label">Data de Check-out *</label>
                    <input type="date" id="data_checkout" name="data_checkout" class="form-control" 
                           required
                           value="<?php echo htmlspecialchars($data['data_checkout'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="casa_id" class="form-label">Casa *</label>
                <select id="casa_id" name="casa_id" class="form-control" required>
                    <option value="">Selecione primeiro as datas</option>
                </select>
                <small style="color: #666;">As casas disponíveis aparecerão após selecionar as datas</small>
            </div>
            
            <div class="form-group">
                <label for="cliente_id" class="form-label">Cliente *</label>
                <select id="cliente_id" name="cliente_id" class="form-control" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?php echo $cliente['id']; ?>"
                                <?php echo (isset($data['cliente_id']) && $data['cliente_id'] == $cliente['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cliente['nome'] . ' - ' . $cliente['email']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small style="color: #666;">
                    <a href="#" onclick="alert('Funcionalidade de criar cliente rápido será implementada em breve.')" style="color: #667eea;">
                        + Criar novo cliente
                    </a>
                </small>
            </div>
            
            <!-- Informações da Casa Selecionada -->
            <div id="casaInfo" style="display: none; margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
                <h5 style="margin: 0 0 10px 0; color: #333;">Informações da Casa</h5>
                <div id="casaDetails"></div>
            </div>
            
            <!-- Cálculo do Valor -->
            <div id="valorInfo" style="display: none; margin: 20px 0; padding: 15px; background-color: #e8f5e8; border-radius: 5px;">
                <h5 style="margin: 0 0 10px 0; color: #333;">Cálculo do Valor</h5>
                <div id="valorDetails"></div>
            </div>
            
            <div class="form-group">
                <label for="observacoes" class="form-label">Observações</label>
                <textarea id="observacoes" name="observacoes" class="form-control" rows="4" 
                          placeholder="Observações sobre a reserva..."><?php echo htmlspecialchars($data['observacoes'] ?? ''); ?></textarea>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i>💾</i> Criar Reserva
                </button>
                <a href="<?php echo UrlHelper::base('reservas'); ?>" class="btn btn-secondary">
                    <i>❌</i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
let casasDisponiveis = [];

// Carregar casas disponíveis quando as datas mudam
document.getElementById('data_checkin').addEventListener('change', carregarCasasDisponiveis);
document.getElementById('data_checkout').addEventListener('change', carregarCasasDisponiveis);

function carregarCasasDisponiveis() {
    const dataCheckin = document.getElementById('data_checkin').value;
    const dataCheckout = document.getElementById('data_checkout').value;
    const casaSelect = document.getElementById('casa_id');
    const casaInfo = document.getElementById('casaInfo');
    const valorInfo = document.getElementById('valorInfo');
    
    // Limpar seleções anteriores
    casaSelect.innerHTML = '<option value="">Selecione...</option>';
    casaInfo.style.display = 'none';
    valorInfo.style.display = 'none';
    
    if (!dataCheckin || !dataCheckout) {
        return;
    }
    
    // Validar datas
    if (new Date(dataCheckin) >= new Date(dataCheckout)) {
        alert('A data de check-out deve ser posterior à data de check-in.');
        return;
    }
    
    // Mostrar loading
    casaSelect.innerHTML = '<option value="">A carregar...</option>';
    
    // Fazer requisição AJAX para obter casas disponíveis
    fetch(`<?php echo UrlHelper::base('reservas/getCasasDisponiveis'); ?>&data_checkin=${dataCheckin}&data_checkout=${dataCheckout}`)
        .then(response => response.json())
        .then(data => {
            casasDisponiveis = data;
            
            if (data.length === 0) {
                casaSelect.innerHTML = '<option value="">Nenhuma casa disponível para as datas selecionadas</option>';
            } else {
                casaSelect.innerHTML = '<option value="">Selecione uma casa...</option>';
                data.forEach(casa => {
                    const option = document.createElement('option');
                    option.value = casa.id;
                    option.textContent = `${casa.codigo} - ${casa.nome} (${parseFloat(casa.preco_diario).toFixed(2)} MZN/noite)`;
                    casaSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            casaSelect.innerHTML = '<option value="">Erro ao carregar casas</option>';
        });
}

// Mostrar informações da casa quando selecionada
document.getElementById('casa_id').addEventListener('change', function() {
    const casaId = this.value;
    const casaInfo = document.getElementById('casaInfo');
    const valorInfo = document.getElementById('valorInfo');
    
    if (!casaId) {
        casaInfo.style.display = 'none';
        valorInfo.style.display = 'none';
        return;
    }
    
    const casa = casasDisponiveis.find(c => c.id == casaId);
    if (!casa) return;
    
    // Mostrar informações da casa
    const casaDetails = document.getElementById('casaDetails');
    casaDetails.innerHTML = `
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <strong>Código:</strong> ${casa.codigo}<br>
                <strong>Nome:</strong> ${casa.nome}<br>
                <strong>Localização:</strong> ${casa.localizacao_nome}<br>
                <strong>Tipologia:</strong> ${casa.tipologia}
            </div>
            <div>
                <strong>Capacidade:</strong> ${casa.capacidade} pessoa(s)<br>
                <strong>Preço Diário:</strong> ${parseFloat(casa.preco_diario).toFixed(2)} MZN<br>
                ${casa.preco_semanal ? `<strong>Preço Semanal:</strong> ${parseFloat(casa.preco_semanal).toFixed(2)} MZN<br>` : ''}
                ${casa.preco_mensal ? `<strong>Preço Mensal:</strong> ${parseFloat(casa.preco_mensal).toFixed(2)} MZN` : ''}
            </div>
        </div>
    `;
    casaInfo.style.display = 'block';
    
    // Calcular valor
    calcularValor(casa);
});

function calcularValor(casa) {
    const dataCheckin = new Date(document.getElementById('data_checkin').value);
    const dataCheckout = new Date(document.getElementById('data_checkout').value);
    const numeroNoites = Math.ceil((dataCheckout - dataCheckin) / (1000 * 60 * 60 * 24));
    
    let valorTotal = 0;
    let detalhesCalculo = '';
    
    // Lógica de preços progressivos
    if (numeroNoites >= 30) {
        const meses = Math.floor(numeroNoites / 30);
        const diasRestantes = numeroNoites % 30;
        valorTotal = (meses * parseFloat(casa.preco_mensal)) + (diasRestantes * parseFloat(casa.preco_diario));
        detalhesCalculo = `
            <strong>${meses} mês(es)</strong> × ${parseFloat(casa.preco_mensal).toFixed(2)} MZN = ${(meses * parseFloat(casa.preco_mensal)).toFixed(2)} MZN<br>
            <strong>${diasRestantes} dia(s)</strong> × ${parseFloat(casa.preco_diario).toFixed(2)} MZN = ${(diasRestantes * parseFloat(casa.preco_diario)).toFixed(2)} MZN
        `;
    } else if (numeroNoites >= 7) {
        const semanas = Math.floor(numeroNoites / 7);
        const diasRestantes = numeroNoites % 7;
        valorTotal = (semanas * parseFloat(casa.preco_semanal)) + (diasRestantes * parseFloat(casa.preco_diario));
        detalhesCalculo = `
            <strong>${semanas} semana(s)</strong> × ${parseFloat(casa.preco_semanal).toFixed(2)} MZN = ${(semanas * parseFloat(casa.preco_semanal)).toFixed(2)} MZN<br>
            <strong>${diasRestantes} dia(s)</strong> × ${parseFloat(casa.preco_diario).toFixed(2)} MZN = ${(diasRestantes * parseFloat(casa.preco_diario)).toFixed(2)} MZN
        `;
    } else {
        valorTotal = numeroNoites * parseFloat(casa.preco_diario);
        detalhesCalculo = `<strong>${numeroNoites} noite(s)</strong> × ${parseFloat(casa.preco_diario).toFixed(2)} MZN = ${valorTotal.toFixed(2)} MZN`;
    }
    
    // Mostrar cálculo
    const valorDetails = document.getElementById('valorDetails');
    valorDetails.innerHTML = `
        ${detalhesCalculo}<br>
        <hr style="margin: 10px 0;">
        <strong>Total (${numeroNoites} noites): ${valorTotal.toFixed(2)} MZN</strong>
    `;
    
    document.getElementById('valorInfo').style.display = 'block';
}

// Validar formulário antes de enviar
document.getElementById('reservaForm').addEventListener('submit', function(e) {
    const dataCheckin = document.getElementById('data_checkin').value;
    const dataCheckout = document.getElementById('data_checkout').value;
    const casaId = document.getElementById('casa_id').value;
    const clienteId = document.getElementById('cliente_id').value;
    
    if (!dataCheckin || !dataCheckout) {
        e.preventDefault();
        alert('Por favor, selecione as datas de check-in e check-out.');
        return;
    }
    
    if (!casaId) {
        e.preventDefault();
        alert('Por favor, selecione uma casa.');
        return;
    }
    
    if (!clienteId) {
        e.preventDefault();
        alert('Por favor, selecione um cliente.');
        return;
    }
    
    // Mostrar loading no botão
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="loading"></span> A processar...';
});
</script>
