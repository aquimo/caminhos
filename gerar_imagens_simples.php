<?php
/**
 * Gerar imagens simples para cada tipo de casa
 */

echo "<h2>Gerando Imagens para Tipos de Casa</h2>";

// Criar pasta se não existir
$pasta = 'assets/images/casas';
if (!is_dir($pasta)) {
    mkdir($pasta, 0755, true);
    echo "<p>✅ Pasta criada: $pasta</p>";
}

// Tipos de casa
$tipos = ['CasaTipo0', 'CasaTipo1', 'CasaTipo2', 'CasaTipo3', 'CasaTipo4'];

foreach ($tipos as $tipo) {
    $arquivo = $pasta . '/' . $tipo . '.jpg';
    
    // Criar imagem simples
    $img = imagecreatetruecolor(600, 400);
    
    // Cores baseadas no tipo
    $cores = [
        'CasaTipo0' => [255, 107, 107],  // Vermelho
        'CasaTipo1' => [78, 205, 196],   // Verde água
        'CasaTipo2' => [69, 183, 209],   // Azul
        'CasaTipo3' => [150, 206, 180],  // Verde claro
        'CasaTipo4' => [255, 234, 167]   // Amarelo
    ];
    
    $cor = $cores[$tipo] ?? [100, 100, 100];
    $bg = imagecolorallocate($img, $cor[0], $cor[1], $cor[2]);
    $white = imagecolorallocate($img, 255, 255, 255);
    $black = imagecolorallocate($img, 0, 0, 0);
    
    // Preencher fundo
    imagefill($img, 0, 0, $bg);
    
    // Adicionar borda branca
    imagerectangle($img, 0, 0, 599, 399, $white);
    imagerectangle($img, 10, 10, 589, 389, $white);
    
    // Extrair número do tipo
    $numero = substr($tipo, -1);
    
    // Adicionar texto principal
    imagestring($img, 5, 220, 150, "CASA T$numero", $white);
    imagestring($img, 3, 180, 200, "TIPO $numero", $white);
    
    // Adicionar descrição baseada no tipo
    $descricoes = [
        'CasaTipo0' => 'Quarto Individual',
        'CasaTipo1' => 'Um Quarto',
        'CasaTipo2' => 'Dois Quartos',
        'CasaTipo3' => 'Três Quartos',
        'CasaTipo4' => 'Quatro Quartos'
    ];
    
    imagestring($img, 2, 200, 250, $descricoes[$tipo], $white);
    
    // Adicionar ícone de casa simples
    // Teto
    $pontos_teto = [300, 80, 200, 160, 400, 160];
    imagefilledpolygon($img, $pontos_teto, 3, $white);
    
    // Base da casa
    imagerectangle($img, 200, 160, 400, 280, $white);
    
    // Porta
    imagerectangle($img, 280, 220, 320, 280, $bg);
    
    // Janelas
    imagerectangle($img, 220, 180, 260, 220, $bg);
    imagerectangle($img, 340, 180, 380, 220, $bg);
    
    // Salvar imagem
    imagejpeg($img, $arquivo, 90);
    imagedestroy($img);
    
    echo "<p>✅ Criada: $arquivo</p>";
}

echo "<h3>✅ Todas as imagens foram geradas!</h3>";
echo "<p>Imagens criadas em: $pasta</p>";
echo "<ul>";
foreach ($tipos as $tipo) {
    echo "<li>$tipo.jpg</li>";
}
echo "</ul>";
echo "<p><a href='index.php'>Testar no sistema</a></p>";
?>
