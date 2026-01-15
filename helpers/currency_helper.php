<?php
/**
 * Helper para formatação de moeda
 * Sistema: Bairro Ferroviário
 * Moeda: Metical (MZN)
 * Desenvolvedor: Oscar Massangaia
 */

/**
 * Formatar valor como moeda Metical
 * @param float $amount Valor a formatar
 * @param int $decimals Número de casas decimais
 * @return string Valor formatado
 */
function formatCurrency($amount, $decimals = 2) {
    return number_format($amount, $decimals, ',', ' ') . ' MZN';
}

/**
 * Formatar valor como moeda para exibição em gráficos
 * @param float $amount Valor a formatar
 * @param int $decimals Número de casas decimais
 * @return string Valor formatado sem símbolo
 */
function formatCurrencyChart($amount, $decimals = 2) {
    return number_format($amount, $decimals, ',', ' ');
}

/**
 * Obter símbolo da moeda
 * @return string Símbolo da moeda
 */
function getCurrencySymbol() {
    return 'MZN';
}

/**
 * Obter nome da moeda
 * @return string Nome da moeda
 */
function getCurrencyName() {
    return 'Metical';
}
?>
