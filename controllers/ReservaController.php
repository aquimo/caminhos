<?php
/**
 * Controlador de Reservas
 */

require_once 'models/ReservaModel.php';
require_once 'models/CasaModel.php';
require_once 'models/ClienteModel.php';
require_once 'helpers/auth_helper.php';
require_once 'helpers/url_helper.php';

class ReservaController {
    
    /**
     * Listar reservas
     */
    public function index() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission('reservas');
        
        $reservaModel = new ReservaModel();
        $casaModel = new CasaModel();
        
        // Filtros
        $estado = $_GET['estado'] ?? null;
        $search = $_GET['search'] ?? null;
        
        // Obter reservas
        if ($search) {
            $reservas = $reservaModel->search($search);
        } else {
            $reservas = $reservaModel->getAll();
        }
        
        // Filtrar por estado
        if ($estado) {
            $reservas = array_filter($reservas, function($reserva) use ($estado) {
                return $reserva['estado'] === $estado;
            });
        }
        
        $page_title = 'Gestão de Reservas';
        ob_start();
        include 'views/reservas/index.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Mostrar formulário de criação
     */
    public function criar() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission('reservas');
        
        $casaModel = new CasaModel();
        $clienteModel = new ClienteModel();
        
        $casas = $casaModel->getAll();
        $clientes = $clienteModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processCreate();
        } else {
            $this->showCreateForm($casas, $clientes);
        }
    }
    
    /**
     * Processar criação de reserva
     */
    private function processCreate() {
        $data = $_POST;
        
        // Validação
        $errors = $this->validateReservaData($data);
        if (!empty($errors)) {
            SessionHelper::setFlash('error', implode('<br>', $errors));
            $this->showCreateForm([], [], $data);
            return;
        }
        
        $reservaModel = new ReservaModel();
        $casaModel = new CasaModel();
        
        // Verificar disponibilidade
        if (!$reservaModel->verificarDisponibilidade($data['casa_id'], $data['data_checkin'], $data['data_checkout'])) {
            SessionHelper::setFlash('error', 'A casa não está disponível para as datas selecionadas.');
            $this->showCreateForm([], [], $data);
            return;
        }
        
        if ($reservaModel->create($data)) {
            // Atualizar estado da casa para ocupado se a reserva começar hoje
            if ($data['data_checkin'] <= date('Y-m-d')) {
                $casaModel->updateEstado($data['casa_id'], 'ocupado');
            }
            
            SessionHelper::setFlash('success', 'Reserva criada com sucesso!');
            UrlHelper::redirect('reservas');
        } else {
            SessionHelper::setFlash('error', 'Erro ao criar reserva. Tente novamente.');
            $this->showCreateForm([], [], $data);
        }
    }
    
    /**
     * Mostrar formulário de criação
     */
    private function showCreateForm($casas = [], $clientes = [], $data = []) {
        if (empty($casas)) {
            $casaModel = new CasaModel();
            $casas = $casaModel->getAll();
        }
        
        if (empty($clientes)) {
            $clienteModel = new ClienteModel();
            $clientes = $clienteModel->getAll();
        }
        
        $page_title = 'Criar Reserva';
        ob_start();
        include 'views/reservas/criar.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Ver detalhes da reserva
     */
    public function ver() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission('reservas');
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            SessionHelper::setFlash('error', 'ID da reserva não especificado.');
            UrlHelper::redirect('reservas');
        }
        
        $reservaModel = new ReservaModel();
        $reserva = $reservaModel->findById($id);
        
        if (!$reserva) {
            SessionHelper::setFlash('error', 'Reserva não encontrada.');
            UrlHelper::redirect('reservas');
        }
        
        $page_title = 'Detalhes da Reserva';
        ob_start();
        include 'views/reservas/ver.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Mostrar check-ins pendentes
     */
    public function checkin() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission('secretaria');
        
        $reservaModel = new ReservaModel();
        $pendentes = $reservaModel->getPendentesCheckin();
        
        $page_title = 'Check-ins Pendentes';
        ob_start();
        include 'views/reservas/checkin.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Processar check-in
     */
    public function processarCheckin() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission('secretaria');
        
        $id = $_POST['id'] ?? null;
        if (!$id) {
            SessionHelper::setFlash('error', 'ID da reserva não especificado.');
            UrlHelper::redirect('reservas/checkin');
        }
        
        $reservaModel = new ReservaModel();
        $casaModel = new CasaModel();
        
        if ($reservaModel->fazerCheckin($id, AuthHelper::getUserId())) {
            // Atualizar estado da casa para ocupado
            $reserva = $reservaModel->findById($id);
            $casaModel->updateEstado($reserva['casa_id'], 'ocupado');
            
            SessionHelper::setFlash('success', 'Check-in realizado com sucesso!');
        } else {
            SessionHelper::setFlash('error', 'Erro ao realizar check-in. Verifique se a reserva está confirmada.');
        }
        
        UrlHelper::redirect('reservas/checkin');
    }
    
    /**
     * Mostrar check-outs pendentes
     */
    public function checkout() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission('secretaria');
        
        $reservaModel = new ReservaModel();
        $pendentes = $reservaModel->getPendentesCheckout();
        $ativas = $reservaModel->getAtivas();
        
        $page_title = 'Check-outs Pendentes';
        ob_start();
        include 'views/reservas/checkout.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Processar check-out
     */
    public function processarCheckout() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission('secretaria');
        
        $id = $_POST['id'] ?? null;
        if (!$id) {
            SessionHelper::setFlash('error', 'ID da reserva não especificado.');
            UrlHelper::redirect('reservas/checkout');
        }
        
        $reservaModel = new ReservaModel();
        
        if ($reservaModel->fazerCheckout($id, AuthHelper::getUserId())) {
            SessionHelper::setFlash('success', 'Check-out realizado com sucesso!');
        } else {
            SessionHelper::setFlash('error', 'Erro ao realizar check-out. Verifique se o check-in já foi realizado.');
        }
        
        UrlHelper::redirect('reservas/checkout');
    }
    
    /**
     * Cancelar reserva
     */
    public function cancelar() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission('reservas');
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            SessionHelper::setFlash('error', 'ID da reserva não especificado.');
            UrlHelper::redirect('reservas');
        }
        
        $reservaModel = new ReservaModel();
        $casaModel = new CasaModel();
        
        $reserva = $reservaModel->findById($id);
        if (!$reserva) {
            SessionHelper::setFlash('error', 'Reserva não encontrada.');
            UrlHelper::redirect('reservas');
        }
        
        // Não permitir cancelar se já estiver em check-out
        if ($reserva['estado'] === 'checkout_realizado') {
            SessionHelper::setFlash('error', 'Não é possível cancelar uma reserva já finalizada.');
            UrlHelper::redirect('reservas');
        }
        
        if ($reservaModel->update($id, ['estado' => 'cancelada'])) {
            // Atualizar estado da casa para disponível se estiver ocupado
            if ($reserva['estado'] === 'checkin_realizado') {
                $casaModel->updateEstado($reserva['casa_id'], 'disponivel');
            }
            
            SessionHelper::setFlash('success', 'Reserva cancelada com sucesso!');
        } else {
            SessionHelper::setFlash('error', 'Erro ao cancelar reserva. Tente novamente.');
        }
        
        UrlHelper::redirect('reservas');
    }
    
    /**
     * Validar dados da reserva
     */
    private function validateReservaData($data) {
        $errors = [];
        
        // Casa
        if (empty($data['casa_id'])) {
            $errors[] = 'A seleção da casa é obrigatória.';
        } elseif (!is_numeric($data['casa_id'])) {
            $errors[] = 'Casa inválida.';
        }
        
        // Cliente
        if (empty($data['cliente_id'])) {
            $errors[] = 'A seleção do cliente é obrigatória.';
        } elseif (!is_numeric($data['cliente_id'])) {
            $errors[] = 'Cliente inválido.';
        }
        
        // Datas
        if (empty($data['data_checkin'])) {
            $errors[] = 'A data de check-in é obrigatória.';
        } elseif (!strtotime($data['data_checkin'])) {
            $errors[] = 'Data de check-in inválida.';
        }
        
        if (empty($data['data_checkout'])) {
            $errors[] = 'A data de check-out é obrigatória.';
        } elseif (!strtotime($data['data_checkout'])) {
            $errors[] = 'Data de check-out inválida.';
        }
        
        // Validar período das datas
        if (!empty($data['data_checkin']) && !empty($data['data_checkout'])) {
            $checkin = new DateTime($data['data_checkin']);
            $checkout = new DateTime($data['data_checkout']);
            $hoje = new DateTime();
            
            if ($checkin >= $checkout) {
                $errors[] = 'A data de check-out deve ser posterior à data de check-in.';
            }
            
            if ($checkin < $hoje->setTime(0, 0, 0)) {
                $errors[] = 'A data de check-in não pode ser anterior a hoje.';
            }
            
            // Validar número máximo de noites (ex: 365 dias)
            $intervalo = $checkin->diff($checkout);
            if ($intervalo->days > 365) {
                $errors[] = 'O período da reserva não pode exceder 365 dias.';
            }
        }
        
        return $errors;
    }
    
    /**
     * Obter casas disponíveis para datas específicas (AJAX)
     */
    public function getCasasDisponiveis() {
        AuthHelper::requireAuth();
        
        $dataCheckin = $_GET['data_checkin'] ?? null;
        $dataCheckout = $_GET['data_checkout'] ?? null;
        $localizacaoId = $_GET['localizacao_id'] ?? null;
        
        if (!$dataCheckin || !$dataCheckout) {
            echo json_encode([]);
            exit;
        }
        
        $casaModel = new CasaModel();
        $casas = $casaModel->getDisponiveis($dataCheckin, $dataCheckout, $localizacaoId);
        
        header('Content-Type: application/json');
        echo json_encode($casas);
        exit;
    }
}
?>
