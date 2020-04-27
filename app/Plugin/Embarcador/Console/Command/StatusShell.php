<?php
require_once ROOT . DS . 'lib' . DS . 'functions.php';

class StatusShell extends AppShell
{
    public $tasks = ['Embarcador.Status', 'Embarcador.CorreioCrawler'];
    
    public function main(){
        $this->Status->importar();
    }
}