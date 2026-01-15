<?php
/**
 * Modelo de Localizações
 */

class LocalizacaoModel {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    /**
     * Obter todas as localizações
     */
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM localizacoes ORDER BY nome";
        
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
     * Obter localização por ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM localizacoes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Criar nova localização
     */
    public function create($data) {
        $sql = "
            INSERT INTO localizacoes (nome, endereco, cidade, codigo_postal, pais, descricao) 
            VALUES (?, ?, ?, ?, ?, ?)
        ";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $data['nome'],
            $data['endereco'],
            $data['cidade'],
            $data['codigo_postal'],
            $data['pais'] ?? 'Portugal',
            $data['descricao'] ?? null
        ]);
    }
    
    /**
     * Atualizar localização
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $updatableFields = ['nome', 'endereco', 'cidade', 'codigo_postal', 'pais', 'descricao'];
        
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
        
        $sql = "UPDATE localizacoes SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($values);
    }
    
    /**
     * Apagar localização
     */
    public function delete($id) {
        // Verificar se existem casas associadas
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM casas WHERE localizacao_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result['total'] > 0) {
            return false; // Não pode apagar se existirem casas
        }
        
        $stmt = $this->db->prepare("DELETE FROM localizacoes WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Contar localizações
     */
    public function count() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM localizacoes");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Pesquisar localizações
     */
    public function search($term, $limit = null) {
        $sql = "
            SELECT * FROM localizacoes 
            WHERE (nome LIKE ? OR cidade LIKE ? OR endereco LIKE ?) 
            ORDER BY nome
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
    
    /**
     * Obter cidades disponíveis
     */
    public function getCidades() {
        $stmt = $this->db->prepare("
            SELECT DISTINCT cidade 
            FROM localizacoes 
            ORDER BY cidade
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Obter localizações por cidade
     */
    public function getByCidade($cidade) {
        $stmt = $this->db->prepare("
            SELECT * FROM localizacoes 
            WHERE cidade = ? 
            ORDER BY nome
        ");
        $stmt->execute([$cidade]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obter estatísticas de casas por localização
     */
    public function getStatsCasas() {
        $stmt = $this->db->prepare("
            SELECT l.*, COUNT(c.id) as total_casas,
                   SUM(CASE WHEN c.estado = 'disponivel' THEN 1 ELSE 0 END) as casas_disponiveis,
                   SUM(CASE WHEN c.estado = 'ocupado' THEN 1 ELSE 0 END) as casas_ocupadas
            FROM localizacoes l
            LEFT JOIN casas c ON l.id = c.localizacao_id
            GROUP BY l.id
            ORDER BY l.nome
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
