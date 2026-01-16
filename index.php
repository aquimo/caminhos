<?php
/**
 * Ponto de Entrada Principal
 * Sistema de Gestão de Casas para Hospedagem
 * 
 * @author Oscar Massangaia
 * @institution Universidade Aberta ISCED
 * @course Engenharia Informática
 * @version 1.0
 */

// Definir constantes da aplicação
define('APP_PATH', __DIR__);
define('CONTROLLERS_PATH', APP_PATH . '/controllers/');
define('MODELS_PATH', APP_PATH . '/models/');
define('VIEWS_PATH', APP_PATH . '/views/');
define('HELPERS_PATH', APP_PATH . '/helpers/');
define('ASSETS_PATH', '/assets/');

// Incluir ficheiros de configuração e helpers
require_once 'config/database.php';
require_once 'helpers/session_helper.php';
require_once 'helpers/auth_helper.php';
require_once 'helpers/url_helper.php';
require_once 'helpers/currency_helper.php';

// Iniciar sessão
SessionHelper::init();

// Obter a rota da URL
$route = isset($_GET['route']) ? $_GET['route'] : 'dashboard';

// Rotas da aplicação
$routes = [
    'dashboard' => 'DashboardController@index',
    'login' => 'AuthController@login',
    'logout' => 'AuthController@logout',
    'casas' => 'CasaController@index',
    'casas/criar' => 'CasaController@criar',
    'casas/editar' => 'CasaController@editar',
    'casas/ver' => 'CasaController@ver',
    'casas/apagar' => 'CasaController@apagar',
    'casas/disponiveis' => 'CasaController@getDisponiveis',
    'utilizadores' => 'UtilizadorController@index',
    'utilizadores/criar' => 'UtilizadorController@criar',
    'utilizadores/editar' => 'UtilizadorController@editar',
    'utilizadores/ver' => 'UtilizadorController@ver',
    'hospedes' => 'HospedeController@index',
    'hospedes/criar' => 'HospedeController@criar',
    'hospedes/ver' => 'HospedeController@ver',
    'hospedes/checkout' => 'HospedeController@checkout',
    'reservas' => 'ReservaController@index',
    'reservas/criar' => 'ReservaController@criar',
    'reservas/checkin' => 'ReservaController@checkin',
    'reservas/checkout' => 'ReservaController@checkout',
    'relatorios' => 'RelatorioController@index',
    'relatorios/financeiros' => 'RelatorioController@financeiros',
    'relatorios/ocupacao' => 'RelatorioController@ocupacao'
];

// Verificar se o utilizador está autenticado (exceto para login)
if ($route !== 'login' && !AuthHelper::isLoggedIn()) {
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
