<?php
/**
 * Controlador do Dashboard
 */

require_once 'models/CasaModel.php';
require_once 'models/ReservaModel.php';
require_once 'models/ClienteModel.php';
require_once 'models/UtilizadorModel.php';
require_once 'models/PagamentoModel.php';
require_once 'helpers/auth_helper.php';
require_once 'helpers/url_helper.php';

class DashboardController {
    
    /**
     * Mostrar dashboard principal
     */
    public function index() {
        AuthHelper::requireAuth();
        
        // Obter estatísticas baseadas no perfil do utilizador
        $stats = $this->getDashboardStats();
        $recentActivities = $this->getRecentActivities();
        $charts = $this->getChartData();
        
        $page_title = 'Dashboard';
        ob_start();
        include 'views/dashboard/index.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Obter estatísticas do dashboard
     */
    private function getDashboardStats() {
        $casaModel = new CasaModel();
        $reservaModel = new ReservaModel();
        $clienteModel = new ClienteModel();
        $utilizadorModel = new UtilizadorModel();
        $pagamentoModel = new PagamentoModel();
        
        $stats = [];
        
        // Estatísticas disponíveis para todos os perfis
        $stats['total_casas'] = $casaModel->count();
        $stats['casas_disponiveis'] = $casaModel->countByEstado('disponivel');
        $stats['casas_ocupadas'] = $casaModel->countByEstado('ocupado');
        
        $stats['total_reservas'] = $reservaModel->count();
        $stats['reservas_ativas'] = $reservaModel->countByEstado('confirmada');
        $stats['checkins_pendentes'] = $reservaModel->countByEstado('confirmada');
        
        $stats['total_clientes'] = $clienteModel->count();
        
        // Estatísticas financeiras (apenas para gestor geral e contabilidade)
        if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('contabilidade')) {
            $stats['total_receitas'] = $pagamentoModel->getTotalReceitas();
            $stats['receitas_mes'] = $pagamentoModel->getReceitasMes();
            $stats['pagamentos_pendentes'] = $reservaModel->getTotalPendente();
        }
        
        // Estatísticas de utilizadores (apenas para gestor geral)
        if (AuthHelper::hasProfile('gestor_geral')) {
            $stats['total_utilizadores'] = $utilizadorModel->count();
        }
        
        // Estatísticas específicas para gestor de condomínios
        if (AuthHelper::hasProfile('gestor_condominios')) {
            $stats['taxa_ocupacao'] = $this->calcularTaxaOcupacao();
        }
        
        // Taxa de ocupação também para gestor geral
        if (AuthHelper::hasProfile('gestor_geral')) {
            $stats['taxa_ocupacao'] = $this->calcularTaxaOcupacao();
        }
        
        return $stats;
    }
    
    /**
     * Obter atividades recentes
     */
    private function getRecentActivities() {
        global $db;
        
        $activities = [];
        
        // Reservas recentes
        $reservas = $db->query("
            SELECT r.*, c.nome as cliente_nome, ca.nome as casa_nome, 'reserva' as tipo
            FROM reservas r
            JOIN clientes c ON r.cliente_id = c.id
            JOIN casas ca ON r.casa_id = ca.id
            ORDER BY r.data_reserva DESC
            LIMIT 5
        ")->fetchAll();
        
        foreach ($reservas as $reserva) {
            $activities[] = [
                'tipo' => 'reserva',
                'descricao' => "Nova reserva: {$reserva['cliente_nome']} - {$reserva['casa_nome']}",
                'data' => $reserva['data_reserva'],
                'estado' => $reserva['estado']
            ];
        }
        
        // Check-ins recentes (apenas para secretaria e gestor geral)
        if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('secretaria')) {
            $checkins = $db->query("
                SELECT r.*, c.nome as cliente_nome, ca.nome as casa_nome, 'checkin' as tipo
                FROM reservas r
                JOIN clientes c ON r.cliente_id = c.id
                JOIN casas ca ON r.casa_id = ca.id
                WHERE r.data_checkin_realizado IS NOT NULL
                ORDER BY r.data_checkin_realizado DESC
                LIMIT 3
            ")->fetchAll();
            
            foreach ($checkins as $checkin) {
                $activities[] = [
                    'tipo' => 'checkin',
                    'descricao' => "Check-in realizado: {$checkin['cliente_nome']} - {$checkin['casa_nome']}",
                    'data' => $checkin['data_checkin_realizado'],
                    'estado' => 'completed'
                ];
            }
        }
        
        // Pagamentos recentes (apenas para contabilidade e gestor geral)
        if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('contabilidade')) {
            $pagamentos = $db->query("
                SELECT p.*, r.id as reserva_id, c.nome as cliente_nome, 'pagamento' as tipo
                FROM pagamentos p
                JOIN reservas r ON p.reserva_id = r.id
                JOIN clientes c ON r.cliente_id = c.id
                ORDER BY p.data_pagamento DESC
                LIMIT 3
            ")->fetchAll();
            
            foreach ($pagamentos as $pagamento) {
                $activities[] = [
                    'tipo' => 'pagamento',
                    'descricao' => "Pagamento recebido: {$pagamento['cliente_nome']} - €{$pagamento['valor']}",
                    'data' => $pagamento['data_pagamento'],
                    'estado' => 'completed'
                ];
            }
        }
        
        // Ordenar por data
        usort($activities, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });
        
        return array_slice($activities, 0, 10);
    }
    
    /**
     * Obter dados para gráficos
     */
    private function getChartData() {
        global $db;
        
        $charts = [];
        
        // Gráfico de reservas por mês
        $reservasMes = $db->query("
            SELECT DATE_FORMAT(data_reserva, '%Y-%m') as mes, COUNT(*) as total
            FROM reservas
            WHERE data_reserva >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(data_reserva, '%Y-%m')
            ORDER BY mes
        ")->fetchAll();
        
        $charts['reservas_mes'] = $reservasMes;
        
        // Gráfico de ocupação (apenas para perfis autorizados)
        if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('gestor_condominios')) {
            $ocupacao = $db->query("
                SELECT 
                    CASE 
                        WHEN estado = 'disponivel' THEN 'Disponível'
                        WHEN estado = 'ocupado' THEN 'Ocupado'
                        WHEN estado = 'manutencao' THEN 'Manutenção'
                        ELSE 'Indisponível'
                    END as estado_label,
                    COUNT(*) as total
                FROM casas
                GROUP BY estado
            ")->fetchAll();
            
            $charts['ocupacao'] = $ocupacao;
        }
        
        // Gráfico de receitas (apenas para contabilidade e gestor geral)
        if (AuthHelper::hasProfile('gestor_geral') || AuthHelper::hasProfile('contabilidade')) {
            $receitasMes = $db->query("
                SELECT DATE_FORMAT(data_pagamento, '%Y-%m') as mes, SUM(valor) as total
                FROM pagamentos
                WHERE data_pagamento >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(data_pagamento, '%Y-%m')
                ORDER BY mes
            ")->fetchAll();
            
            $charts['receitas_mes'] = $receitasMes;
        }
        
        return $charts;
    }
    
    /**
     * Calcular taxa de ocupação
     */
    private function calcularTaxaOcupacao() {
        global $db;
        
        $totalCasas = $this->getTotalCasas();
        if ($totalCasas == 0) return 0;
        
        $casasOcupadas = $this->getCasasOcupadas();
        
        return round(($casasOcupadas / $totalCasas) * 100, 2);
    }
    
    /**
     * Obter total de casas
     */
    private function getTotalCasas() {
        global $db;
        
        $stmt = $db->query("SELECT COUNT(*) as total FROM casas");
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Obter casas ocupadas
     */
    private function getCasasOcupadas() {
        global $db;
        
        $stmt = $db->query("SELECT COUNT(*) as total FROM casas WHERE estado = 'ocupado'");
        $result = $stmt->fetch();
        return $result['total'];
    }
}
?>
