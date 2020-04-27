<?php
require_once ROOT . DS . 'lib' . DS . 'functions.php';

class TotalExpressShell extends AppShell {
    
    public $tasks = ['Embarcador.TotalExpress'];
    
    public function main(){
        $this->TotalExpress->rastreamento();
        $this->TotalExpress->data_coleta();
        $this->TotalExpress->previsao_entrega();
    }
    
}