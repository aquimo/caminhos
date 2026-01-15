<?php
/**
 * Configuração da Base de Dados
 * Sistema de Gestão de Casas para Hospedagem
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'caminhos_hospedagem';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    private $conn;

    /**
     * Estabelecer conexão com a base de dados
     */
    public function connect() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch(PDOException $e) {
            echo "Erro de conexão: " . $e->getMessage();
        }

        return $this->conn;
    }

    /**
     * Fechar conexão com a base de dados
     */
    public function close() {
        $this->conn = null;
    }

    /**
     * Executar query preparada
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            echo "Erro na query: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Inserir registo
     */
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array_values($data));
            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            echo "Erro ao inserir: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Atualizar registo
     */
    public function update($table, $data, $where, $whereParams = []) {
        $setClause = [];
        foreach ($data as $column => $value) {
            $setClause[] = "$column = ?";
        }
        $setClause = implode(', ', $setClause);
        
        $sql = "UPDATE $table SET $setClause WHERE $where";
        $params = array_merge(array_values($data), $whereParams);
        
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch(PDOException $e) {
            echo "Erro ao atualizar: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Apagar registo
     */
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch(PDOException $e) {
            echo "Erro ao apagar: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Selecionar um registo
     */
    public function selectOne($table, $where = '1=1', $params = [], $columns = '*') {
        $sql = "SELECT $columns FROM $table WHERE $where LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch(PDOException $e) {
            echo "Erro ao selecionar: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Selecionar múltiplos registos
     */
    public function select($table, $where = '1=1', $params = [], $columns = '*', $orderBy = '', $limit = '') {
        $sql = "SELECT $columns FROM $table WHERE $where";
        
        if (!empty($orderBy)) {
            $sql .= " ORDER BY $orderBy";
        }
        
        if (!empty($limit)) {
            $sql .= " LIMIT $limit";
        }
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            echo "Erro ao selecionar: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Contar registos
     */
    public function count($table, $where = '1=1', $params = []) {
        $sql = "SELECT COUNT(*) as total FROM $table WHERE $where";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return $result['total'];
        } catch(PDOException $e) {
            echo "Erro ao contar: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Verificar se registo existe
     */
    public function exists($table, $where, $params = []) {
        $sql = "SELECT 1 FROM $table WHERE $where LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch() !== false;
        } catch(PDOException $e) {
            echo "Erro ao verificar existência: " . $e->getMessage();
            return false;
        }
    }
}

// Criar instância global da base de dados
$database = new Database();
$db = $database->connect();
?>
