<?php
/**
 * Modelo de Casas
 */

class CasaModel {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    /**
     * Obter todas as casas
     */
    public function getAll($limit = null, $offset = 0) {
        $sql = "
            SELECT c.*, l.nome as localizacao_nome, l.cidade 
            FROM casas c 
            LEFT JOIN localizacoes l ON c.localizacao_id = l.id 
            ORDER BY c.nome
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
     * Obter casa por ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT c.*, l.nome as localizacao_nome, l.cidade, l.endereco 
            FROM casas c 
            LEFT JOIN localizacoes l ON c.localizacao_id = l.id 
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Obter casa por código
     */
    public function findByCodigo($codigo) {
        $stmt = $this->db->prepare("
            SELECT c.*, l.nome as localizacao_nome, l.cidade 
            FROM casas c 
            LEFT JOIN localizacoes l ON c.localizacao_id = l.id 
            WHERE c.codigo = ?
        ");
        $stmt->execute([$codigo]);
        return $stmt->fetch();
    }
    
    /**
     * Criar nova casa
     */
    public function create($data) {
        $sql = "
            INSERT INTO casas (codigo, localizacao_id, nome, descricao, tipologia, capacidade, 
                              area_decimal, preco_diario, preco_semanal, preco_mensal, estado, 
                              comodidades, imagens) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $data['codigo'],
            $data['localizacao_id'],
            $data['nome'],
            $data['descricao'],
            $data['tipologia'],
            $data['capacidade'],
            $data['area_decimal'],
            $data['preco_diario'],
            $data['preco_semanal'],
            $data['preco_mensal'],
            $data['estado'] ?? 'disponivel',
            json_encode($data['comodidades'] ?? []),
            json_encode($data['imagens'] ?? [])
        ]);
    }
    
    /**
     * Atualizar casa
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $updatableFields = [
            'codigo', 'localizacao_id', 'nome', 'descricao', 'tipologia', 'capacidade',
            'area_decimal', 'preco_diario', 'preco_semanal', 'preco_mensal', 'estado'
        ];
        
        foreach ($updatableFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        
        if (isset($data['comodidades'])) {
            $fields[] = "comodidades = ?";
            $values[] = json_encode($data['comodidades']);
        }
        
        if (isset($data['imagens'])) {
            $fields[] = "imagens = ?";
            $values[] = json_encode($data['imagens']);
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $fields[] = "data_atualizacao = CURRENT_TIMESTAMP";
        $values[] = $id;
        
        $sql = "UPDATE casas SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($values);
    }
    
    /**
     * Apagar casa
     */
    public function delete($id) {
        // Verificar se existem reservas associadas
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM reservas WHERE casa_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result['total'] > 0) {
            return false; // Não pode apagar se existirem reservas
        }
        
        $stmt = $this->db->prepare("DELETE FROM casas WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Contar casas
     */
    public function count() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM casas");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Contar casas por estado
     */
    public function countByEstado($estado) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM casas WHERE estado = ?");
        $stmt->execute([$estado]);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Verificar se código já existe
     */
    public function codigoExists($codigo, $excludeId = null) {
        $sql = "SELECT COUNT(*) as total FROM casas WHERE codigo = ?";
        $params = [$codigo];
        
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
     * Obter casas disponíveis para datas específicas
     */
    public function getDisponiveis($dataCheckin, $dataCheckout, $localizacaoId = null) {
        $sql = "
            SELECT c.*, l.nome as localizacao_nome, l.cidade 
            FROM casas c 
            LEFT JOIN localizacoes l ON c.localizacao_id = l.id 
            WHERE c.estado = 'disponivel'
            AND c.id NOT IN (
                SELECT DISTINCT casa_id 
                FROM reservas 
                WHERE estado IN ('confirmada', 'checkin_realizado')
                AND (
                    (data_checkin <= ? AND data_checkout > ?) OR
                    (data_checkin < ? AND data_checkout >= ?) OR
                    (data_checkin >= ? AND data_checkout <= ?)
                )
            )
        ";
        
        $params = [
            $dataCheckin, $dataCheckin,
            $dataCheckout, $dataCheckout,
            $dataCheckin, $dataCheckout
        ];
        
        if ($localizacaoId) {
            $sql .= " AND c.localizacao_id = ?";
            $params[] = $localizacaoId;
        }
        
        $sql .= " ORDER BY c.nome";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Pesquisar casas
     */
    public function search($term, $localizacaoId = null, $tipologia = null) {
        $sql = "
            SELECT c.*, l.nome as localizacao_nome, l.cidade 
            FROM casas c 
            LEFT JOIN localizacoes l ON c.localizacao_id = l.id 
            WHERE (c.nome LIKE ? OR c.descricao LIKE ? OR c.codigo LIKE ?)
        ";
        
        $params = ["%$term%", "%$term%", "%$term%"];
        
        if ($localizacaoId) {
            $sql .= " AND c.localizacao_id = ?";
            $params[] = $localizacaoId;
        }
        
        if ($tipologia) {
            $sql .= " AND c.tipologia = ?";
            $params[] = $tipologia;
        }
        
        $sql .= " ORDER BY c.nome";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obter casas por localização
     */
    public function getByLocalizacao($localizacaoId) {
        $stmt = $this->db->prepare("
            SELECT c.*, l.nome as localizacao_nome, l.cidade 
            FROM casas c 
            LEFT JOIN localizacoes l ON c.localizacao_id = l.id 
            WHERE c.localizacao_id = ? 
            ORDER BY c.nome
        ");
        $stmt->execute([$localizacaoId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Atualizar estado da casa
     */
    public function updateEstado($id, $estado) {
        $stmt = $this->db->prepare("UPDATE casas SET estado = ? WHERE id = ?");
        return $stmt->execute([$estado, $id]);
    }
    
    /**
     * Calcular valor total da reserva
     */
    public function calcularValorTotal($id, $dataCheckin, $dataCheckout) {
        $stmt = $this->db->prepare("
            SELECT preco_diario, preco_semanal, preco_mensal 
            FROM casas 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        $casa = $stmt->fetch();
        
        // Calcular valor total da reserva
        $valorTotal = 0;
        // ...
        
        return $valorTotal;
    }
    
    /**
     * Obter tipologias disponíveis
     */
    public function getTipologias() {
        $stmt = $this->db->prepare("
            SELECT DISTINCT tipologia 
            FROM casas 
            ORDER BY tipologia
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>
