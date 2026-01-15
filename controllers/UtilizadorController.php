<?php
/**
 * Controlador de Utilizadores
 */

require_once 'models/UtilizadorModel.php';
require_once 'helpers/auth_helper.php';
require_once 'helpers/url_helper.php';

class UtilizadorController {
    
    /**
     * Listar utilizadores
     */
    public function index() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission('gestor_geral');
        
        $utilizadorModel = new UtilizadorModel();
        
        // Filtros
        $perfil = $_GET['perfil'] ?? null;
        $search = $_GET['search'] ?? null;
        
        // Obter utilizadores
        if ($search) {
            $utilizadores = $utilizadorModel->search($search);
        } else {
            $utilizadores = $utilizadorModel->getAll();
        }
        
        // Filtrar por perfil
        if ($perfil) {
            $utilizadores = array_filter($utilizadores, function($utilizador) use ($perfil) {
                return $utilizador['perfil'] === $perfil;
            });
        }
        
        $page_title = 'Gestão de Utilizadores';
        ob_start();
        include 'views/utilizadores/index.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Mostrar formulário de criação
     */
    public function criar() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission('gestor_geral');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processCreate();
        } else {
            $this->showCreateForm();
        }
    }
    
    /**
     * Processar criação de utilizador
     */
    private function processCreate() {
        $data = $_POST;
        
        // Validação
        $errors = $this->validateUtilizadorData($data);
        if (!empty($errors)) {
            SessionHelper::setFlash('error', implode('<br>', $errors));
            $this->showCreateForm($data);
            return;
        }
        
        $utilizadorModel = new UtilizadorModel();
        
        // Verificar se email já existe
        if ($utilizadorModel->emailExists($data['email'])) {
            SessionHelper::setFlash('error', 'O email já está em uso.');
            $this->showCreateForm($data);
            return;
        }
        
        if ($utilizadorModel->create($data)) {
            SessionHelper::setFlash('success', 'Utilizador criado com sucesso!');
            UrlHelper::redirect('utilizadores');
        } else {
            SessionHelper::setFlash('error', 'Erro ao criar utilizador. Tente novamente.');
            $this->showCreateForm($data);
        }
    }
    
    /**
     * Mostrar formulário de criação
     */
    private function showCreateForm($data = []) {
        $page_title = 'Criar Utilizador';
        ob_start();
        include 'views/utilizadores/criar.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Mostrar formulário de edição
     */
    public function editar() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission('gestor_geral');
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            SessionHelper::setFlash('error', 'ID do utilizador não especificado.');
            UrlHelper::redirect('utilizadores');
        }
        
        $utilizadorModel = new UtilizadorModel();
        $utilizador = $utilizadorModel->findById($id);
        
        if (!$utilizador) {
            SessionHelper::setFlash('error', 'Utilizador não encontrado.');
            UrlHelper::redirect('utilizadores');
        }
        
        // Não permitir editar o próprio perfil se não for gestor geral
        if ($utilizador['id'] == AuthHelper::getUserId() && !AuthHelper::hasProfile('gestor_geral')) {
            SessionHelper::setFlash('error', 'Não pode editar o seu próprio perfil.');
            UrlHelper::redirect('utilizadores');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEdit($id);
        } else {
            $this->showEditForm($utilizador);
        }
    }
    
    /**
     * Processar edição de utilizador
     */
    private function processEdit($id) {
        $data = $_POST;
        
        // Validação
        $errors = $this->validateUtilizadorData($data, $id);
        if (!empty($errors)) {
            SessionHelper::setFlash('error', implode('<br>', $errors));
            UrlHelper::redirect('utilizadores/editar?id=' . $id);
            return;
        }
        
        $utilizadorModel = new UtilizadorModel();
        
        // Verificar se email já existe (excluindo este utilizador)
        if ($utilizadorModel->emailExists($data['email'], $id)) {
            SessionHelper::setFlash('error', 'O email já está em uso.');
            UrlHelper::redirect('utilizadores/editar?id=' . $id);
            return;
        }
        
        // Se senha não foi preenchida, remover do array
        if (empty($data['senha'])) {
            unset($data['senha']);
        }
        
        if ($utilizadorModel->update($id, $data)) {
            SessionHelper::setFlash('success', 'Utilizador atualizado com sucesso!');
            
            // Se o utilizador editou o próprio perfil, atualizar sessão
            if ($id == AuthHelper::getUserId()) {
                $utilizadorAtualizado = $utilizadorModel->findById($id);
                AuthHelper::login($utilizadorAtualizado);
            }
            
            UrlHelper::redirect('utilizadores');
        } else {
            SessionHelper::setFlash('error', 'Erro ao atualizar utilizador. Tente novamente.');
            UrlHelper::redirect('utilizadores/editar?id=' . $id);
        }
    }
    
    /**
     * Mostrar formulário de edição
     */
    private function showEditForm($utilizador) {
        $page_title = 'Editar Utilizador';
        ob_start();
        include 'views/utilizadores/editar.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Apagar utilizador
     */
    public function apagar() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission('gestor_geral');
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            SessionHelper::setFlash('error', 'ID do utilizador não especificado.');
            UrlHelper::redirect('utilizadores');
        }
        
        // Não permitir apagar o próprio utilizador
        if ($id == AuthHelper::getUserId()) {
            SessionHelper::setFlash('error', 'Não pode apagar o seu próprio utilizador.');
            UrlHelper::redirect('utilizadores');
        }
        
        $utilizadorModel = new UtilizadorModel();
        
        if ($utilizadorModel->delete($id)) {
            SessionHelper::setFlash('success', 'Utilizador apagado com sucesso!');
        } else {
            SessionHelper::setFlash('error', 'Erro ao apagar utilizador. Tente novamente.');
        }
        
        UrlHelper::redirect('utilizadores');
    }
    
    /**
     * Ver detalhes do utilizador
     */
    public function ver() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission('gestor_geral');
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            SessionHelper::setFlash('error', 'ID do utilizador não especificado.');
            UrlHelper::redirect('utilizadores');
        }
        
        $utilizadorModel = new UtilizadorModel();
        $utilizador = $utilizadorModel->findById($id);
        
        if (!$utilizador) {
            SessionHelper::setFlash('error', 'Utilizador não encontrado.');
            UrlHelper::redirect('utilizadores');
        }
        
        $page_title = 'Detalhes do Utilizador';
        ob_start();
        include 'views/utilizadores/ver.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Validar dados do utilizador
     */
    private function validateUtilizadorData($data, $excludeId = null) {
        $errors = [];
        
        // Nome
        if (empty($data['nome'])) {
            $errors[] = 'O nome é obrigatório.';
        } elseif (strlen($data['nome']) < 3 || strlen($data['nome']) > 100) {
            $errors[] = 'O nome deve ter entre 3 e 100 caracteres.';
        }
        
        // Email
        if (empty($data['email'])) {
            $errors[] = 'O email é obrigatório.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido.';
        }
        
        // Senha (apenas na criação ou se for preenchida na edição)
        if (!$excludeId || !empty($data['senha'])) {
            if (empty($data['senha'])) {
                $errors[] = 'A senha é obrigatória.';
            } elseif (strlen($data['senha']) < 6) {
                $errors[] = 'A senha deve ter pelo menos 6 caracteres.';
            }
        }
        
        // Perfil
        if (empty($data['perfil'])) {
            $errors[] = 'O perfil é obrigatório.';
        } elseif (!in_array($data['perfil'], ['gestor_geral', 'secretaria', 'contabilidade', 'gestor_condominios'])) {
            $errors[] = 'Perfil inválido.';
        }
        
        // Estado
        if (isset($data['ativo']) && !in_array($data['ativo'], [0, 1])) {
            $errors[] = 'Estado inválido.';
        }
        
        return $errors;
    }
}
?>
