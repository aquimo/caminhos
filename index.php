<?php
/**
 * Ponto de Entrada Principal
 * Sistema de Gestão de Casas para Hospedagem
 * 
 * @author Oscar Massangaia
 * @institução Universidade Aberta ISCED
 * @curso Gestão de Sistemas de Informação
 * @version 1.0
 */

// Definir constantes da aplicação
define('APP_PATH', __DIR__);
define('CONTROLLERS_PATH', APP_PATH . '/controllers/');
define('MODELS_PATH', APP_PATH . '/models/');
define('VIEWS_PATH', APP_PATH . '/views/');
define('HELPERS_PATH', APP_PATH . '/helpers/');
define('ASSETS_PATH', 'assets/');

// Incluir ficheiros de configuração e helpers
require_once 'config/database.php';
require_once 'helpers/session_helper.php';
require_once 'helpers/auth_helper.php';
require_once 'helpers/url_helper.php';
require_once 'helpers/currency_helper.php';

// Iniciar sessão
SessionHelper::init();

// Obter a rota da URL
$route = isset($_GET['route']) ? $_GET['route'] : '';

// Rotas da aplicação
$routes = [
    '' => 'HomeController@index',  // Página inicial padrão
    'home' => 'HomeController@index',
    'disponibilidade' => 'DisponibilidadeController@index',
    'dashboard' => 'DashboardController@index',
    'login' => 'AuthController@login',
    'logout' => 'AuthController@logout',
    'casas' => 'CasaController@index',
    'casas/criar' => 'CasaController@criar',
    'casas/editar' => 'CasaController@editar',
    'casas/ver' => 'CasaController@ver',
    'casas/apagar' => 'CasaController@apagar',
    'casas/disponiveis' => 'CasaController@getDisponiveis',
    'disponibilidade/buscarDisponiveis' => 'DisponibilidadeController@buscarDisponiveis',
    'utilizadores' => 'UtilizadorController@index',
    'utilizadores/criar' => 'UtilizadorController@criar',
    'utilizadores/editar' => 'UtilizadorController@editar',
    'utilizadores/ver' => 'UtilizadorController@ver',
    'utilizadores/apagar' => 'UtilizadorController@apagar',
    'hospedes' => 'HospedeController@index',
    'hospedes/criar' => 'HospedeController@criar',
    'hospedes/ver' => 'HospedeController@ver',
    'hospedes/checkout' => 'HospedeController@checkout',
    'reservas' => 'ReservaController@index',
    'reservas/criar' => 'ReservaController@criar',
    'reservas/ver' => 'ReservaController@ver',
    'reservas/cancelar' => 'ReservaController@cancelar',
    'reservas/checkin' => 'ReservaController@checkin',
    'reservas/processarCheckin' => 'ReservaController@processarCheckin',
    'reservas/checkout' => 'ReservaController@checkout',
    'reservas/processarCheckout' => 'ReservaController@processarCheckout',
    'reservas/getCasasDisponiveis' => 'ReservaController@getCasasDisponiveis',
    'relatorios' => 'RelatorioController@index',
    'relatorios/financeiros' => 'RelatorioController@financeiros',
    'relatorios/ocupacao' => 'RelatorioController@ocupacao',
    'relatorios/exportarFinanceiro' => 'RelatorioController@exportarFinanceiro',
    'relatorios/exportarOcupacao' => 'RelatorioController@exportarOcupacao'
];

// Verificar se o utilizador está autenticado (exceto para login, página inicial e disponibilidade)
if ($route !== 'login' && $route !== '' && $route !== 'home' && $route !== 'disponibilidade' && !AuthHelper::isLoggedIn()) {
    header('Location: index.php?route=login');
    exit;
}

// Encaminhar para o controlador apropriado
if (isset($routes[$route])) {
    list($controllerName, $method) = explode('@', $routes[$route]);
    
    $controllerFile = CONTROLLERS_PATH . $controllerName . '.php';
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $controller = new $controllerName();
        
        if (method_exists($controller, $method)) {
            $controller->$method();
        } else {
            echo "Método $method não encontrado no controlador $controllerName";
        }
    } else {
        echo "Controlador $controllerName não encontrado";
    }
} else {
    echo "Rota não encontrada: $route";
}
?>
