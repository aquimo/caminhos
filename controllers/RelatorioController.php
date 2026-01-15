<?php
/**
 * Controlador de Relatórios
 */

require_once 'models/PagamentoModel.php';
require_once 'models/ReservaModel.php';
require_once 'models/CasaModel.php';
require_once 'models/ClienteModel.php';
require_once 'helpers/auth_helper.php';
require_once 'helpers/url_helper.php';

class RelatorioController {
    
    /**
     * Listar relatórios gerais
     */
    public function index() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission('gestor_geral');
        
        $page_title = 'Relatórios Gerais';
        ob_start();
        include 'views/relatorios/index.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Relatórios financeiros
     */
    public function financeiros() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission('contabilidade');
        
        $pagamentoModel = new PagamentoModel();
        $reservaModel = new ReservaModel();
        
        // Filtros
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');
        $metodo = $_GET['metodo'] ?? null;
        
        // Obter dados
        $receitasPeriodo = $pagamentoModel->getReceitasPeriodo($dataInicio, $dataFim);
        $receitasPorMetodo = $pagamentoModel->getReceitasPorMetodo();
        $receitasPorMes = $pagamentoModel->getReceitasPorMes(12);
        $pagamentosPendentes = $reservaModel->getPendentes();
        
        // Filtrar por método se especificado
        if ($metodo) {
            $receitasPorMetodo = array_filter($receitasPorMetodo, function($item) use ($metodo) {
                return $item['metodo_pagamento'] === $metodo;
            });
        }
        
        $page_title = 'Relatórios Financeiros';
        ob_start();
        include 'views/relatorios/financeiros.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Relatório de ocupação
     */
    public function ocupacao() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission('gestor_condominios');
        
        $casaModel = new CasaModel();
        $reservaModel = new ReservaModel();
        
        // Filtros
        $localizacaoId = $_GET['localizacao_id'] ?? null;
        $mes = $_GET['mes'] ?? date('Y-m');
        
        // Obter dados
        $casas = $casaModel->getAll();
        if ($localizacaoId) {
            $casas = array_filter($casas, function($casa) use ($localizacaoId) {
                return $casa['localizacao_id'] == $localizacaoId;
            });
        }
        
        // Calcular taxa de ocupação
        $taxaOcupacao = $this->calcularTaxaOcupacaoPeriodo($mes, $localizacaoId);
        
        // Obter estatísticas por casa
        $estatisticasCasas = $this->getEstatisticasCasas($mes, $localizacaoId);
        
        $page_title = 'Relatório de Ocupação';
        ob_start();
        include 'views/relatorios/ocupacao.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Exportar relatório financeiro (CSV)
     */
    public function exportarFinanceiro() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission('contabilidade');
        
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');
        
        $pagamentoModel = new PagamentoModel();
        $pagamentos = $pagamentoModel->getAll();
        
        // Filtrar por período
        $pagamentosFiltrados = array_filter($pagamentos, function($pagamento) use ($dataInicio, $dataFim) {
            $dataPagamento = date('Y-m-d', strtotime($pagamento['data_pagamento']));
            return $dataPagamento >= $dataInicio && $dataPagamento <= $dataFim;
        });
        
        // Gerar CSV
        $filename = 'relatorio_financeiro_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Cabeçalho
        fputcsv($output, [
            'ID', 'Data Pagamento', 'Valor', 'Método', 'Referência',
            'Cliente', 'Casa', 'Utilizador'
        ]);
        
        // Dados
        foreach ($pagamentosFiltrados as $pagamento) {
            fputcsv($output, [
                $pagamento['id'],
                date('d/m/Y H:i', strtotime($pagamento['data_pagamento'])),
                number_format($pagamento['valor'], 2, ',', ' '),
                $pagamento['metodo_pagamento'],
                $pagamento['referencia'] ?? '',
                $pagamento['cliente_nome'],
                $pagamento['casa_codigo'],
                $pagamento['utilizador_nome']
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Exportar relatório de ocupação (CSV)
     */
    public function exportarOcupacao() {
        AuthHelper::requireAuth();
        AuthHelper::requirePermission('gestor_condominios');
        
        $mes = $_GET['mes'] ?? date('Y-m');
        $localizacaoId = $_GET['localizacao_id'] ?? null;
        
        $estatisticas = $this->getEstatisticasCasas($mes, $localizacaoId);
        
        $filename = 'relatorio_ocupacao_' . str_replace('-', '_', $mes) . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Cabeçalho
        fputcsv($output, [
            'Casa', 'Código', 'Localização', 'Tipologia', 'Capacidade',
            'Dias Ocupados', 'Dias Disponíveis', 'Taxa Ocupação', 'Receita'
        ]);
        
        // Dados
        foreach ($estatisticas as $estatistica) {
            fputcsv($output, [
                $estatistica['nome'],
                $estatistica['codigo'],
                $estatistica['localizacao_nome'],
                $estatistica['tipologia'],
                $estatistica['capacidade'],
                $estatistica['dias_ocupados'],
                $estatistica['dias_disponiveis'],
                number_format($estatistica['taxa_ocupacao'], 1) . '%',
                '€' . number_format($estatistica['receita'], 2, ',', ' ')
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Calcular taxa de ocupação para um período
     */
    private function calcularTaxaOcupacaoPeriodo($mes, $localizacaoId = null) {
        global $db;
        
        // Obter número de dias no mês
        $data = new DateTime($mes . '-01');
        $diasNoMes = $data->format('t');
        
        // Obter casas
        $sql = "SELECT c.id FROM casas c";
        $params = [];
        
        if ($localizacaoId) {
            $sql .= " WHERE c.localizacao_id = ?";
            $params[] = $localizacaoId;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $casas = $stmt->fetchAll();
        
        if (empty($casas)) {
            return 0;
        }
        
        $totalCasas = count($casas);
        $totalDiasPossiveis = $totalCasas * $diasNoMes;
        
        // Calcular dias ocupados
        $sql = "
            SELECT COUNT(*) * DATEDIFF(r.data_checkout, r.data_checkin) as dias_ocupados
            FROM reservas r
            JOIN casas c ON r.casa_id = c.id
            WHERE r.estado IN ('checkin_realizado', 'checkout_realizado')
            AND (
                (r.data_checkin <= ? AND r.data_checkout > ?)
            )
        ";
        
        $params = [
            $mes . '-' . $diasNoMes,
            $mes . '-01'
        ];
        
        if ($localizacaoId) {
            $sql .= " AND c.localizacao_id = ?";
            $params[] = $localizacaoId;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        $diasOcupados = $result['dias_ocupados'] ?? 0;
        
        return $totalDiasPossiveis > 0 ? round(($diasOcupados / $totalDiasPossiveis) * 100, 2) : 0;
    }
    
    /**
     * Obter estatísticas por casa
     */
    private function getEstatisticasCasas($mes, $localizacaoId = null) {
        global $db;
        
        // Obter número de dias no mês
        $data = new DateTime($mes . '-01');
        $diasNoMes = $data->format('t');
        $dataInicio = $mes . '-01';
        $dataFim = $mes . '-' . $diasNoMes;
        
        $sql = "
            SELECT 
                c.id, c.codigo, c.nome, c.tipologia, c.capacidade,
                l.nome as localizacao_nome,
                COALESCE(SUM(
                    CASE 
                        WHEN r.estado IN ('checkin_realizado', 'checkout_realizado')
                        AND (
                            (r.data_checkin <= ? AND r.data_checkout > ?)
                        )
                        THEN GREATEST(1, DATEDIFF(
                            LEAST(r.data_checkout, ?),
                            GREATEST(r.data_checkin, ?)
                        ))
                        ELSE 0
                    END
                ), 0) as dias_ocupados,
                COALESCE(SUM(
                    CASE 
                        WHEN r.estado IN ('checkin_realizado', 'checkout_realizado')
                        AND (
                            (r.data_checkin <= ? AND r.data_checkout > ?)
                        )
                        THEN r.valor_total / DATEDIFF(r.data_checkout, r.data_checkin) * 
                            GREATEST(1, DATEDIFF(
                                LEAST(r.data_checkout, ?),
                                GREATEST(r.data_checkin, ?)
                            ))
                        ELSE 0
                    END
                ), 0) as receita
            FROM casas c
            LEFT JOIN localizacoes l ON c.localizacao_id = l.id
            LEFT JOIN reservas r ON c.id = r.casa_id
        ";
        
        $params = [
            $dataFim, $dataInicio, $dataFim, $dataInicio,
            $dataFim, $dataInicio, $dataFim, $dataInicio
        ];
        
        if ($localizacaoId) {
            $sql .= " WHERE c.localizacao_id = ?";
            $params[] = $localizacaoId;
        }
        
        $sql .= " GROUP BY c.id, c.codigo, c.nome, c.tipologia, c.capacidade, l.nome
                  ORDER BY c.nome";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $estatisticas = $stmt->fetchAll();
        
        // Calcular campos adicionais
        foreach ($estatisticas as &$estatistica) {
            $estatistica['dias_disponiveis'] = $diasNoMes;
            $estatistica['taxa_ocupacao'] = round(($estatistica['dias_ocupados'] / $diasNoMes) * 100, 1);
        }
        
        return $estatisticas;
    }
}
?>
