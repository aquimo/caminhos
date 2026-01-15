<?php
/**
 * Helper para gestão de sessões
 */

class SessionHelper {
    
    /**
     * Iniciar sessão
     */
    public static function init() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Definir valor na sessão
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Obter valor da sessão
     */
    public static function get($key, $default = null) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
    
    /**
     * Verificar se chave existe na sessão
     */
    public static function has($key) {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remover valor da sessão
     */
    public static function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Destruir sessão
     */
    public static function destroy() {
        session_destroy();
        $_SESSION = [];
    }
    
    /**
     * Definir mensagem flash
     */
    public static function setFlash($key, $message) {
        $_SESSION['flash'][$key] = $message;
    }
    
    /**
     * Obter mensagem flash
     */
    public static function getFlash($key, $default = null) {
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return $default;
    }
    
    /**
     * Verificar se existe mensagem flash
     */
    public static function hasFlash($key) {
        return isset($_SESSION['flash'][$key]);
    }
}
?>
