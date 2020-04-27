<?php
require_once ROOT . '/lib/functions.php';
require_once ROOT . DS . 'lib' . DS . 'fpdf' . DS . 'fpdf.php';
require_once ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';
require_once ROOT . DS . APP_DIR . DS . 'Plugin' . DS . 'Sigepweb' . DS . 'vendor' . DS . 'vendor' . DS . 'autoload.php';

use PhpSigep\Model\Diretoria;
use NFePHP\NFe\Common\Standardize;

class RemessaShell extends AppShell {
    
    public $tasks = ['Sigepweb.Postagem'];
    
    public function startup() {
        putenv("DATABASE_SUFFIX={$this->args[0]}");
        putenv("MKTLOG_EMPRESA_ID={$this->args[1]}");
    }
    
    public function main(){
        $this->Postagem->contas();
    }
    
}