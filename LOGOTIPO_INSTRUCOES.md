# Instruções para Logotipo do Bairro Ferroviário

## Especificações do Logotipo

### 📐 Dimensões Recomendadas
- **Tamanho:** 60x60 pixels (para a versão circular no header)
- **Formato:** PNG com transparência
- **Resolução:** 300 DPI (para impressão)
- **Versão vetorial:** SVG ou AI (para escalabilidade)

### 📁 Caminho para Colocação
```
assets/images/logo-bairro-ferroviario.png
```

### 🎨 Design Sugerido
- **Cores:** 
  - Verde principal: #667eea (cor do sistema)
  - Branco: #ffffff (fundo do logo)
  - Secundária: #764ba2 (gradiente)
- **Estilo:** Moderno, limpo e profissional
- **Elementos:** Pode incluir ícones relacionados a:
  - Casas/edifícios
  - Ferrovia (trens/trilhos)
  - Bairro/comunidade

### 📝 Versões Necessárias
1. **Logo Principal:** assets/images/logo-bairro-ferroviario.png
2. **Logo Header:** assets/images/logo-header.png (60x60px)
3. **Favicon:** assets/images/favicon.ico (32x32px)
4. **Logo Completo:** assets/images/logo-completo.png (com nome completo)

### 🔧 Implementação no Código

O logotipo é referenciado nos seguintes locais:

1. **Página Inicial (views/home/index.php):**
   ```html
   <div class="logo">BF</div>
   ```
   - Substituir "BF" pela imagem do logotipo

2. **Layout Principal (views/layouts/main.php):**
   - Atualizar para usar o novo logotipo

### 🚀 Passos para Implementação

1. **Criar o logotipo** nas dimensões especificadas
2. **Salvar na pasta** `assets/images/`
3. **Atualizar o CSS** para usar a imagem em vez do texto
4. **Testar** a visualização em diferentes dispositivos

### 📱 Responsividade
- O logotipo deve ser legível em dispositivos móveis
- Manter proporções em diferentes tamanhos de tela
- Testar em diferentes resoluções

### ⚠️ Notas Importantes
- Manter consistência com as cores do sistema
- Garantir boa legibilidade
- Testar contraste com diferentes fundos
- O logotipo atual é um placeholder (texto "BF")

---

**Autor:** Oscar Massangaia  
**Instituição:** Universidade Aberta ISCED  
**Curso:** Engenharia Informática  
**Versão:** 1.0
