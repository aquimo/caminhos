<?php
/**
 * Helper para gestão de URLs e redirecionamentos
 */

class UrlHelper {
    
    /**
     * Gerar URL para uma rota
     */
    public static function base($route = '') {
        $base_url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' 
            ? "https://" 
            : "http://";
        $base_url .= $_SERVER['HTTP_HOST'];
        $base_url .= rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        
        return $base_url . ($route ? '?route=' . $route : '');
    }
    
    /**
     * Redirecionar para uma rota
     */
    public static function redirect($route) {
        header('Location: ' . self::base($route));
        exit;
    }
    
    /**
     * Obter URL atual
     */
    public static function current() {
        return self::base(isset($_GET['route']) ? $_GET['route'] : '');
    }
    
    /**
     * Obter rota atual
     */
    public static function getRoute() {
        return isset($_GET['route']) ? $_GET['route'] : 'dashboard';
    }
    
    /**
     * Verificar se é a rota atual
     */
    public static function isCurrent($route) {
        return self::getRoute() === $route;
    }
    
    /**
     * Adicionar parâmetro à URL
     */
    public static function addParam($param, $value) {
        $route = self::getRoute();
        return self::base($route) . '&' . $param . '=' . $value;
    }
    
    /**
     * Gerar URL para assets
     */
    public static function asset($file) {
        $base_url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' 
            ? "https://" 
            : "http://";
        $base_url .= $_SERVER['HTTP_HOST'];
        $base_url .= '/caminhos/assets/' . ltrim($file, '/');
        
        return $base_url;
    }
    
    /**
     * Voltar para a página anterior
     */
    public static function back() {
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            self::redirect('dashboard');
        }
        exit;
    }
}
?>
