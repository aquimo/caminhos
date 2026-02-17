<?php
/**
 * Criar imagens para cada tipo de casa
 */

echo "<h2>Criando Imagens para Tipos de Casa</h2>";

// Criar pasta se não existir
if (!is_dir('assets/images/casas')) {
    mkdir('assets/images/casas', 0755, true);
    echo "<p>✅ Pasta criada: assets/images/casas</p>";
}

// Definir tipos e suas características
$tipos_casa = [
    'CasaTipo0' => ['T0', 'Quarto Individual', '#FF6B6B', 'Perfeito para estudantes'],
    'CasaTipo1' => ['T1', 'Um Quarto', '#4ECDC4', 'Ideal para casais'],
    'CasaTipo2' => ['T2', 'Dois Quartos', '#45B7D1', 'Ótimo para pequenas famílias'],
    'CasaTipo3' => ['T3', 'Três Quartos', '#96CEB4', 'Perfeito para famílias'],
    'CasaTipo4' => ['T4', 'Quatro Quartos', '#FFEAA7', 'Excelente para grupos grandes']
];

foreach ($tipos_casa as $filename => $info) {
    $codigo = $info[0];
    $descricao = $info[1];
    $cor = $info[2];
    $subtitulo = $info[3];
    
    // Criar imagem
    $img = imagecreatetruecolor(600, 400);
    
    // Converter cor hex para RGB
    $r = hexdec(substr($cor, 1, 2));
    $g = hexdec(substr($cor, 3, 2));
    $b = hexdec(substr($cor, 5, 2));
    
    // Cores
    $bg = imagecolorallocate($img, $r, $g, $b);
    $white = imagecolorallocate($img, 255, 255, 255);
    $dark = imagecolorallocate($img, 0, 0, 0);
    
    // Preencher fundo
    imagefill($img, 0, 0, $bg);
    
    // Adicionar gradiente sutil
    for ($i = 0; $i < 100; $i++) {
        $alpha = $i / 100;
        $color = imagecolorallocatealpha($img, 255, 255, 255, 127 * $alpha);
        imageline($img, 0, $i * 4, 600, $i * 4, $color);
    }
    
    // Adicionar borda
    imagerectangle($img, 0, 0, 599, 399, $white);
    
    // Adicionar texto principal grande
    imagettftext($img, 48, 0, 200, 150, $white, null, $codigo);
    
    // Adicionar descrição
    imagettftext($img, 24, 0, 150, 200, $white, null, $descricao);
    
    // Adicionar subtítulo
    imagettftext($img, 18, 0, 120, 250, $white, null, $subtitulo);
    
    // Adicionar ícone de casa simples
    $casa_points = [
        300, 100,  // topo
        200, 180,  // esquerda
        200, 280,  // esquerda baixo
        400, 280,  // direita baixo
        400, 180   // direita
    ];
    imagefilledpolygon($img, $casa_points, 5, $white);
    
    // Salvar imagem
    $filepath = "assets/images/casas/{$filename}.jpg";
    imagejpeg($img, $filepath, 90);
    imagedestroy($img);
    
    echo "<p>✅ Criada: {$filepath}</p>";
}

echo "<h3>✅ Todas as imagens foram criadas com sucesso!</h3>";
echo "<p>Imagens criadas:</p>";
echo "<ul>";
echo "<li>CasaTipo0.jpg - Quarto Individual</li>";
echo "<li>CasaTipo1.jpg - Um Quarto</li>";
echo "<li>CasaTipo2.jpg - Dois Quartos</li>";
echo "<li>CasaTipo3.jpg - Três Quartos</li>";
echo "<li>CasaTipo4.jpg - Quatro Quartos</li>";
echo "</ul>";
echo "<p><a href='index.php'>Voltar para o sistema</a></p>";
?>
