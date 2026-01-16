<div class="card">
    <div class="card-header">
        <h3 class="card-title">Registar Hóspede</h3>
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
        
        <form method="POST" id="hospedeForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="nome" class="form-label">Nome Completo *</label>
                    <input type="text" id="nome" name="nome" class="form-control" 
                           placeholder="Digite o nome completo do hóspede" required
                           value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="procedencia" class="form-label">Procedência *</label>
                    <input type="text" id="procedencia" name="procedencia" class="form-control" 
                           placeholder="Ex: Maputo, Matola, etc." required
                           value="<?php echo htmlspecialchars($_POST['procedencia'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="endereco" class="form-label">Endereço Completo *</label>
                <textarea id="endereco" name="endereco" class="form-control" rows="3" 
                          placeholder="Digite o endereço completo" required><?php echo htmlspecialchars($_POST['endereco'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="contacto" class="form-label">Contacto *</label>
                    <input type="text" id="contacto" name="contacto" class="form-control" 
                           placeholder="Telefone ou email" required
                           value="<?php echo htmlspecialchars($_POST['contacto'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="previsao_permanencia" class="form-label">Número de Dias *</label>
                    <input type="number" id="previsao_permanencia" name="previsao_permanencia" class="form-control" 
                           placeholder="Ex: 3, 7, 15, etc." required min="1"
                           value="<?php echo htmlspecialchars($_POST['previsao_permanencia'] ?? ''); ?>">
                    <small style="color: #666; display: block; margin-top: 5px;">
                        Número de dias que o hóspede ficará hospedado
                    </small>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="data_checkin" class="form-label">Data de Check-in *</label>
                    <input type="datetime-local" id="data_checkin" name="data_checkin" class="form-control" required
                           value="<?php echo htmlspecialchars($_POST['data_checkin'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="casa_id" class="form-label">Casa Disponível *</label>
                    <select id="casa_id" name="casa_id" class="form-control" required>
                        <option value="">Selecione uma casa...</option>
                        <?php foreach ($casas_disponiveis as $casa): ?>
                            <option value="<?php echo $casa['id']; ?>" 
                                    <?php echo (isset($_POST['casa_id']) && $_POST['casa_id'] == $casa['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($casa['codigo'] . ' - ' . $casa['nome']); ?>
                                (<?php echo htmlspecialchars($casa['localizacao_nome']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;">
                <h4 style="margin-bottom: 20px; color: #333;">Dados da Conta Corrente</h4>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="senha" class="form-label">Senha de Acesso *</label>
                        <input type="password" id="senha" name="senha" class="form-control" 
                               placeholder="Senha para o hóspede" required>
                        <small style="color: #666; display: block; margin-top: 5px;">
                            Esta senha será usada pelo hóspede para aceder ao sistema
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="numero_conta" class="form-label">Número da Conta *</label>
                        <input type="text" id="numero_conta" name="numero_conta" class="form-control" 
                               placeholder="Número da conta corrente" required readonly
                               value="<?php echo htmlspecialchars($_POST['numero_conta'] ?? ''); ?>">
                        <small style="color: #666; display: block; margin-top: 5px;">
                            Número gerado automaticamente (6 dígitos)
                        </small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="nome_conta" class="form-label">Nome da Conta *</label>
                    <input type="text" id="nome_conta" name="nome_conta" class="form-control" 
                           placeholder="Nome como aparece na conta" required
                           value="<?php echo htmlspecialchars($_POST['nome_conta'] ?? ''); ?>">
                </div>
            </div>
            
            <div style="background-color: #fff3cd; padding: 20px; border-radius: 5px; margin: 20px 0;">
                <h4 style="margin-bottom: 20px; color: #333;">Valores</h4>
                
                <div class="form-group">
                    <label for="valor_pagar" class="form-label">Valor a Pagar (MZN) *</label>
                    <input type="number" id="valor_pagar" name="valor_pagar" class="form-control" 
                           placeholder="Valor total da hospedagem" required step="0.01" min="0.01"
                           value="<?php echo htmlspecialchars($_POST['valor_pagar'] ?? ''); ?>">
                    <small style="color: #666; display: block; margin-top: 5px;">
                        Será calculado com base na permanência e valores da casa
                    </small>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 40px;">
                    <i>👥</i> Registar Hóspede
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Definir data/hora atual como padrão
    const now = new Date();
    const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000);
    document.getElementById('data_checkin').value = localDateTime.toISOString().slice(0, 16);
    
    // Gerar número de conta automático (6 dígitos)
    function gerarNumeroConta() {
        const numero = Math.floor(100000 + Math.random() * 900000);
        document.getElementById('numero_conta').value = numero.toString();
    }
    
    // Gerar número de conta ao carregar a página
    gerarNumeroConta();
    
    // Calcular valor automaticamente quando selecionar casa ou alterar dias
    const casaSelect = document.getElementById('casa_id');
    const valorInput = document.getElementById('valor_pagar');
    const diasInput = document.getElementById('previsao_permanencia');
    
    // Mapeamento de valores por casa
    const valoresPorCasa = {
        <?php foreach ($casas_disponiveis as $casa): ?>
        '<?php echo $casa['id']; ?>': {
            diario: <?php echo $casa['preco_diario']; ?>,
            semanal: <?php echo $casa['preco_semanal'] ?? 0; ?>,
            mensal: <?php echo $casa['preco_mensal'] ?? 0; ?>
        },
        <?php endforeach; ?>
    };
    
    function calcularValor() {
        const casaId = casaSelect.value;
        const dias = parseInt(diasInput.value) || 0;
        
        if (!casaId || dias <= 0) {
            valorInput.value = '';
            return;
        }
        
        const precos = valoresPorCasa[casaId];
        if (!precos) return;
        
        // Calcular valor com base no número de dias
        let valor = dias * precos.diario;
        
        // Aplicar desconto para permanências mais longas
        if (dias >= 30) {
            // Usar preço mensal para 30+ dias
            const meses = Math.floor(dias / 30);
            const diasRestantes = dias % 30;
            valor = (meses * precos.mensal) + (diasRestantes * precos.diario);
        } else if (dias >= 7) {
            // Usar preço semanal para 7+ dias
            const semanas = Math.floor(dias / 7);
            const diasRestantes = dias % 7;
            valor = (semanas * precos.semanal) + (diasRestantes * precos.diario);
        }
        
        valorInput.value = valor.toFixed(2);
    }
    
    casaSelect.addEventListener('change', calcularValor);
    diasInput.addEventListener('input', calcularValor);
    
    // Preencher automaticamente o nome da conta com o nome do hóspede
    document.getElementById('nome').addEventListener('input', function() {
        const nomeHospede = this.value.trim();
        if (nomeHospede) {
            document.getElementById('nome_conta').value = 'CONTA - ' + nomeHospede.toUpperCase();
        }
    });
});
</script>
