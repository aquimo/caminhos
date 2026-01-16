<?php
/**
 * Modelo de Hóspedes
 * Sistema de Gestão de Casas para Hospedagem
 */

class HospedeModel {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    /**
     * Obter todos os hóspedes
     */
    public function getAll($limit = null, $offset = 0) {
        $sql = "
            SELECT h.*, 
                   c.nome as casa_nome, c.codigo as casa_codigo,
                   l.nome as localizacao_nome,
                   uc.nome as utilizador_checkin_nome,
                   uco.nome as utilizador_checkout_nome
            FROM hospedes h
            JOIN casas c ON h.casa_id = c.id
            LEFT JOIN localizacoes l ON c.localizacao_id = l.id
            LEFT JOIN utilizadores uc ON h.utilizador_checkin = uc.id
            LEFT JOIN utilizadores uco ON h.utilizador_checkout = uco.id
            ORDER BY h.data_checkin DESC
        ";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit, $offset]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obter hóspede por ID
     */
    public function findById($id) {
        $sql = "
            SELECT h.*, 
                   c.nome as casa_nome, c.codigo as casa_codigo,
                   l.nome as localizacao_nome,
                   uc.nome as utilizador_checkin_nome,
                   uco.nome as utilizador_checkout_nome
            FROM hospedes h
            JOIN casas c ON h.casa_id = c.id
            LEFT JOIN localizacoes l ON c.localizacao_id = l.id
            LEFT JOIN utilizadores uc ON h.utilizador_checkin = uc.id
            LEFT JOIN utilizadores uco ON h.utilizador_checkout = uco.id
            WHERE h.id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obter hóspedes ativos
     */
    public function getAtivos() {
        $sql = "
            SELECT h.*, 
                   c.nome as casa_nome, c.codigo as casa_codigo,
                   l.nome as localizacao_nome
            FROM hospedes h
            JOIN casas c ON h.casa_id = c.id
            LEFT JOIN localizacoes l ON c.localizacao_id = l.id
            WHERE h.estado = 'ativo'
            ORDER BY h.data_checkin DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obter casas disponíveis para check-in
     */
    public function getCasasDisponiveis() {
        $sql = "
            SELECT c.*, l.nome as localizacao_nome
            FROM casas c
            LEFT JOIN localizacoes l ON c.localizacao_id = l.id
            WHERE c.estado = 'disponivel'
            AND c.id NOT IN (
                SELECT DISTINCT casa_id 
                FROM hospedes 
                WHERE estado = 'ativo'
            )
            ORDER BY c.nome
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Criar novo hóspede
     */
    public function create($data) {
        $sql = "
            INSERT INTO hospedes (
                nome, procedencia, endereco, contacto, previsao_permanencia,
                data_checkin, casa_id, senha, numero_conta, nome_conta,
                valor_pagar, valor_pago, estado, utilizador_checkin
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 'ativo', ?)
        ";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nome'],
            $data['procedencia'],
            $data['endereco'],
            $data['contacto'],
            $data['previsao_permanencia'],
            $data['data_checkin'],
            $data['casa_id'],
            $data['senha'],
            $data['numero_conta'],
            $data['nome_conta'],
            $data['valor_pagar'],
            $data['utilizador_checkin']
        ]);
    }
    
    /**
     * Atualizar hóspede
     */
    public function update($id, $data) {
        $sql = "
            UPDATE hospedes SET
                nome = ?, procedencia = ?, endereco = ?, contacto = ?,
                previsao_permanencia = ?, valor_pagar = ?, valor_pago = ?
            WHERE id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nome'],
            $data['procedencia'],
            $data['endereco'],
            $data['contacto'],
            $data['previsao_permanencia'],
            $data['valor_pagar'],
            $data['valor_pago'],
            $id
        ]);
    }
    
    /**
     * Realizar check-out
     */
    public function checkout($id, $utilizador_id) {
        $sql = "
            UPDATE hospedes SET
                estado = 'checkout_realizado',
                data_checkout = NOW(),
                utilizador_checkout = ?
            WHERE id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$utilizador_id, $id]);
    }
    
    /**
     * Contar hóspedes
     */
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM hospedes";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    /**
     * Contar hóspedes ativos
     */
    public function countAtivos() {
        $sql = "SELECT COUNT(*) as total FROM hospedes WHERE estado = 'ativo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    /**
     * Obter estatísticas
     */
    public function getEstatisticas() {
        $sql = "
            SELECT 
                COUNT(*) as total_hospedes,
                COUNT(CASE WHEN estado = 'ativo' THEN 1 END) as hospedes_ativos,
                COUNT(CASE WHEN estado = 'checkout_realizado' THEN 1 END) as checkouts_realizados,
                SUM(valor_pagar) as valor_total_pagar,
                SUM(valor_pago) as valor_total_pago,
                SUM(valor_pagar - valor_pago) as valor_pendente
            FROM hospedes
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
