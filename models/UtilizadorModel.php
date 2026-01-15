<?php
/**
 * Modelo de Utilizadores
 */

class UtilizadorModel {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    /**
     * Encontrar utilizador por email
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM utilizadores WHERE email = ? AND ativo = 1");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    /**
     * Encontrar utilizador por ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM utilizadores WHERE id = ? AND ativo = 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Obter todos os utilizadores
     */
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM utilizadores WHERE ativo = 1 ORDER BY data_criacao DESC";
        
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
     * Criar novo utilizador
     */
    public function create($data) {
        $sql = "INSERT INTO utilizadores (nome, email, senha, perfil, ativo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        $hashedPassword = password_hash($data['senha'], PASSWORD_DEFAULT);
        
        return $stmt->execute([
            $data['nome'],
            $data['email'],
            $hashedPassword,
            $data['perfil'],
            $data['ativo'] ?? 1
        ]);
    }
    
    /**
     * Atualizar utilizador
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        if (isset($data['nome'])) {
            $fields[] = "nome = ?";
            $values[] = $data['nome'];
        }
        
        if (isset($data['email'])) {
            $fields[] = "email = ?";
            $values[] = $data['email'];
        }
        
        if (isset($data['senha']) && !empty($data['senha'])) {
            $fields[] = "senha = ?";
            $values[] = password_hash($data['senha'], PASSWORD_DEFAULT);
        }
        
        if (isset($data['perfil'])) {
            $fields[] = "perfil = ?";
            $values[] = $data['perfil'];
        }
        
        if (isset($data['ativo'])) {
            $fields[] = "ativo = ?";
            $values[] = $data['ativo'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $fields[] = "data_atualizacao = CURRENT_TIMESTAMP";
        $values[] = $id;
        
        $sql = "UPDATE utilizadores SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($values);
    }
    
    /**
     * Apagar (desativar) utilizador
     */
    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE utilizadores SET ativo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Contar utilizadores
     */
    public function count() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM utilizadores WHERE ativo = 1");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Verificar se email já existe
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as total FROM utilizadores WHERE email = ? AND ativo = 1";
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
     * Obter utilizadores por perfil
     */
    public function getByProfile($perfil) {
        $stmt = $this->db->prepare("SELECT * FROM utilizadores WHERE perfil = ? AND ativo = 1 ORDER BY nome");
        $stmt->execute([$perfil]);
        return $stmt->fetchAll();
    }
    
    /**
     * Pesquisar utilizadores
     */
    public function search($term, $limit = null) {
        $sql = "SELECT * FROM utilizadores WHERE 
                (nome LIKE ? OR email LIKE ?) AND ativo = 1 
                ORDER BY nome";
        
        $params = ["%$term%", "%$term%"];
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = $limit;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obter estatísticas de utilizadores por perfil
     */
    public function getStatsByProfile() {
        $stmt = $this->db->prepare("
            SELECT perfil, COUNT(*) as total 
            FROM utilizadores 
            WHERE ativo = 1 
            GROUP BY perfil
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obter últimos utilizadores registados
     */
    public function getLatest($limit = 5) {
        $stmt = $this->db->prepare("
            SELECT * FROM utilizadores 
            WHERE ativo = 1 
            ORDER BY data_criacao DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
?>
