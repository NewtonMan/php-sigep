<?php
require_once ROOT . DS . 'lib' . DS . 'functions.php';

class RemessaShell extends AppShell {
    
    public $tasks = ['Embarcador.Remessa'];
    
    public function startup(){
        putenv("DATABASE_SUFFIX={$this->args[0]}");
        putenv("MKTLOG_EMPRESA_ID={$this->args[1]}");
        $_SERVER['HTTP_HOST'] = $this->args[0] . '.mkt-log.com.br';
    }
    
    public function main(){
        $this->Remessa->importar();
    }
    
}