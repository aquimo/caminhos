# Sistema de Gestão de Casas para Hospedagem

Sistema completo em PHP para gestão de casas de hospedagem, desenvolvido com padrão MVC e compatível com WAMP.

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

### Gestão de Casas
- Cadastro de casas com informações detalhadas
- Gestão de localizações e condomínios
- Upload de imagens
- Controle de estado (disponível, ocupado, manutenção)
- Preços dinâmicos (diário, semanal, mensal)

### Sistema de Reservas
- Criação e gestão de reservas
- Verificação automática de disponibilidade
- Processo de check-in e check-out
- Cálculo automático de valores
- Cancelamento de reservas

### Relatórios Financeiros
- Receitas por período
- Análise por método de pagamento
- Pagamentos pendentes
- Exportação para CSV

### Relatórios de Ocupação
- Taxa de ocupação por casa
- Análise por localização
- Receitas por ocupação
- Exportação para CSV

## 📋 Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor Apache (WAMP recomendado)
- Extensões PHP: PDO, PDO_MYSQL, JSON, GD

## 🛠️ Instalação

### 1. Configurar Base de Dados

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

### 4. Acesso ao Sistema

1. Inicie o WAMP
2. Acesse: `http://localhost/caminhos/`
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
│       └── casas/             # Imagens das casas
├── config/
│   └── database.php           # Configuração da BD
├── controllers/               # Controladores MVC
│   ├── AuthController.php
│   ├── CasaController.php
│   ├── DashboardController.php
│   ├── RelatorioController.php
│   ├── ReservaController.php
│   └── UtilizadorController.php
├── helpers/                   # Funções auxiliares
│   ├── auth_helper.php
│   ├── session_helper.php
│   └── url_helper.php
├── models/                    # Modelos MVC
│   ├── CasaModel.php
│   ├── ClienteModel.php
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
│   ├── relatorios/
│   ├── reservas/
│   └── utilizadores/
├── index.php                  # Ponto de entrada
└── database.sql               # Script da BD
```

## 🔧 Configuração Adicional

### Criar Novo Utilizador

1. Acesse como Gestor Geral
2. Vá em "Gestão de Utilizadores"
3. Clique em "Novo Utilizador"
4. Preencha os dados e selecione o perfil adequado

### Adicionar Casa

1. Vá em "Gestão de Casas"
2. Clique em "Nova Casa"
3. Preencha todas as informações
4. Adicione imagens se desejar

### Criar Reserva

1. Vá em "Gestão de Reservas"
2. Clique em "Nova Reserva"
3. Selecione as datas
4. Escolha uma casa disponível
5. Selecione o cliente
6. Confirme a reserva

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
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --info-color: #17a2b8;
}
```

### Adicionar Novos Perfis

1. Edite a tabela `utilizadores` na BD
2. Adicione novo valor ao ENUM do campo `perfil`
3. Atualize os helpers de autenticação
4. Ajuste as permissões nos controladores

## 🚨 Solução de Problemas

### Erro de Conexão
- Verifique as credenciais em `config/database.php`
- Certifique-se de que o MySQL está em execução
- Verifique se a base de dados existe

### Upload de Imagens
- Verifique as permissões da pasta `assets/images/casas/`
- Certifique-se de que a extensão GD do PHP está ativa

### Performance
- Adicione índices às tabelas da BD
- Configure cache adequado
- Otimize as imagens antes do upload

## 📞 Suporte

Para dúvidas ou problemas:
1. Verifique os logs de erro do PHP
2. Consulte a documentação
3. Verifique a configuração do WAMP

## 📝 Licença

Este sistema foi desenvolvido para fins educativos e pode ser modificado conforme necessidade.

---

**Desenvolvido com PHP puro, MySQL, HTML5, CSS3 e JavaScript**
**Compatível com WAMP (Apache + MySQL + PHP)**
