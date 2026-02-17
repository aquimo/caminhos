<?php
/**
 * Teste de funcionamento das imagens das casas
 */

echo "<h2>🧪 Teste de Imagens das Casas - Bairro Ferroviário</h2>";

// Verificar pasta e imagens
echo "<h3>📁 Verificação de Arquivos</h3>";
$pasta = 'assets/images/casas';
$tipos = ['CasaTipo0', 'CasaTipo1', 'CasaTipo2', 'CasaTipo3', 'CasaTipo4'];

foreach ($tipos as $tipo) {
    $arquivo = $pasta . '/' . $tipo . '.jpg';
    $existe = file_exists($arquivo);
    $tamanho = $existe ? filesize($arquivo) : 0;
    
    echo "<p><strong>$tipo.jpg:</strong> ";
    echo $existe ? "✅ Existe" : "❌ Não existe";
    if ($existe) {
        echo " | Tamanho: " . number_format($tamanho / 1024, 2) . " KB";
    }
    echo "</p>";
}

echo "<h3>🖼️ Teste Visual das Imagens</h3>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;'>";

foreach ($tipos as $tipo) {
    $arquivo = $pasta . '/' . $tipo . '.jpg';
    if (file_exists($arquivo)) {
        echo "<div style='border: 1px solid #ddd; border-radius: 8px; overflow: hidden; text-align: center;'>";
        echo "<img src='$arquivo' alt='$tipo' style='width: 100%; height: 150px; object-fit: cover;'>";
        echo "<p style='margin: 10px; font-weight: bold;'>$tipo</p>";
        echo "</div>";
    }
}
echo "</div>";

echo "<h3>🔗 Teste de URLs com UrlHelper</h3>";
require_once 'helpers/url_helper.php';

foreach ($tipos as $tipo) {
    $url = UrlHelper::asset('casas/' . $tipo . '.jpg');
    echo "<p><strong>$tipo:</strong> <a href='$url' target='_blank'>$url</a></p>";
}

echo "<h3>✅ Resumo da Implementação</h3>";
echo "<ul>";
echo "<li>✅ <strong>Página Inicial:</strong> Usa CasaTipo{T}.jpg para cada casa</li>";
echo "<li>✅ <strong>Página Disponibilidade:</strong> Usa CasaTipo{T}.jpg para cada casa</li>";
echo "<li>✅ <strong>Sistema Administrativo:</strong> Usa CasaTipo{T}.jpg quando não há imagens</li>";
echo "<li>✅ <strong>Fallback:</strong> Placeholder.png se a imagem não carregar</li>";
echo "<li>✅ <strong>Terminologia:</strong> 'Apartamento' trocado por 'Casa'</li>";
echo "</ul>";

echo "<h3>🚀 Links para Testar</h3>";
echo "<p><a href='index.php' style='background: #0b5b36; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🏠 Página Inicial</a></p>";
echo "<p><a href='index.php?route=disponibilidade' style='background: #0b5b36; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔍 Disponibilidade</a></p>";
echo "<p><a href='index.php?route=login' style='background: #0b5b36; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Sistema Administrativo</a></p>";

echo "<h3>🎯 O que foi implementado:</h3>";
echo "<ol>";
echo "<li><strong>Imagens por Tipo:</strong> CasaTipo0.jpg, CasaTipo1.jpg, etc.</li>";
echo "<li><strong>Mapeamento Automático:</strong> T0 → CasaTipo0.jpg, T1 → CasaTipo1.jpg</li>";
echo "<li><strong>Fallback Robusto:</strong> Se imagem falhar, mostra gradiente + texto</li>";
echo "<li><strong>Terminologia Corrigida:</strong> 'Apartamento' → 'Casa'</li>";
echo "<li><strong>Views Atualizadas:</strong> home, disponibilidade, casas/ver, casas/editar</li>";
echo "</ol>";
?>
