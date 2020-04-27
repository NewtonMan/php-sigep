<?php
require_once ROOT . '/lib/functions.php';
require_once ROOT . DS . 'lib' . DS . 'fpdf' . DS . 'fpdf.php';
require_once ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';

use NFePHP\NFe\Common\Standardize;

class RemessaShell extends AppShell {
    
    public $tasks = ['TotalExpress.Postagem'];
    
    public function startup() {
        putenv("DATABASE_SUFFIX={$this->args[0]}");
        putenv("MKTLOG_EMPRESA_ID={$this->args[1]}");
    }
    
    public function main(){
        $this->Postagem->contas();
    }
    
}