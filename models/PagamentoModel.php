<?php
/**
 * Modelo de Pagamentos
 */

class PagamentoModel {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    /**
     * Obter todos os pagamentos
     */
    public function getAll($limit = null, $offset = 0) {
        $sql = "
            SELECT p.*, 
                   r.id as reserva_id, r.valor_total as reserva_valor_total,
                   c.nome as cliente_nome, c.email as cliente_email,
                   ca.nome as casa_nome, ca.codigo as casa_codigo,
                   u.nome as utilizador_nome
            FROM pagamentos p
            JOIN reservas r ON p.reserva_id = r.id
            JOIN clientes c ON r.cliente_id = c.id
            JOIN casas ca ON r.casa_id = ca.id
            JOIN utilizadores u ON p.utilizador_id = u.id
            ORDER BY p.data_pagamento DESC
        ";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit, $offset]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obter pagamento por ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   r.id as reserva_id, r.valor_total as reserva_valor_total,
                   c.nome as cliente_nome, c.email as cliente_email,
                   ca.nome as casa_nome, ca.codigo as casa_codigo,
                   u.nome as utilizador_nome
            FROM pagamentos p
            JOIN reservas r ON p.reserva_id = r.id
            JOIN clientes c ON r.cliente_id = c.id
            JOIN casas ca ON r.casa_id = ca.id
            JOIN utilizadores u ON p.utilizador_id = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Criar novo pagamento
     */
    public function create($data) {
        $sql = "
            INSERT INTO pagamentos (reserva_id, valor, data_pagamento, metodo_pagamento, 
                                   referencia, observacoes, utilizador_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";
        
        $stmt = $this->db->prepare($sql);
        
        $result = $stmt->execute([
            $data['reserva_id'],
            $data['valor'],
            $data['data_pagamento'] ?? date('Y-m-d H:i:s'),
            $data['metodo_pagamento'],
            $data['referencia'] ?? null,
            $data['observacoes'] ?? null,
            $data['utilizador_id']
        ]);
        
        if ($result) {
            // Atualizar valor pago na reserva
            $this->atualizarValorPagoReserva($data['reserva_id']);
        }
        
        return $result;
    }
    
    /**
     * Atualizar pagamento
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $updatableFields = [
            'valor', 'data_pagamento', 'metodo_pagamento', 'referencia', 'observacoes'
        ];
        
        foreach ($updatableFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        
        $sql = "UPDATE pagamentos SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        $result = $stmt->execute($values);
        
        if ($result) {
            // Atualizar valor pago na reserva
            $pagamento = $this->findById($id);
            if ($pagamento) {
                $this->atualizarValorPagoReserva($pagamento['reserva_id']);
            }
        }
        
        return $result;
    }
    
    /**
     * Apagar pagamento
     */
    public function delete($id) {
        $pagamento = $this->findById($id);
        $reservaId = $pagamento['reserva_id'];
        
        $stmt = $this->db->prepare("DELETE FROM pagamentos WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            // Atualizar valor pago na reserva
            $this->atualizarValorPagoReserva($reservaId);
        }
        
        return $result;
    }
    
    /**
     * Obter pagamentos por reserva
     */
    public function getByReserva($reservaId) {
        $stmt = $this->db->prepare("
            SELECT p.*, u.nome as utilizador_nome
            FROM pagamentos p
            JOIN utilizadores u ON p.utilizador_id = u.id
            WHERE p.reserva_id = ?
            ORDER BY p.data_pagamento DESC
        ");
        $stmt->execute([$reservaId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obter total de receitas
     */
    public function getTotalReceitas() {
        $stmt = $this->db->prepare("SELECT SUM(valor) as total FROM pagamentos");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    /**
     * Obter receitas do mês atual
     */
    public function getReceitasMes() {
        $stmt = $this->db->prepare("
            SELECT SUM(valor) as total 
            FROM pagamentos 
            WHERE MONTH(data_pagamento) = MONTH(CURRENT_DATE) 
            AND YEAR(data_pagamento) = YEAR(CURRENT_DATE)
        ");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    /**
     * Obter receitas por período
     */
    public function getReceitasPeriodo($dataInicio, $dataFim) {
        $stmt = $this->db->prepare("
            SELECT SUM(valor) as total 
            FROM pagamentos 
            WHERE DATE(data_pagamento) BETWEEN ? AND ?
        ");
        $stmt->execute([$dataInicio, $dataFim]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    /**
     * Obter receitas por mês
     */
    public function getReceitasPorMes($meses = 12) {
        $stmt = $this->db->prepare("
            SELECT DATE_FORMAT(data_pagamento, '%Y-%m') as mes, 
                   SUM(valor) as total,
                   COUNT(*) as numero_pagamentos
            FROM pagamentos 
            WHERE data_pagamento >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            GROUP BY DATE_FORMAT(data_pagamento, '%Y-%m')
            ORDER BY mes
        ");
        $stmt->execute([$meses]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obter receitas por método de pagamento
     */
    public function getReceitasPorMetodo() {
        $stmt = $this->db->prepare("
            SELECT metodo_pagamento, SUM(valor) as total, COUNT(*) as count
            FROM pagamentos 
            GROUP BY metodo_pagamento
            ORDER BY total DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Atualizar valor pago na reserva
     */
    private function atualizarValorPagoReserva($reservaId) {
        $stmt = $this->db->prepare("
            UPDATE reservas 
            SET valor_pago = (
                SELECT COALESCE(SUM(valor), 0) 
                FROM pagamentos 
                WHERE reserva_id = ?
            )
            WHERE id = ?
        ");
        $stmt->execute([$reservaId, $reservaId]);
    }
    
    /**
     * Obter pagamentos pendentes
     */
    public function getPendentes() {
        $stmt = $this->db->prepare("
            SELECT r.*, 
                   c.nome as cliente_nome,
                   ca.nome as casa_nome, ca.codigo as casa_codigo,
                   (r.valor_total - r.valor_pago) as pendente
            FROM reservas r
            JOIN clientes c ON r.cliente_id = c.id
            JOIN casas ca ON r.casa_id = ca.id
            WHERE r.estado IN ('confirmada', 'checkin_realizado')
            AND r.valor_pago < r.valor_total
            ORDER BY r.data_checkin
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Contar pagamentos
     */
    public function count() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM pagamentos");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Pesquisar pagamentos
     */
    public function search($term, $limit = null) {
        $sql = "
            SELECT p.*, 
                   c.nome as cliente_nome,
                   ca.nome as casa_nome, ca.codigo as casa_codigo
            FROM pagamentos p
            JOIN reservas r ON p.reserva_id = r.id
            JOIN clientes c ON r.cliente_id = c.id
            JOIN casas ca ON r.casa_id = ca.id
            WHERE (c.nome LIKE ? OR ca.codigo LIKE ? OR p.referencia LIKE ?)
            ORDER BY p.data_pagamento DESC
        ";
        
        $params = ["%$term%", "%$term%", "%$term%"];
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = $limit;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
}
?>
