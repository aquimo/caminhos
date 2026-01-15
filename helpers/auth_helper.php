<?php
/**
 * Helper para autenticação e autorização
 */

require_once 'session_helper.php';

class AuthHelper {
    
    /**
     * Verificar se utilizador está autenticado
     */
    public static function isLoggedIn() {
        return SessionHelper::has('utilizador_id');
    }
    
    /**
     * Obter ID do utilizador autenticado
     */
    public static function getUserId() {
        return SessionHelper::get('utilizador_id');
    }
    
    /**
     * Obter dados do utilizador autenticado
     */
    public static function getUser() {
        return SessionHelper::get('utilizador');
    }
    
    /**
     * Obter perfil do utilizador autenticado
     */
    public static function getProfile() {
        return SessionHelper::get('utilizador_perfil');
    }
    
    /**
     * Verificar se utilizador tem perfil específico
     */
    public static function hasProfile($profile) {
        return SessionHelper::get('utilizador_perfil') === $profile;
    }
    
    /**
     * Verificar se utilizador tem permissão (baseado no perfil)
     */
    public static function hasPermission($requiredProfile) {
        $userProfile = self::getProfile();
        
        // Gestor Geral tem acesso a tudo
        if ($userProfile === 'gestor_geral') {
            return true;
        }
        
        // Verificar permissões específicas
        $permissions = [
            'secretaria' => ['secretaria', 'checkin', 'checkout'],
            'contabilidade' => ['contabilidade', 'relatorios', 'pagamentos'],
            'gestor_condominios' => ['gestor_condominios', 'casas', 'localizacoes']
        ];
        
        if (isset($permissions[$userProfile])) {
            return in_array($requiredProfile, $permissions[$userProfile]);
        }
        
        return false;
    }
    
    /**
     * Verificar se utilizador pode aceder a determinada rota
     */
    public static function canAccess($route) {
        $userProfile = self::getProfile();
        
        // Gestor Geral tem acesso a tudo
        if ($userProfile === 'gestor_geral') {
            return true;
        }
        
        // Definir rotas permitidas por perfil
        $allowedRoutes = [
            'secretaria' => ['dashboard', 'reservas', 'reservas/criar', 'reservas/checkin', 'reservas/checkout'],
            'contabilidade' => ['dashboard', 'relatorios', 'relatorios/financeiros', 'pagamentos'],
            'gestor_condominios' => ['dashboard', 'casas', 'casas/criar', 'casas/editar', 'localizacoes']
        ];
        
        if (isset($allowedRoutes[$userProfile])) {
            return in_array($route, $allowedRoutes[$userProfile]);
        }
        
        return false;
    }
    
    /**
     * Fazer login do utilizador
     */
    public static function login($utilizador) {
        SessionHelper::set('utilizador_id', $utilizador['id']);
        SessionHelper::set('utilizador', $utilizador);
        SessionHelper::set('utilizador_perfil', $utilizador['perfil']);
        SessionHelper::set('utilizador_nome', $utilizador['nome']);
    }
    
    /**
     * Fazer logout do utilizador
     */
    public static function logout() {
        SessionHelper::destroy();
    }
    
    /**
     * Redirecionar se não estiver autenticado
     */
    public static function requireAuth() {
        if (!self::isLoggedIn()) {
            header('Location: index.php?route=login');
            exit;
        }
    }
    
    /**
     * Redirecionar se não tiver permissão
     */
    public static function requirePermission($requiredProfile) {
        if (!self::hasPermission($requiredProfile)) {
            SessionHelper::setFlash('error', 'Não tem permissão para aceder a esta página.');
            header('Location: index.php?route=dashboard');
            exit;
        }
    }
    
    /**
     * Obter nome completo do perfil
     */
    public static function getProfileName($profile) {
        $profiles = [
            'gestor_geral' => 'Gestor Geral',
            'secretaria' => 'Secretaria',
            'contabilidade' => 'Contabilidade',
            'gestor_condominios' => 'Gestor de Condomínios'
        ];
        
        return isset($profiles[$profile]) ? $profiles[$profile] : $profile;
    }
}
?>
