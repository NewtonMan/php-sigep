<?php
require_once ROOT . DS . 'lib' . DS . 'functions.php';

class FeriadosShell extends AppShell {
    
    public $uses = ['Embarcador.Feriado'];
    
    public function main(){
        $provisionarDias = 365;
        for ($x = 0; $x < $provisionarDias; $x++) {
            $time = time()+60*60*24*$x;
            $date = date("Y-m-d", $time);
            $n = date("N", $time);
            if ($n==6 || $n==7){
                $cadastrado = $this->Feriado->find('count', ['conditions' => [
                    'Feriado.data' => $date,
                ]]);
                if ($cadastrado==0) {
                    $this->Feriado->create();
                    $this->Feriado->save([
                        'Feriado' => [
                            'data' => $date,
                            'descricao' => 'Fim de Semana',
                        ],
                    ]);
                }
            }
        }
    }
    
}