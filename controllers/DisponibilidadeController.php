<?php
/**
 * Controlador de Disponibilidade de Casas
 * Sistema de Gestão de Casas para Hospedagem
 * 
 * @author Oscar Massangaia
 * @institution Universidade Aberta ISCED
 * @course Engenharia Informática
 * @version 1.0
 */

require_once MODELS_PATH . 'CasaModel.php';
require_once MODELS_PATH . 'LocalizacaoModel.php';

class DisponibilidadeController {
    private $casaModel;
    private $localizacaoModel;
    
    public function __construct() {
        $this->casaModel = new CasaModel();
        $this->localizacaoModel = new LocalizacaoModel();
    }
    
    /**
     * Mostrar casas disponíveis
     */
    public function index() {
        // Obter parâmetros do formulário
        $localizacao_id = $_GET['localizacao'] ?? null;
        $data_checkin = $_GET['data_checkin'] ?? null;
        $data_checkout = $_GET['data_checkout'] ?? null;
        $tipo_casa = $_GET['tipo_casa'] ?? null;
        
        // Validar datas
        $erro = null;
        $casas_disponiveis = [];
        
        if ($data_checkin && $data_checkout) {
            // Validar se check-out é depois de check-in
            if (strtotime($data_checkout) <= strtotime($data_checkin)) {
                $erro = "A data de check-out deve ser posterior à data de check-in.";
            } else {
                // Buscar casas disponíveis
                $casas_disponiveis = $this->casaModel->getDisponiveis(
                    $data_checkin, 
                    $data_checkout, 
                    $localizacao_id,
                    $tipo_casa
                );
                
                if (empty($casas_disponiveis)) {
                    $erro = "Não foram encontradas casas disponíveis para as datas selecionadas.";
                }
            }
        }
        
        // Obter localizações para o formulário
        $localizacoes = $this->localizacaoModel->getAll();
        
        // Carregar a view
        require_once VIEWS_PATH . 'disponibilidade/index.php';
    }
    
    /**
     * API para buscar casas disponíveis via AJAX
     */
    public function buscarDisponiveis() {
        header('Content-Type: application/json');
        
        $localizacao_id = $_GET['localizacao'] ?? null;
        $data_checkin = $_GET['data_checkin'] ?? null;
        $data_checkout = $_GET['data_checkout'] ?? null;
        $tipo_casa = $_GET['tipo_casa'] ?? null;
        
        if (!$data_checkin || !$data_checkout) {
            echo json_encode(['error' => 'Datas são obrigatórias']);
            exit;
        }
        
        if (strtotime($data_checkout) <= strtotime($data_checkin)) {
            echo json_encode(['error' => 'Data de check-out deve ser posterior à de check-in']);
            exit;
        }
        
        $casas_disponiveis = $this->casaModel->getDisponiveis(
            $data_checkin, 
            $data_checkout, 
            $localizacao_id,
            $tipo_casa
        );
        
        echo json_encode([
            'success' => true,
            'casas' => $casas_disponiveis,
            'total' => count($casas_disponiveis)
        ]);
        exit;
    }
}
