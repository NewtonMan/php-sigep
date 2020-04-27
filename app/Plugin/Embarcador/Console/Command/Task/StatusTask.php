<?php

App::uses('HttpSocket', 'Network/Http');

class StatusTask extends Shell {

    public $tasks = ['Embarcador.CorreioCrawler'];
    
    public $uses = ['Embarcador.Encomenda'];
    
    public function importar() {
        $this->correios();
    }

    public function correios() {
        // CONSULTA ETIQUETA NA BASE DE DADOS
        $page = 1;
        $limite = 100;
        paginar:
        $this->out("Rastreio PG: {$page}");
        $encomendas = $this->Encomenda->find('all', ['conditions' => [
            'Encomenda.codigo_rastreamento IS NOT NULL',
            //'Encomenda.codigo_rastreamento' => 'PS623381312BR',
            'Encomenda.data_romaneio IS NOT NULL',
            'Encomenda.cancelado IS NULL',
            'Encomenda.data_conclusao IS NULL',
            'Transportador.correios' => 1,
        ], 'page' => $page, 'limit' => $limite]);
        foreach ($encomendas as $encomenda){
            try {
                $data = $this->CorreioCrawler->consultar($encomenda['Encomenda']['codigo_rastreamento']);
                if (!$data['success']) continue;
                if ($encomenda['Encomenda']['status_id'] != $data['data'][0]['status_id']){
                    $this->Encomenda->setStatusId($encomenda['Encomenda']['id'], $data['data'][0]['status_id']);
                    if ($data['data'][0]['status_id']==2){
                        $this->Encomenda->setDataConclusao($encomenda['Encomenda']['id'], DataFromSQL($data['data'][0]['data']));
                    }
                }
            } catch (Exception $ex) {
            }
            sleep(5);
        }
        $total = count($encomendas);
        if ($total==$limite){
            $this->out("Rastreio PG: {$page} FIM");
            $page++;
            goto paginar;
        } else {
            $this->out("Rastreio PG: {$page} FIM");
        }
    }
    
}
