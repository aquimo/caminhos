<?php
/**
 * Controlador de Casas
 * Sistema de Gestão de Casas para Hospedagem
 * 
 * @author Oscar Massangaia
 * @institution Universidade Aberta ISCED
 * @course Engenharia Informática
 * @version 1.0
 */

require_once 'models/CasaModel.php';
require_once 'models/LocalizacaoModel.php';
require_once 'helpers/auth_helper.php';
require_once 'helpers/url_helper.php';
require_once 'helpers/session_helper.php';

class CasaController {
    
    /**
     * Listar casas
     */
    public function index() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission(['casas', 'gestor_geral']);
        
        $casaModel = new CasaModel();
        $localizacaoModel = new LocalizacaoModel();
        
        // Filtros
        $localizacaoId = $_GET['localizacao_id'] ?? null;
        $tipologia = $_GET['tipologia'] ?? null;
        $estado = $_GET['estado'] ?? null;
        $search = $_GET['search'] ?? null;
        
        // Obter casas
        if ($search) {
            $casas = $casaModel->search($search, $localizacaoId, $tipologia);
        } else {
            $casas = $casaModel->getAll();
        }
        
        // Filtrar resultados
        if ($localizacaoId) {
            $casas = array_filter($casas, function($casa) use ($localizacaoId) {
                return $casa['localizacao_id'] == $localizacaoId;
            });
        }
        
        if ($tipologia) {
            $casas = array_filter($casas, function($casa) use ($tipologia) {
                return $casa['tipologia'] === $tipologia;
            });
        }
        
        if ($estado) {
            $casas = array_filter($casas, function($casa) use ($estado) {
                return $casa['estado'] === $estado;
            });
        }
        
        $localizacoes = $localizacaoModel->getAll();
        $tipologias = $casaModel->getTipologias();
        
        $page_title = 'Gestão de Casas';
        ob_start();
        include 'views/casas/index.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Mostrar formulário de criação
     */
    public function criar() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission(['casas', 'gestor_geral']);
        
        $localizacaoModel = new LocalizacaoModel();
        $localizacoes = $localizacaoModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processCreate();
        } else {
            $this->showCreateForm($localizacoes);
        }
    }
    
    /**
     * Processar criação de casa
     */
    private function processCreate() {
        $data = $_POST;
        
        // Validação
        $errors = $this->validateCasaData($data);
        if (!empty($errors)) {
            SessionHelper::setFlash('error', implode('<br>', $errors));
            $this->showCreateForm([], $data);
            return;
        }
        
        $casaModel = new CasaModel();
        
        // Verificar se código já existe
        if ($casaModel->codigoExists($data['codigo'])) {
            SessionHelper::setFlash('error', 'O código da casa já está em uso.');
            $this->showCreateForm([], $data);
            return;
        }
        
        // Processar comodidades
        if (isset($data['comodidades']) && is_array($data['comodidades'])) {
            $data['comodidades'] = $data['comodidades'];
        } else {
            $data['comodidades'] = [];
        }
        
        // Processar imagens
        $data['imagens'] = [];
        if (isset($_FILES['imagens']) && !empty($_FILES['imagens']['name'][0])) {
            $data['imagens'] = $this->processUploadImagens($_FILES['imagens']);
        }
        
        if ($casaModel->create($data)) {
            SessionHelper::setFlash('success', 'Casa criada com sucesso!');
            UrlHelper::redirect('casas');
        } else {
            SessionHelper::setFlash('error', 'Erro ao criar casa. Tente novamente.');
            $this->showCreateForm([], $data);
        }
    }
    
    /**
     * Mostrar formulário de criação
     */
    private function showCreateForm($localizacoes = [], $data = []) {
        if (empty($localizacoes)) {
            $localizacaoModel = new LocalizacaoModel();
            $localizacoes = $localizacaoModel->getAll();
        }
        
        $page_title = 'Criar Casa';
        ob_start();
        include 'views/casas/criar.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Mostrar formulário de edição
     */
    public function editar() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission(['casas', 'gestor_geral']);
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            SessionHelper::setFlash('error', 'ID da casa não especificado.');
            UrlHelper::redirect('casas');
        }
        
        $casaModel = new CasaModel();
        $localizacaoModel = new LocalizacaoModel();
        
        $casa = $casaModel->findById($id);
        if (!$casa) {
            SessionHelper::setFlash('error', 'Casa não encontrada.');
            UrlHelper::redirect('casas');
        }
        
        $localizacoes = $localizacaoModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEdit($id);
        } else {
            $this->showEditForm($casa, $localizacoes);
        }
    }
    
    /**
     * Processar edição de casa
     */
    private function processEdit($id) {
        $data = $_POST;
        
        // Validação
        $errors = $this->validateCasaData($data, $id);
        if (!empty($errors)) {
            SessionHelper::setFlash('error', implode('<br>', $errors));
            UrlHelper::redirect('casas/editar?id=' . $id);
            return;
        }
        
        $casaModel = new CasaModel();
        
        // Verificar se código já existe (excluindo esta casa)
        if ($casaModel->codigoExists($data['codigo'], $id)) {
            SessionHelper::setFlash('error', 'O código da casa já está em uso.');
            UrlHelper::redirect('casas/editar?id=' . $id);
            return;
        }
        
        // Processar comodidades
        if (isset($data['comodidades']) && is_array($data['comodidades'])) {
            $data['comodidades'] = $data['comodidades'];
        } else {
            $data['comodidades'] = [];
        }
        
        // Processar imagens novas
        if (isset($_FILES['imagens']) && !empty($_FILES['imagens']['name'][0])) {
            $novasImagens = $this->processUploadImagens($_FILES['imagens']);
            $casaAtual = $casaModel->findById($id);
            $imagensExistentes = json_decode($casaAtual['imagens'] ?? '[]', true);
            $data['imagens'] = array_merge($imagensExistentes, $novasImagens);
        }
        
        if ($casaModel->update($id, $data)) {
            SessionHelper::setFlash('success', 'Casa atualizada com sucesso!');
            UrlHelper::redirect('casas');
        } else {
            SessionHelper::setFlash('error', 'Erro ao atualizar casa. Tente novamente.');
            UrlHelper::redirect('casas/editar?id=' . $id);
        }
    }
    
    /**
     * Mostrar formulário de edição
     */
    private function showEditForm($casa, $localizacoes) {
        // Decodificar JSON
        $casa['comodidades'] = json_decode($casa['comodidades'] ?? '[]', true);
        $casa['imagens'] = json_decode($casa['imagens'] ?? '[]', true);
        
        $page_title = 'Editar Casa';
        ob_start();
        include 'views/casas/editar.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Apagar casa
     */
    public function apagar() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission(['casas', 'gestor_geral']);
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            SessionHelper::setFlash('error', 'ID da casa não especificado.');
            UrlHelper::redirect('casas');
        }
        
        $casaModel = new CasaModel();
        
        if ($casaModel->delete($id)) {
            SessionHelper::setFlash('success', 'Casa apagada com sucesso!');
        } else {
            SessionHelper::setFlash('error', 'Não foi possível apagar a casa. Verifique se existem reservas associadas.');
        }
        
        UrlHelper::redirect('casas');
    }
    
    /**
     * Ver detalhes da casa
     */
    public function ver() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission(['casas', 'gestor_geral']);
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            SessionHelper::setFlash('error', 'ID da casa não especificado.');
            UrlHelper::redirect('casas');
        }
        
        $casaModel = new CasaModel();
        $casa = $casaModel->findById($id);
        
        if (!$casa) {
            SessionHelper::setFlash('error', 'Casa não encontrada.');
            UrlHelper::redirect('casas');
        }
        
        // Decodificar JSON
        $casa['comodidades'] = json_decode($casa['comodidades'] ?? '[]', true);
        $casa['imagens'] = json_decode($casa['imagens'] ?? '[]', true);
        
        $page_title = 'Detalhes da Casa';
        ob_start();
        include 'views/casas/ver.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Validar dados da casa
     */
    private function validateCasaData($data, $excludeId = null) {
        $errors = [];
        
        // Código
        if (empty($data['codigo'])) {
            $errors[] = 'O código é obrigatório.';
        } elseif (!preg_match('/^[A-Z0-9]{3,10}$/', $data['codigo'])) {
            $errors[] = 'O código deve conter apenas letras maiúsculas e números (3-10 caracteres).';
        }
        
        // Localização
        if (empty($data['localizacao_id'])) {
            $errors[] = 'A localização é obrigatória.';
        }
        
        // Nome
        if (empty($data['nome'])) {
            $errors[] = 'O nome é obrigatório.';
        } elseif (strlen($data['nome']) > 100) {
            $errors[] = 'O nome não pode ter mais de 100 caracteres.';
        }
        
        // Tipologia
        if (empty($data['tipologia'])) {
            $errors[] = 'A tipologia é obrigatória.';
        }
        
        // Capacidade
        if (empty($data['capacidade'])) {
            $errors[] = 'A capacidade é obrigatória.';
        } elseif (!is_numeric($data['capacidade']) || $data['capacidade'] < 1 || $data['capacidade'] > 20) {
            $errors[] = 'A capacidade deve ser um número entre 1 e 20.';
        }
        
        // Área
        if (!empty($data['area_decimal']) && (!is_numeric($data['area_decimal']) || $data['area_decimal'] <= 0)) {
            $errors[] = 'A área deve ser um número positivo.';
        }
        
        // Preços
        if (empty($data['preco_diario'])) {
            $errors[] = 'O preço diário é obrigatório.';
        } elseif (!is_numeric($data['preco_diario']) || $data['preco_diario'] <= 0) {
            $errors[] = 'O preço diário deve ser um número positivo.';
        }
        
        if (!empty($data['preco_semanal']) && (!is_numeric($data['preco_semanal']) || $data['preco_semanal'] <= 0)) {
            $errors[] = 'O preço semanal deve ser um número positivo.';
        }
        
        if (!empty($data['preco_mensal']) && (!is_numeric($data['preco_mensal']) || $data['preco_mensal'] <= 0)) {
            $errors[] = 'O preço mensal deve ser um número positivo.';
        }
        
        // Estado
        if (empty($data['estado'])) {
            $errors[] = 'O estado é obrigatório.';
        } elseif (!in_array($data['estado'], ['disponivel', 'ocupado', 'manutencao', 'indisponivel'])) {
            $errors[] = 'Estado inválido.';
        }
        
        return $errors;
    }
    
    /**
     * Processar upload de imagens
     */
    private function processUploadImagens($files) {
        $imagens = [];
        $uploadDir = '../assets/images/casas/';
        
        // Criar diretório se não existir
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $fileName = time() . '_' . $files['name'][$i];
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($files['tmp_name'][$i], $targetPath)) {
                    $imagens[] = 'assets/images/casas/' . $fileName;
                }
            }
        }
        
        return $imagens;
    }
}
?>
