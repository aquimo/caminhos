# Sistema de Gestão de Casas para Hospedagem - Bairro Ferroviário

Sistema completo em PHP para gestão de casas de hospedagem, desenvolvido com padrão MVC e compatível com WAMP.

**Autor:** Oscar Massangaia  
**Instituição:** Universidade Aberta ISCED  
**Curso:** Engenharia Informática  
**Versão:** 1.0

## 🌐 Página Inicial Pública

### Website Institucional
- **Design moderno:** One-page website para o Bairro Ferroviário
- **Hero section:** Com imagem de fundo e texto institucional
- **Formulário de disponibilidade:** Busca pública de casas
- **Seção de casas:** Apresentação das tipologias disponíveis
- **Contactos:** Informações completas com múltiplos canais
- **Menu navegacional:** Links suaves para seções da página
- **Cores padrão:** Identidade visual consistente (verde institucional)
- **Logo personalizado:** Espaço para logotipo do Bairro Ferroviário

### Sistema de Disponibilidade Pública
- **Acesso livre:** Verificação de casas sem necessidade de login
- **Filtros avançados:** Localização, datas, tipologia
- **Resultados em tempo real:** Casas realmente disponíveis
- **Interface responsiva:** Funciona em todos os dispositivos
- **Navegação múltipla:** Botões flutuantes e links de saída
- **Redirecionamento inteligente:** Para formulário de reservas

## 🚀 Funcionalidades

### Sistema de Autenticação
- Login seguro com validação de credenciais
- Controle de acesso por perfil de utilizador
- Sessões seguras e logout

### Perfis de Utilizador
1. **Gestor Geral** - Acesso total ao sistema
2. **Secretaria** - Check-in, check-out e gestão de reservas
3. **Contabilidade** - Pagamentos e relatórios financeiros
4. **Gestor de Condomínios** - Gestão de casas por localização

### 🆕 Sistema de Hóspedes
- Cadastro completo de hóspedes com informações detalhadas
- Geração automática de número de conta (6 dígitos)
- Controle de estado (ativo, inativo)
- Associação automática com casas
- Cálculo automático de valores por permanência
- Histórico de estadias e pagamentos

### Gestão de Casas
- Cadastro de casas com informações detalhadas
- Gestão de localizações (Cidade de Inhambane, Tofo)
- Upload de imagens
- Controle de estado (disponível, ocupado, manutenção)
- Preços dinâmicos em Metical (MZN)
- Verificação automática de disponibilidade

### 🔄 Sistema de Reservas Integrado
- **Fluxo Hóspede → Casa → Reserva**
- Seleção de hóspedes já registados
- Opção de criar novo hóspede durante reserva
- Verificação automática de disponibilidade
- Processo de check-in e check-out integrado
- Cálculo automático de valores com preços progressivos
- Cancelamento de reservas
- Atualização automática de estado das casas

### 💰 Sistema Financeiro Completo
- **Moeda padrão:** Metical (MZN)
- **Preços realistas:** Valores adequados ao mercado moçambicano
- **Pagamentos:** Múltiplos métodos (M-Pesa, transferência, etc.)
- **Controlo:** Valores pagos e pendentes
- **Relatórios:** Exportação em CSV

### Relatórios Financeiros
- Receitas por período
- Análise por método de pagamento
- Pagamentos pendentes
- Exportação para CSV

### Relatórios de Ocupação
- Taxa de ocupação por casa
- Análise por localização (Cidade de Inhambane, Tofo)
- Receitas por ocupação
- Exportação para CSV

### 🎨 Interface e Design
- **Design responsivo:** Funciona em desktop, tablet e mobile
- **Cores institucionais:** Verde primário (#0b5b36) e variantes
- **Interface moderna:** Cards, animações suaves, sombras
- **UX intuitiva:** Navegação clara e fluxos lógicos
- **Acessibilidade:** Contraste adequado e navegação por teclado

### 🌍 Localizações Atualizadas
- **Cidade de Inhambane:** Principal localização urbana
- **Tofo:** Localização turística à beira-mar
- **Endereços completos:** Informações detalhadas de contacto
- **Coordenação geográfica:** Sistema integrado de localização

## 📋 Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor Apache (WAMP recomendado)
- Extensões PHP: PDO, PDO_MYSQL, JSON, GD

## 🛠️ Instalação

### 1. Configurar Base de Dados

**Opção A - Nova Instalação Completa:**
1. Importe o ficheiro `database_completo_atualizado.sql` para o seu MySQL:
   ```sql
   mysql -u root -p < database_completo_atualizado.sql
   ```

**Opção B - Instalação Básica:**
1. Importe o ficheiro `database.sql` para o seu MySQL:
   ```sql
   mysql -u root -p < database.sql
   ```

2. Verifique se a base de dados `caminhos_hospedagem` foi criada com todas as tabelas.

### 2. Configurar Conexão

Edite o ficheiro `config/database.php` se necessário:

```php
private $host = 'localhost';
private $db_name = 'caminhos_hospedagem';
private $username = 'root';
private $password = '';
```

### 3. Permissões

Certifique-se de que as seguintes pastas têm permissões de escrita:
- `assets/images/casas/`
- `assets/images/home/`

### 4. Assets Visuais

**Logotipo:**
- Coloque o ficheiro em: `assets/images/logo-bairro-ferroviario.png`
- Dimensão recomendada: 200x80px
- Formato: PNG com fundo transparente

**Imagem de Fundo:**
- Coloque o ficheiro em: `assets/images/home/hero-background.jpg`
- Dimensão recomendada: 1920x800px
- Formato: JPG otimizado para web

### 5. Acesso ao Sistema

**Página Inicial Pública:**
1. Acesse: `http://localhost/caminhos/`
2. Navegue pelo website institucional
3. Use o formulário de disponibilidade pública

**Área Administrativa:**
1. Acesse: `http://localhost/caminhos/`
2. Clique em "Área Administrativa"
3. Faça login com as credenciais padrão:
   - **Email:** `admin@caminhos.pt`
   - **Senha:** `password`

## 📁 Estrutura de Pastas

```
caminhos/
├── assets/
│   ├── css/
│   │   └── style.css          # Estilos principais
│   ├── js/
│   │   └── script.js          # JavaScript principal
│   └── images/
│       ├── casas/             # Imagens das casas
│       └── home/              # 🆕 Imagens da página inicial
├── config/
│   └── database.php           # Configuração da BD
├── controllers/               # Controladores MVC
│   ├── AuthController.php
│   ├── CasaController.php
│   ├── DashboardController.php
│   ├── DisponibilidadeController.php  # 🆕 Controlador público
│   ├── HomeController.php          # 🆕 Controlador da página inicial
│   ├── HospedeController.php  # 🆕 Controlador de hóspedes
│   ├── RelatorioController.php
│   ├── ReservaController.php
│   └── UtilizadorController.php
├── helpers/                   # Funções auxiliares
│   ├── auth_helper.php
│   ├── currency_helper.php     # 🆕 Helper para formatação MZN
│   ├── session_helper.php
│   └── url_helper.php
├── models/                    # Modelos MVC
│   ├── CasaModel.php
│   ├── ClienteModel.php
│   ├── HospedeModel.php       # 🆕 Modelo de hóspedes
│   ├── LocalizacaoModel.php
│   ├── PagamentoModel.php
│   ├── ReservaModel.php
│   └── UtilizadorModel.php
├── views/                     # Views MVC
│   ├── layouts/
│   │   └── main.php          # Layout principal
│   ├── auth/
│   ├── casas/
│   ├── dashboard/
│   ├── disponibilidade/        # 🆕 View pública de disponibilidade
│   ├── home/                 # 🆕 Página inicial pública
│   ├── hospedes/              # 🆕 Views de hóspedes
│   ├── relatorios/
│   ├── reservas/
│   └── utilizadores/
├── index.php                  # Ponto de entrada
├── database.sql               # Script da BD básico
├── database_completo_atualizado.sql  # 🆕 Script completo com dados
└── README.md                # Documentação atualizada
```

## 🔧 Configuração Adicional

### 🆕 Criar Novo Hóspede

1. Acesse como Gestor Geral ou Secretaria
2. Vá em "Hospedagem" → "Hóspedes"
3. Clique em "Novo Hóspede"
4. Preencha os dados:
   - Nome, BI, NUIT, Contacto
   - **Permanência:** Número de dias
   - **Valor a Pagar:** Calculado automaticamente
   - **Número da Conta:** Gerado automaticamente (6 dígitos)
5. Selecione a casa onde ficará hospedado
6. Confirme o registo

### Criar Reserva

1. Vá em "Hospedagem" → "Reservas"
2. Clique em "Nova Reserva"
3. **Selecione o hóspede**:
   - Escolha hóspede já registado, OU
   - Clique em "Adicionar Novo Hóspede" para registo rápido
4. **Selecione as datas** de check-in e check-out
5. **Escolha a casa** disponível (carregada dinamicamente)
6. **Confirme a reserva**:
   - Valor calculado automaticamente
   - Casa marcada como ocupada
   - Reserva associada ao hóspede

### Processo de Check-in/Check-out

1. **Check-in:**
   - Vá em "Hospedagem" → "Check-ins Pendentes"
   - Selecione a reserva
   - Confirme o check-in
   - Casa marcada como ocupada

2. **Check-out:**
   - Vá em "Hospedagem" → "Check-outs Pendentes"
   - Selecione o hóspede
   - Registre o check-out
   - Casa marcada como disponível

### Adicionar Casa

1. Vá em "Gestão de Casas"
2. Clique em "Nova Casa"
3. Preencha todas as informações
4. Adicione imagens se desejar

## 📊 Relatórios

### Financeiros
- Acesse "Relatórios" → "Relatórios Financeiros"
- Filtre por período e método de pagamento
- Exporte para CSV se necessário

### Ocupação
- Acesse "Relatórios" → "Taxa de Ocupação"
- Filtre por mês e localização
- Visualize gráficos e estatísticas

## 🔒 Segurança

- Senhas encriptadas com `password_hash()`
- Validação de inputs do lado do servidor
- Prevenção contra SQL Injection com prepared statements
- Controle de acesso por perfil
- Sessões seguras

## 🌐 Interface Responsiva

O sistema é totalmente responsivo e funciona em:
- Desktop
- Tablet
- Smartphones

## 📝 Personalização

### Alterar Cores

Edite o ficheiro `assets/css/style.css` e modifique as variáveis CSS:

```css
:root {
    --primary-color: #0b5b36;        /* Verde institucional */
    --primary-light: #0a4d2d;       /* Verde escuro */
    --secondary-color: #6c757d;     /* Cinza secundário */
    --success-color: #28a745;        /* Verde sucesso */
    --danger-color: #dc3545;         /* Vermelho erro */
    --warning-color: #ffc107;        /* Amarelo alerta */
    --info-color: #17a2b8;          /* Azul informação */
}
```

### 🆕 Novas Funcionalidades

**Disponibilidade Pública:**
- Acesso em `http://localhost/caminhos/`
- Formulário de busca sem login
- Resultados com filtros avançados
- Redirecionamento para reservas

**Sistema de Hóspedes:**
- Gestão completa de hóspedes
- Contas automáticas de 6 dígitos
- Integração com sistema de reservas

**Moeda Atualizada:**
- Todos os valores em Metical (MZN)
- Preços realistas para mercado moçambicano
- Formatação consistente em todo o sistema

## 🚀 Novidades da Versão 1.0

### ✅ Implementações Recentes
- **Website institucional** completo para Bairro Ferroviário
- **Sistema de disponibilidade pública** sem necessidade de login
- **Sistema integrado de hóspedes** com gestão completa
- **Atualização para Metical (MZN)** em todo o sistema
- **Localizações atualizadas** (Cidade de Inhambane, Tofo)
- **Interface responsiva** com design moderno
- **Botões de navegação** flutuantes e múltiplas saídas
- **Banco de dados completo** com dados realistas

### 📈 Melhorias de UX
- **Formulários intuitivos** com validação em tempo real
- **Navegação suave** com scroll animado
- **Feedback visual** em todas as ações
- **Acessibilidade** melhorada com contraste adequado
- **Performance otimizada** com índices e cache

### 🔧 Melhorias Técnicas
- **Segurança reforçada** contra ataques comuns
- **Código organizado** com padrão MVC consistente
- **Documentação completa** com exemplos práticos
- **Banco de dados otimizado** com índices eficientes

1. Edite a tabela `utilizadores` na BD
2. Adicione novo valor ao ENUM do campo `perfil`
3. Atualize os helpers de autenticação
4. Ajuste as permissões nos controladores

## 🔄 Fluxo de Trabalho Integrado

### Novo Hóspede → Reserva
1. **Registar Hóspede:** Dados completos + casa + cálculo automático
2. **Criar Reserva:** Associar hóspede existente a nova casa
3. **Check-in:** Ativar estadia na casa
4. **Check-out:** Finalizar estadia e liberar casa

### Gestão Automática
- ✅ **Disponibilidade:** Casas marcadas automaticamente
- ✅ **Valores:** Cálculo progressivo (diário/semanal/mensal)
- ✅ **Associação:** Reserva ↔ Hóspede ↔ Casa
- ✅ **Estados:** Atualização automática de estados

## 🚨 Solução de Problemas

### Erro de Conexão
- Verifique as credenciais em `config/database.php`
- Certifique-se de que o MySQL está em execução
- Verifique se a base de dados existe

### Upload de Imagens
- Verifique as permissões da pasta `assets/images/casas/`
- Verifique as permissões da pasta `assets/images/home/`
- Limite máximo: 2MB por imagem

### Sistema de Disponibilidade
- Certifique-se de que as datas são válidas
- Verifique se existem casas disponíveis para o período
- Confirme se a localização está correta

## 📞 Suporte e Contactos

### 🆕 Suporte Técnico
- **Email:** suporte@caminhos.pt
- **Telefone:** +258 29 320 453
- **Horário:** Segunda-Sexta, 8h-17h

### Contactos Institucionais
- **Sede:** Cidade de Inhambane, Av. Samora Machel, Praça dos Trabalhadores
- **Bairro:** Balane-2, Casa nº 49
- **Tel:** (+258) 29 320 453
- **Fax:** (+258) 29 320 822
- **Email Geral:** gci@cfm.co.mz

### Documentação Adicional
- `DISPONIBILIDADE_PUBLICA.md` - Guia do sistema público
- `VALORES_MZN_ATUALIZADOS.md` - Tabela de valores em Metical
- `CORRECOES_EURO_MZN.md` - Histórico de atualizações
- `LOGOTIPO_INSTRUCOES.md` - Especificações do logotipo
- `IMAGEM_FUNDO_INSTRUCOES.md` - Especificações da imagem de fundo

## 🎯 Roadmap Futuro

### Versão 1.1 (Planeado)
- **Sistema de notificações** por email e SMS
- **Mapa interativo** com localização das casas
- **Galeria de fotos** completa para cada casa
- **Sistema de avaliações** e feedback de hóspedes
- **Integração com M-Pesa** para pagamentos automáticos
- **Aplicação mobile** para hóspedes

### Versão 2.0 (Planeado)
- **Multi-idiomas** (Português, Inglês)
- **Sistema de pontos** e programa de fidelidade
- **Integração com Airbnb** e outras plataformas
- **Dashboard avançado** com analytics em tempo real
- **API REST** para integrações externas

---

**Autor:** Oscar Massangaia  
**Instituição:** Universidade Aberta ISCED  
**Curso:** Engenharia Informática  
**Versão:** 1.0  
**Data:** 20/01/2026  
**Licença:** MIT  

**Agradecimentos:** Universidade Aberta ISCED pelo apoio no desenvolvimento deste projeto acadêmico.

**Desenvolvido com PHP puro, MySQL, HTML5, CSS3 e JavaScript**
**Compatível com WAMP (Apache + MySQL + PHP)**
**🆕 Sistema Integrado de Hóspedes e Reservas**

**Autor:** Oscar Massangaia  
**Instituição:** Universidade Aberta ISCED  
**Curso:** Engenharia Informática  
**Versão:** 1.0
