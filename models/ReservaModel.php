<?php
/**
 * Modelo de Reservas
 */

class ReservaModel {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    /**
     * Obter todas as reservas
     */
    public function getAll($limit = null, $offset = 0) {
        $sql = "
            SELECT r.*, 
                   c.nome as cliente_nome, c.email as cliente_email,
                   ca.nome as casa_nome, ca.codigo as casa_codigo,
                   l.nome as localizacao_nome
            FROM reservas r
            JOIN clientes c ON r.cliente_id = c.id
            JOIN casas ca ON r.casa_id = ca.id
            LEFT JOIN localizacoes l ON ca.localizacao_id = l.id
            ORDER BY r.data_reserva DESC
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
     * Obter reserva por ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT r.*, 
                   c.nome as cliente_nome, c.email as cliente_email, c.telefone as cliente_telefone,
                   ca.nome as casa_nome, ca.codigo as casa_codigo, ca.tipologia,
                   l.nome as localizacao_nome, l.cidade
            FROM reservas r
            JOIN clientes c ON r.cliente_id = c.id
            JOIN casas ca ON r.casa_id = ca.id
            LEFT JOIN localizacoes l ON ca.localizacao_id = l.id
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Criar nova reserva
     */
    public function create($data) {
        // Calcular número de noites
        $dataCheckin = new DateTime($data['data_checkin']);
        $dataCheckout = new DateTime($data['data_checkout']);
        $numeroNoites = $dataCheckin->diff($dataCheckout)->days;
        
        // Obter preço da casa
        $stmt = $this->db->prepare("SELECT preco_diario, preco_semanal, preco_mensal FROM casas WHERE id = ?");
        $stmt->execute([$data['casa_id']]);
        $casa = $stmt->fetch();
        
        // Calcular valor total
        $valorTotal = $this->calcularValorTotal($casa, $numeroNoites);
        
        $sql = "
            INSERT INTO reservas (casa_id, cliente_id, data_checkin, data_checkout, 
                                numero_noites, valor_total, valor_pago, estado, observacoes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $data['casa_id'],
            $data['cliente_id'],
            $data['data_checkin'],
            $data['data_checkout'],
            $numeroNoites,
            $valorTotal,
            0,
            $data['estado'] ?? 'confirmada',
            $data['observacoes'] ?? null
        ]);
    }
    
    /**
     * Atualizar reserva
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $updatableFields = [
            'casa_id', 'cliente_id', 'data_checkin', 'data_checkout', 
            'estado', 'observacoes'
        ];
        
        foreach ($updatableFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        
        // Se datas foram alteradas, recalcular número de noites e valor total
        if (isset($data['data_checkin']) || isset($data['data_checkout'])) {
            $reserva = $this->findById($id);
            $checkin = $data['data_checkin'] ?? $reserva['data_checkin'];
            $checkout = $data['data_checkout'] ?? $reserva['data_checkout'];
            
            $dataCheckin = new DateTime($checkin);
            $dataCheckout = new DateTime($checkout);
            $numeroNoites = $dataCheckin->diff($dataCheckout)->days;
            
            $fields[] = "numero_noites = ?";
            $values[] = $numeroNoites;
            
            // Recalcular valor total
            $casaId = $data['casa_id'] ?? $reserva['casa_id'];
            $stmt = $this->db->prepare("SELECT preco_diario, preco_semanal, preco_mensal FROM casas WHERE id = ?");
            $stmt->execute([$casaId]);
            $casa = $stmt->fetch();
            
            $valorTotal = $this->calcularValorTotal($casa, $numeroNoites);
            $fields[] = "valor_total = ?";
            $values[] = $valorTotal;
        }
        
        if (isset($data['valor_pago'])) {
            $fields[] = "valor_pago = ?";
            $values[] = $data['valor_pago'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        
        $sql = "UPDATE reservas SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($values);
    }
    
    /**
     * Apagar reserva
     */
    public function delete($id) {
        // Apagar pagamentos associados
        $stmt = $this->db->prepare("DELETE FROM pagamentos WHERE reserva_id = ?");
        $stmt->execute([$id]);
        
        // Apagar reserva
        $stmt = $this->db->prepare("DELETE FROM reservas WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Contar reservas
     */
    public function count() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM reservas");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Contar reservas por estado
     */
    public function countByEstado($estado) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM reservas WHERE estado = ?");
        $stmt->execute([$estado]);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Obter total pendente de pagamento
     */
    public function getTotalPendente() {
        $stmt = $this->db->prepare("
            SELECT SUM(valor_total - valor_pago) as total 
            FROM reservas 
            WHERE estado IN ('confirmada', 'checkin_realizado')
        ");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    /**
     * Verificar disponibilidade da casa para datas
     */
    public function verificarDisponibilidade($casaId, $dataCheckin, $dataCheckout, $excludeId = null) {
        $sql = "
            SELECT COUNT(*) as total 
            FROM reservas 
            WHERE casa_id = ? 
            AND estado IN ('confirmada', 'checkin_realizado')
            AND (
                (data_checkin <= ? AND data_checkout > ?) OR
                (data_checkin < ? AND data_checkout >= ?) OR
                (data_checkin >= ? AND data_checkout <= ?)
            )
        ";
        
        $params = [
            $casaId,
            $dataCheckin, $dataCheckin,
            $dataCheckout, $dataCheckout,
            $dataCheckin, $dataCheckout
        ];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['total'] == 0;
    }
    
    /**
     * Fazer check-in
     */
    public function fazerCheckin($id, $utilizadorId) {
        $stmt = $this->db->prepare("
            UPDATE reservas 
            SET estado = 'checkin_realizado', 
                data_checkin_realizado = CURRENT_TIMESTAMP,
                utilizador_checkin = ?
            WHERE id = ? AND estado = 'confirmada'
        ");
        
        return $stmt->execute([$utilizadorId, $id]);
    }
    
    /**
     * Fazer check-out
     */
    public function fazerCheckout($id, $utilizadorId) {
        $stmt = $this->db->prepare("
            UPDATE reservas 
            SET estado = 'checkout_realizado', 
                data_checkout_realizado = CURRENT_TIMESTAMP,
                utilizador_checkout = ?
            WHERE id = ? AND estado = 'checkin_realizado'
        ");
        
        // Atualizar estado da casa para disponível
        $reserva = $this->findById($id);
        if ($reserva) {
            $casaStmt = $this->db->prepare("UPDATE casas SET estado = 'disponivel' WHERE id = ?");
            $casaStmt->execute([$reserva['casa_id']]);
        }
        
        return $stmt->execute([$utilizadorId, $id]);
    }
    
    /**
     * Obter reservas ativas (check-in realizado)
     */
    public function getAtivas() {
        $stmt = $this->db->prepare("
            SELECT r.*, 
                   c.nome as cliente_nome, c.telefone as cliente_telefone,
                   ca.nome as casa_nome, ca.codigo as casa_codigo
            FROM reservas r
            JOIN clientes c ON r.cliente_id = c.id
            JOIN casas ca ON r.casa_id = ca.id
            WHERE r.estado = 'checkin_realizado'
            ORDER BY r.data_checkout
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obter reservas pendentes de check-in
     */
    public function getPendentesCheckin() {
        $stmt = $this->db->prepare("
            SELECT r.*, 
                   c.nome as cliente_nome, c.telefone as cliente_telefone,
                   ca.nome as casa_nome, ca.codigo as casa_codigo
            FROM reservas r
            JOIN clientes c ON r.cliente_id = c.id
            JOIN casas ca ON r.casa_id = ca.id
            WHERE r.estado = 'confirmada'
            AND r.data_checkin <= CURRENT_DATE
            ORDER BY r.data_checkin
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obter reservas pendentes de check-out
     */
    public function getPendentesCheckout() {
        $stmt = $this->db->prepare("
            SELECT r.*, 
                   c.nome as cliente_nome, c.telefone as cliente_telefone,
                   ca.nome as casa_nome, ca.codigo as casa_codigo
            FROM reservas r
            JOIN clientes c ON r.cliente_id = c.id
            JOIN casas ca ON r.casa_id = ca.id
            WHERE r.estado = 'checkin_realizado'
            AND r.data_checkout <= CURRENT_DATE
            ORDER BY r.data_checkout
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obter reservas pendentes (pagamentos em atraso)
     */
    public function getPendentes() {
        $stmt = $this->db->prepare("
            SELECT r.*, 
                   c.nome as cliente_nome, c.telefone as cliente_telefone,
                   ca.nome as casa_nome, ca.codigo as casa_codigo
            FROM reservas r
            JOIN clientes c ON r.cliente_id = c.id
            JOIN casas ca ON r.casa_id = ca.id
            WHERE r.valor_pago < r.valor_total
            AND r.data_checkout < CURDATE()
            ORDER BY r.data_checkout ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Calcular valor total da reserva
     */
    private function calcularValorTotal($casa, $numeroNoites) {
        $valorTotal = 0;
        
        // Lógica de preços progressivos
        if ($numeroNoites >= 30) {
            // Usar preço mensal
            $meses = floor($numeroNoites / 30);
            $diasRestantes = $numeroNoites % 30;
            $valorTotal = ($meses * $casa['preco_mensal']) + ($diasRestantes * $casa['preco_diario']);
        } elseif ($numeroNoites >= 7) {
            // Usar preço semanal
            $semanas = floor($numeroNoites / 7);
            $diasRestantes = $numeroNoites % 7;
            $valorTotal = ($semanas * $casa['preco_semanal']) + ($diasRestantes * $casa['preco_diario']);
        } else {
            // Usar preço diário
            $valorTotal = $numeroNoites * $casa['preco_diario'];
        }
        
        return $valorTotal;
    }
}
?>
