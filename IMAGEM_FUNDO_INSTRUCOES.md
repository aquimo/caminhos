# Instruções para Imagem de Fundo da Seção Hero

## 📐 Especificações da Imagem

### **Caminho para Colocar:**
```
assets/images/home/hero-background.jpg
```

### **Dimensões Recomendadas:**
- **Largura:** 1920 pixels
- **Altura:** 800 pixels
- **Proporção:** 16:7 (panorâmica)
- **Resolução:** 72 DPI (web)
- **Formato:** JPG ou PNG

### **📝 Descrição do Conteúdo:**

**Tema Sugerido:**
- Foto panorâmica do Bairro Ferroviário
- Vista aérea das casas/residências
- Imagem de Inhambane ou Tofo
- Paisagem urbana com edifícios residenciais
- Foto das casas do bairro ao pôr do sol

**Características Visuais:**
- Cores vibrantes e bem iluminadas
- Boa qualidade e nitidez
- Composição equilibrada
- Espaço para texto sobreposto
- Horizonte nivelado

### **🎨 Tratamento Visual:**

**Efeito Aplicado pelo CSS:**
- **Overlay:** Verde semi-transparente (rgba(11, 91, 54, 0.7))
- **Função:** Garante legibilidade do texto branco
- **Resultado:** Imagem com filtro verde do sistema

**Recomendações:**
- Imagem com boa luminosidade
- Contraste adequado
- Sem elementos muito escuros
- Foco nas edificações

### **🔧 Implementação Técnica:**

**CSS Aplicado:**
```css
.hero-section {
    background-image: url('assets/images/home/hero-background.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 20px;
    position: relative;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(11, 91, 54, 0.7);
    border-radius: 20px;
    z-index: 1;
}
```

**Efeito Visual:**
- Imagem ocupa todo o hero section
- Overlay verde garante legibilidade
- Texto branco sobreposto
- Bordas arredondadas

### **📱 Responsividade:**

**Comportamento em Diferentes Telas:**
- **Desktop:** Imagem panorâmica completa
- **Tablet:** Recorte central da imagem
- **Mobile:** Seção central da imagem
- **Background-size: cover** garante preenchimento

### **🎯 Resultado Esperado:**

**Visual Final:**
- Imagem de fundo com overlay verde
- Texto "Bem Vindos ao Bairro Ferroviário" em branco
- Descrição em branco semi-transparente
- Efeito profissional e moderno
- Integração com cores do sistema

### **⚠️ Notas Importantes:**

1. **Backup:** Mantenha cópia da imagem original
2. **Otimização:** Comprima para web (sem perder qualidade)
3. **Teste:** Visualize em diferentes dispositivos
4. **Alternativa:** Tenha imagem de fallback caso falhe
5. **Copyright:** Use imagens com direitos autorais liberados

### **🚀 Passos para Implementação:**

1. **Criar/Editar** a imagem nas dimensões especificadas
2. **Salvar** na pasta `assets/images/home/`
3. **Nomear** como `hero-background.jpg`
4. **Testar** a visualização no navegador
5. **Ajustar** se necessário (brilho, contraste)

### **💡 Sugestões de Fonte de Imagens:**

- **Fotografias próprias** do Bairro Ferroviário
- **Banco de imagens** (Unsplash, Pexels, Pixabay)
- **Fotógrafo local** de Inhambane
- **Drone** para vistas aéreas
- **Arquivo CFM** (se disponível)

---

**Autor:** Oscar Massangaia  
**Instituição:** Universidade Aberta ISCED  
**Curso:** Engenharia Informática  
**Versão:** 1.0
