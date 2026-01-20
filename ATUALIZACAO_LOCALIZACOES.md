# Atualização de Localizações - Cidade de Inhambane

## 📋 Descrição da Atualização

Substituição de "Cidade-Maputo" para "Cidade de Inhambane" em todo o sistema.

## 🔧 Alterações Realizadas

### **1. Banco de Dados (database.sql)**
- ✅ **Localização:** 'Cidade' → 'Cidade de Inhambane'
- ✅ **Cidade:** 'Maputo' → 'Inhambane'
- ✅ **Endereço:** 'Avenida Principal' → 'Avenida Samora Machel'
- ✅ **Descrição:** Atualizada para referenciar Inhambane
- ✅ **Casas:** Nomes atualizados para "Cidade de Inhambane"

### **2. Página Inicial (views/home/index.php)**
- ✅ **Formulário:** Fallback atualizado para "Cidade de Inhambane"
- ✅ **Contactos:** Mantido "CFM (Maputo)" como contacto geral
- ✅ **Sede:** "Cidade de Inhambane" no endereço

### **3. Formulário Hóspedes (views/hospedes/criar.php)**
- ✅ **Placeholder:** "Ex: Maputo, Matola, etc." → "Ex: Inhambane, Maxixe, etc."

## 📁 Ficheiros Criados

### **1. update_localizacoes.sql**
Script SQL para atualizar dados existentes no banco de dados:
```sql
-- Atualiza localizações, casas, hóspedes e clientes
-- Substitui referências de Maputo para Inhambane
```

### **2. ATUALIZACAO_LOCALIZACOES.md**
Documentação completa da atualização.

## 🗄️ Execução da Atualização

### **Para Novas Instalações:**
1. Use o `database.sql` atualizado
2. As localizações já virão corretas

### **Para Instalações Existentes:**
1. **Backup do banco:**
   ```sql
   mysqldump -u usuario -p caminhos_hospedagem > backup_before_update.sql
   ```

2. **Execute o script de atualização:**
   ```bash
   mysql -u usuario -p caminhos_hospedagem < update_localizacoes.sql
   ```

3. **Verifique os resultados:**
   ```sql
   SELECT * FROM localizacoes;
   SELECT * FROM casas WHERE nome LIKE '%Inhambane%';
   ```

## 📊 Impacto no Sistema

### **Localizações Afetadas:**
- ✅ ID 1: "Cidade de Inhambane" (anteriormente "Cidade")
- ✅ ID 2: "Tofo" (mantido)

### **Casas Atualizadas:**
- ✅ CASA001: "Apartamento T1 Cidade de Inhambane"
- ✅ CASA002: "Apartamento T2 Cidade de Inhambane"
- ✅ CASA003: "Casa de Praia Tofo" (mantido)

### **Dados Dinâmicos:**
- ✅ **Views:** Buscam do banco (já atualizado)
- ✅ **Formulários:** Exibem dados corretos
- ✅ **Relatórios:** Mostram localizações corretas

## 🎯 Verificação Manual

### **1. Página Inicial:**
- [ ] Formulário mostra "Cidade de Inhambane"
- [ ] Contactos mostram endereço correto

### **2. Sistema Administrativo:**
- [ ] Listagem de casas mostra localizações corretas
- [ ] Formulários exibem opções corretas
- [ ] Relatórios mostram dados atualizados

### **3. Banco de Dados:**
- [ ] Tabela `localizacoes` com dados corretos
- [ ] Tabela `casas` com nomes atualizados
- [ ] Tabela `hóspedes` sem referências a Maputo

## 🔄 Comportamento Esperado

### **Com Dados no Banco:**
- Sistema exibe "Cidade de Inhambane - Inhambane"
- Casas mostram nomes corretos
- Formulários funcionam com dados reais

### **Sem Dados no Banco:**
- Fallback mostra "Cidade de Inhambane"
- Sistema continua funcional
- Usuário pode selecionar localizações

## ⚠️ Notas Importantes

1. **Backup:** Sempre faça backup antes de executar scripts SQL
2. **Teste:** Verifique todas as funcionalidades após atualização
3. **Cache:** Limpe cache do navegador se necessário
4. **Sessões:** Faça logout/login para recarregar dados

## 🚀 Próximos Passos

1. **Executar** script SQL em bancos existentes
2. **Verificar** funcionamento de todas as views
3. **Testar** formulários e relatórios
4. **Documentar** quaisquer ajustes necessários

---

**Autor:** Oscar Massangaia  
**Instituição:** Universidade Aberta ISCED  
**Curso:** Engenharia Informática  
**Versão:** 1.0  
**Data:** 20/01/2026
