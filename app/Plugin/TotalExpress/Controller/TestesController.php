<?php
require_once ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';

class TestesController extends TotalExpressAppController {
    
    public $uses = ['Cadastros.Destino', 'TotalExpress.TotalConta', 'TotalExpress.Servico', 'TotalExpress.Encomenda', 'TotalExpress.Postagem'];
    
    public function index($conta_id){
        $result = $this->TotalConta->TotalExpressCotacao($conta_id, '06765120', 500, 0.01);
        echo '<pre>';
        var_dump($result);
        echo '</pre>';
    }
    
}