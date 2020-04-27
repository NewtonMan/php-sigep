<?php
require_once ROOT . DS . 'lib' . DS . 'fpdf' . DS . 'fpdf.php';
require_once ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';

use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;

class TestesController extends SigepwebAppController {
    
    public $uses = ['Cadastros.Destino', 'Sigepweb.SigepConta', 'Sigepweb.Servico', 'Sigepweb.Encomenda', 'Sigepweb.Postagem'];
    
    public function index($conta_id){
        $this->autoRender = false;
        $conta = $this->SigepConta->read(null, $conta_id);
        $this->SigepConta->sigepweb_start($conta);
        $phpSigep = new PhpSigep\Services\SoapClient\Real();
        $result = $phpSigep->buscaCliente($this->SigepConta->sigepweb_access_data($conta));
        echo '<pre>';
        var_dump($result);
        echo '</pre>';
    }
    
}