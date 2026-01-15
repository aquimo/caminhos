<?php
/**
 * Modelo de Clientes
 */

class ClienteModel {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    /**
     * Obter todos os clientes
     */
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM clientes ORDER BY nome";
        
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
     * Obter cliente por ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Encontrar cliente por email
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    /**
     * Encontrar cliente por documento
     */
    public function findByDocumento($documentoNumero) {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE documento_numero = ?");
        $stmt->execute([$documentoNumero]);
        return $stmt->fetch();
    }
    
    /**
     * Criar novo cliente
     */
    public function create($data) {
        $sql = "
            INSERT INTO clientes (nome, email, telefone, nif, data_nascimento, morada, 
                                 codigo_postal, cidade, pais, documento_tipo, documento_numero) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $data['nome'],
            $data['email'],
            $data['telefone'],
            $data['nif'],
            $data['data_nascimento'],
            $data['morada'],
            $data['codigo_postal'],
            $data['cidade'],
            $data['pais'] ?? 'Portugal',
            $data['documento_tipo'],
            $data['documento_numero']
        ]);
    }
    
    /**
     * Atualizar cliente
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $updatableFields = [
            'nome', 'email', 'telefone', 'nif', 'data_nascimento', 
            'morada', 'codigo_postal', 'cidade', 'pais', 
            'documento_tipo', 'documento_numero'
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
        
        $fields[] = "data_atualizacao = CURRENT_TIMESTAMP";
        $values[] = $id;
        
        $sql = "UPDATE clientes SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($values);
    }
    
    /**
     * Apagar cliente
     */
    public function delete($id) {
        // Verificar se existem reservas associadas
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM reservas WHERE cliente_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result['total'] > 0) {
            return false; // Não pode apagar se existirem reservas
        }
        
        $stmt = $this->db->prepare("DELETE FROM clientes WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Contar clientes
     */
    public function count() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM clientes");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Verificar se email já existe
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as total FROM clientes WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['total'] > 0;
    }
    
    /**
     * Verificar se documento já existe
     */
    public function documentoExists($documentoNumero, $excludeId = null) {
        $sql = "SELECT COUNT(*) as total FROM clientes WHERE documento_numero = ?";
        $params = [$documentoNumero];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['total'] > 0;
    }
    
    /**
     * Pesquisar clientes
     */
    public function search($term, $limit = null) {
        $sql = "
            SELECT * FROM clientes 
            WHERE (nome LIKE ? OR email LIKE ? OR telefone LIKE ? OR nif LIKE ?) 
            ORDER BY nome
        ";
        
        $params = ["%$term%", "%$term%", "%$term%", "%$term%"];
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = $limit;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obter clientes com reservas ativas
     */
    public function getComReservasAtivas() {
        $stmt = $this->db->prepare("
            SELECT DISTINCT c.* 
            FROM clientes c
            JOIN reservas r ON c.id = r.cliente_id
            WHERE r.estado IN ('confirmada', 'checkin_realizado')
            ORDER BY c.nome
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obter histórico de reservas do cliente
     */
    public function getHistoricoReservas($clienteId) {
        $stmt = $this->db->prepare("
            SELECT r.*, ca.nome as casa_nome, ca.codigo as casa_codigo
            FROM reservas r
            JOIN casas ca ON r.casa_id = ca.id
            WHERE r.cliente_id = ?
            ORDER BY r.data_reserva DESC
        ");
        $stmt->execute([$clienteId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obter clientes mais recentes
     */
    public function getLatest($limit = 5) {
        $stmt = $this->db->prepare("
            SELECT * FROM clientes 
            ORDER BY data_criacao DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obter clientes com mais reservas
     */
    public function getTopClientes($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT c.*, COUNT(r.id) as total_reservas, SUM(r.valor_total) as valor_total
            FROM clientes c
            JOIN reservas r ON c.id = r.cliente_id
            GROUP BY c.id
            ORDER BY total_reservas DESC, valor_total DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
?>
