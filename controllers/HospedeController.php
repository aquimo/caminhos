<?php
/**
 * Controlador de Hóspedes
 * Sistema de Gestão de Casas para Hospedagem
 */

require_once 'models/HospedeModel.php';
require_once 'helpers/auth_helper.php';
require_once 'helpers/session_helper.php';
require_once 'helpers/url_helper.php';

class HospedeController {
    private $hospedeModel;
    
    public function __construct() {
        $this->hospedeModel = new HospedeModel();
    }
    
    /**
     * Listar hóspedes
     */
    public function index() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission(['secretaria', 'gestor_geral']);
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $hospedes = $this->hospedeModel->getAll($limit, $offset);
        $total = $this->hospedeModel->count();
        $totalPages = ceil($total / $limit);
        $estatisticas = $this->hospedeModel->getEstatisticas();
        
        $page_title = 'Hóspedes';
        ob_start();
        include 'views/hospedes/index.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Mostrar formulário de registo
     */
    public function criar() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission(['secretaria', 'gestor_geral']);
        
        $casas_disponiveis = $this->hospedeModel->getCasasDisponiveis();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nome' => $_POST['nome'] ?? '',
                'procedencia' => $_POST['procedencia'] ?? '',
                'endereco' => $_POST['endereco'] ?? '',
                'contacto' => $_POST['contacto'] ?? '',
                'previsao_permanencia' => $_POST['previsao_permanencia'] . ' dias',
                'data_checkin' => $_POST['data_checkin'] . ' ' . date('H:i:s'),
                'casa_id' => $_POST['casa_id'] ?? '',
                'senha' => password_hash($_POST['senha'] ?? '', PASSWORD_DEFAULT),
                'numero_conta' => $_POST['numero_conta'] ?? '',
                'nome_conta' => $_POST['nome_conta'] ?? '',
                'valor_pagar' => $_POST['valor_pagar'] ?? 0,
                'utilizador_checkin' => AuthHelper::getUserId()
            ];
            
            // Validar dados
            $errors = $this->validateData($data);
            
            if (empty($errors)) {
                if ($this->hospedeModel->create($data)) {
                    SessionHelper::setFlash('success', 'Hóspede registado com sucesso!');
                    UrlHelper::redirect('hospedes');
                } else {
                    SessionHelper::setFlash('error', 'Erro ao registar hóspede. Tente novamente.');
                }
            } else {
                SessionHelper::setFlash('error', implode('<br>', $errors));
            }
        }
        
        $page_title = 'Registar Hóspede';
        ob_start();
        include 'views/hospedes/criar.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Ver detalhes do hóspede
     */
    public function ver($id) {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission(['secretaria', 'gestor_geral']);
        
        $hospede = $this->hospedeModel->findById($id);
        
        if (!$hospede) {
            SessionHelper::setFlash('error', 'Hóspede não encontrado.');
            UrlHelper::redirect('hospedes');
        }
        
        $page_title = 'Detalhes do Hóspede';
        ob_start();
        include 'views/hospedes/ver.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Realizar check-out
     */
    public function checkout() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission(['secretaria', 'gestor_geral']);
        
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            SessionHelper::setFlash('error', 'ID do hóspede não especificado.');
            UrlHelper::redirect('hospedes');
        }
        
        $hospede = $this->hospedeModel->findById($id);
        
        if (!$hospede) {
            SessionHelper::setFlash('error', 'Hóspede não encontrado.');
            UrlHelper::redirect('hospedes');
        }
        
        if ($hospede['estado'] !== 'ativo') {
            SessionHelper::setFlash('error', 'Este hóspede já realizou check-out.');
            UrlHelper::redirect('hospedes');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $valor_pago = $_POST['valor_pago'] ?? 0;
            
            // Atualizar valor pago
            $this->hospedeModel->update($id, [
                'valor_pago' => $valor_pago
            ]);
            
            // Realizar checkout
            if ($this->hospedeModel->checkout($id, AuthHelper::getUserId())) {
                // Liberar casa
                $this->liberarCasa($hospede['casa_id']);
                
                SessionHelper::setFlash('success', 'Check-out realizado com sucesso!');
                UrlHelper::redirect('hospedes');
            } else {
                SessionHelper::setFlash('error', 'Erro ao realizar check-out.');
            }
        }
        
        $page_title = 'Check-out de Hóspede';
        ob_start();
        include 'views/hospedes/checkout.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Liberar casa para nova ocupação
     */
    private function liberarCasa($casa_id) {
        $sql = "UPDATE casas SET estado = 'disponivel' WHERE id = ?";
        global $db;
        $stmt = $db->prepare($sql);
        $stmt->execute([$casa_id]);
    }
    
    /**
     * Validar dados do hóspede
     */
    private function validateData($data) {
        $errors = [];
        
        if (empty($data['nome'])) {
            $errors[] = 'O nome é obrigatório.';
        }
        
        if (empty($data['procedencia'])) {
            $errors[] = 'A procedência é obrigatória.';
        }
        
        if (empty($data['endereco'])) {
            $errors[] = 'O endereço é obrigatório.';
        }
        
        if (empty($data['contacto'])) {
            $errors[] = 'O contacto é obrigatório.';
        }
        
        if (empty($data['previsao_permanencia'])) {
            $errors[] = 'O número de dias é obrigatório.';
        }
        
        if (!is_numeric($_POST['previsao_permanencia']) || $_POST['previsao_permanencia'] <= 0) {
            $errors[] = 'O número de dias deve ser um número positivo.';
        }
        
        if (empty($data['data_checkin'])) {
            $errors[] = 'A data de check-in é obrigatória.';
        }
        
        if (empty($data['casa_id'])) {
            $errors[] = 'A seleção da casa é obrigatória.';
        }
        
        if (empty($_POST['senha'])) {
            $errors[] = 'A senha é obrigatória.';
        }
        
        if (empty($data['numero_conta'])) {
            $errors[] = 'O número da conta é obrigatório.';
        }
        
        if (empty($data['nome_conta'])) {
            $errors[] = 'O nome da conta é obrigatório.';
        }
        
        if (!is_numeric($data['valor_pagar']) || $data['valor_pagar'] <= 0) {
            $errors[] = 'O valor a pagar deve ser um número positivo.';
        }
        
        return $errors;
    }
}
?>
