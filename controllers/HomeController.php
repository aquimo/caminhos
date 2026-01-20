<?php
/**
 * Controlador da Página Inicial
 * Sistema de Gestão de Casas para Hospedagem
 * 
 * @author Oscar Massangaia
 * @institution Universidade Aberta ISCED
 * @course Engenharia Informática
 * @version 1.0
 */

require_once 'models/CasaModel.php';
require_once 'models/LocalizacaoModel.php';

class HomeController {
    
    /**
     * Mostrar página inicial
     */
    public function index() {
        // Obter casas disponíveis para mostrar na página inicial
        $casaModel = new CasaModel();
        $casas_disponiveis = $casaModel->getCasasParaHome();
        
        // Obter localizações para o formulário
        $localizacaoModel = new LocalizacaoModel();
        $localizacoes = $localizacaoModel->getAll();
        
        $page_title = 'Bairro Ferroviário - Sistema de Gestão de Casas';
        ob_start();
        include 'views/home/index.php';
        $content = ob_get_clean();
        include 'views/layouts/main.php';
    }
    
    /**
     * Obter descrição da tipologia
     */
    private function getDescricaoTipologia($tipologia) {
        $descricoes = [
            'T0' => 'Perfeito para estudantes e profissionais que buscam conforto e privacidade.',
            'T1' => 'Ideal para casais ou profissionais que necessitam de mais espaço.',
            'T2' => 'Espaçoso e confortável, perfeito para pequenas famílias.',
            'T3' => 'Amplo e bem distribuído, ideal para famílias maiores.',
            'T4' => 'Muito espaçoso, ideal para famílias grandes ou grupos.'
        ];
        
        return $descricoes[$tipologia] ?? 'Apartamento confortável e bem equipado.';
    }
}
?>
