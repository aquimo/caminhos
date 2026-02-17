<?php
/**
 * Diagnóstico de problemas com imagens
 */

// Definir constantes
define('APP_PATH', __DIR__);
define('ASSETS_PATH', 'assets/');

// Verificar pastas
echo "<h2>Diagnóstico de Imagens - Bairro Ferroviário</h2>";

echo "<h3>1. Verificação de Pastas</h3>";
$pastas = [
    'assets/images',
    'assets/images/home',
    'assets/images/casas'
];

foreach ($pastas as $pasta) {
    $caminho_completo = APP_PATH . '/' . $pasta;
    $existe = is_dir($caminho_completo);
    $permissao = is_readable($caminho_completo);
    
    echo "<p><strong>$pasta:</strong> ";
    echo $existe ? "✅ Existe" : "❌ Não existe";
    echo " | ";
    echo $permissao ? "✅ Legível" : "❌ Não legível";
    echo "</p>";
}

echo "<h3>2. Verificação de Imagens</h3>";
$imagens = [
    'assets/images/logo-bairro-ferroviario.png',
    'assets/images/home/hero-background.jpg',
    'assets/images/casas/placeholder.png'
];

foreach ($imagens as $imagem) {
    $caminho_completo = APP_PATH . '/' . $imagem;
    $existe = file_exists($caminho_completo);
    $tamanho = $existe ? filesize($caminho_completo) : 0;
    
    echo "<p><strong>$imagem:</strong> ";
    echo $existe ? "✅ Existe" : "❌ Não existe";
    if ($existe) {
        echo " | Tamanho: " . number_format($tamanho / 1024, 2) . " KB";
    }
    echo "</p>";
}

echo "<h3>3. Teste de URL com UrlHelper</h3>";
require_once 'helpers/url_helper.php';

foreach ($imagens as $imagem) {
    $url = UrlHelper::asset($imagem);
    echo "<p><strong>$imagem:</strong> <a href='$url' target='_blank'>$url</a></p>";
}

echo "<h3>4. Configuração do Servidor</h3>";
echo "<p><strong>Base URL:</strong> " . UrlHelper::base() . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>PHP_SELF:</strong> " . $_SERVER['PHP_SELF'] . "</p>";

echo "<h3>5. Teste de Exibição</h3>";
echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
echo "<h4>Logo:</h4>";
echo "<img src='" . UrlHelper::asset('logo-bairro-ferroviario.png') . "' alt='Logo' style='max-width: 200px; height: auto;'>";
echo "</div>";

echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
echo "<h4>Placeholder Casa:</h4>";
echo "<img src='" . UrlHelper::asset('casas/placeholder.png') . "' alt='Placeholder' style='max-width: 200px; height: auto;'>";
echo "</div>";

echo "<h3>6. Soluções Recomendadas</h3>";
echo "<ul>";
echo "<li>✅ Corrigido ASSETS_PATH para 'assets/' (sem barra no início)</li>";
echo "<li>✅ Adicionado onerror nas imagens para fallback</li>";
echo "<li>✅ Criada pasta assets/images/casas</li>";
echo "<li>✅ Adicionado placeholder para casas</li>";
echo "</ul>";

echo "<p><strong>Próximos passos:</strong></p>";
echo "<ol>";
echo "<li>Verifique se as imagens estão aparecendo acima</li>";
echo "<li>Se não, verifique as permissões das pastas</li>";
echo "<li>Teste o acesso direto às URLs mostradas</li>";
echo "</ol>";
?>
