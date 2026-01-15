<?php
/**
 * Controlador de Autenticação
 */

require_once 'models/UtilizadorModel.php';
require_once 'helpers/auth_helper.php';
require_once 'helpers/url_helper.php';

class AuthController {
    
    /**
     * Mostrar formulário de login
     */
    public function login() {
        // Se já estiver autenticado, redirecionar para dashboard
        if (AuthHelper::isLoggedIn()) {
            UrlHelper::redirect('dashboard');
        }
        
        // Se for POST, tentar fazer login
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processLogin();
        } else {
            $this->showLoginForm();
        }
    }
    
    /**
     * Processar login
     */
    private function processLogin() {
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        
        // Validação básica
        if (empty($email) || empty($senha)) {
            SessionHelper::setFlash('error', 'Por favor, preencha todos os campos.');
            $this->showLoginForm();
            return;
        }
        
        // Validar formato do email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            SessionHelper::setFlash('error', 'Email inválido.');
            $this->showLoginForm();
            return;
        }
        
        $utilizadorModel = new UtilizadorModel();
        $utilizador = $utilizadorModel->findByEmail($email);
        
        // Verificar se utilizador existe e está ativo
        if (!$utilizador || !$utilizador['ativo']) {
            SessionHelper::setFlash('error', 'Credenciais inválidas ou utilizador inativo.');
            $this->showLoginForm();
            return;
        }
        
        // Verificar senha
        if (!password_verify($senha, $utilizador['senha'])) {
            SessionHelper::setFlash('error', 'Credenciais inválidas.');
            $this->showLoginForm();
            return;
        }
        
        // Login bem-sucedido
        AuthHelper::login($utilizador);
        
        // Registar log
        $this->registrarLog('login', 'utilizadores', $utilizador['id'], 'Utilizador fez login');
        
        SessionHelper::setFlash('success', 'Bem-vindo, ' . $utilizador['nome'] . '!');
        UrlHelper::redirect('dashboard');
    }
    
    /**
     * Mostrar formulário de login
     */
    private function showLoginForm() {
        $page_title = 'Login';
        ob_start();
        include 'views/auth/login.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Fazer logout
     */
    public function logout() {
        if (AuthHelper::isLoggedIn()) {
            $utilizadorId = AuthHelper::getUserId();
            $this->registrarLog('logout', 'utilizadores', $utilizadorId, 'Utilizador fez logout');
            AuthHelper::logout();
            SessionHelper::setFlash('success', 'Sessão terminada com sucesso.');
        }
        
        UrlHelper::redirect('login');
    }
    
    /**
     * Registar log de ação
     */
    private function registrarLog($acao, $tabela = null, $registoId = null, $descricao = '') {
        global $db;
        
        try {
            $stmt = $db->prepare("
                INSERT INTO logs_sistema (utilizador_id, acao, tabela, registo_id, descricao, ip_address) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
            $utilizadorId = AuthHelper::isLoggedIn() ? AuthHelper::getUserId() : null;
            
            $stmt->execute([$utilizadorId, $acao, $tabela, $registoId, $descricao, $ipAddress]);
        } catch (PDOException $e) {
            // Silently fail para não quebrar a aplicação
            error_log("Erro ao registrar log: " . $e->getMessage());
        }
    }
}
?>
