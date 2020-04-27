<?php
App::uses('HttpSocket', 'Network/Http');
class IzettleTask extends Shell {
    
    public $uses = ['NotaFiscal', 'Embarcador.Encomenda', 'LegadoIzettle.FaturamentoArquivoLinha', 'Cadastros.Destino', 'Movimento'];
    
    public function importar(){
        //$this->captura_nfe();
        $this->captura_romaneio();
        $this->captura_rastreio();
        $this->informe_rastreio();
    }
    
    public function captura_nfe(){
        $pasta = ROOT . DS . APP_DIR . DS . 'Plugin' . DS . 'Embarcador' . DS . 'repo' . DS . 'remessa' . DS . getenv('MKTLOG_EMPRESA_ID');
        $nfs = $this->NotaFiscal->find('all', ['conditions' => [
            'Emitente.embarcador' => 1,
            'NotaFiscal.nf_autorizada IS NOT NULL',
            'NotaFiscal.nf_cancelada IS NULL',
            'NotaFiscal.nf_inutilizada IS NULL',
            //'NotaFiscal.modified >= DATE_ADD(NOW(), INTERVAL -7 DAY)',
            '(SELECT id FROM embarcador_encomendas Embarcador WHERE Embarcador.embarcador_id=Emitente.id AND Embarcador.nfe_numero=NotaFiscal.nNF) IS NULL'
        ]]);
        foreach ($nfs as $nf){
            die(print_r($nf));
        }
    }
    
    public function captura_romaneio(){
        $this->out("Captura Romaneios...");
        $page = 1;
        paginacao:
        $itens = $this->Encomenda->find('all', [
            'conditions' => [
                'Embarcador.id' => 14718,
                'Encomenda.cancelado IS NULL',
                'Encomenda.data_romaneio IS NULL',
            ],
            'page' => $page,
            'limit' => 100,
        ]);
        $total = count($itens);
        foreach ($itens as $i){
            $this->out("Consultando NF-e {$i['Encomenda']['nfe_chave']}: ", false);
            $FaturamentoArquivoLinha = $this->FaturamentoArquivoLinha->find('first', [
                'conditions' => [
                    'FaturamentoArquivoLinha.nfe_chave' => $i['Encomenda']['nfe_chave'],
                ],
            ]);
            $movimento_id = @$FaturamentoArquivoLinha['FaturamentoArquivoLinha']['movimento_id'];
            if (!empty($movimento_id)){
                $this->out("MOV {$movimento_id} / ROMANEIO: ", false);
                $movimento = $this->Movimento->read(null, $movimento_id);
                if (!empty($movimento['Movimento']['romaneio_data'])){
                    $this->out("{$movimento['Movimento']['romaneio_data']}");
                    $data_romaneio = DataFromSQL(substr($movimento['Movimento']['romaneio_data'], 0, 10));
                    $this->Encomenda->setDataRomaneio($i['Encomenda']['id'], $data_romaneio);
                    $this->Encomenda->setDataColeta($i['Encomenda']['id'], $data_romaneio);
                    if ($i['City']['capital']==1){
                        $prazo = 5;
                    } else {
                        $prazo = 10;
                    }
                    $time_previsao = strtotime(substr($movimento['Movimento']['romaneio_data'], 0, 10) . " 00:00:00") + 60*60*24*$prazo;
                    $data_previsao = date('d/m/Y', $time_previsao);
                    $this->Encomenda->setDataPrevisao($i['Encomenda']['id'], $data_previsao);
                    $this->Encomenda->setStatusId($i['Encomenda']['id'], 1);
                } else {
                    $this->out('SEM ROMANEIO');
                }
            } else {
                $this->out('FALHA');
            }
        }
        if ($total>=100){
            $page++;
            goto paginacao;
        }
    }
    
    public function captura_rastreio(){
        $this->out("Captura Rastreios...");
        $page = 1;
        paginacao:
        $itens = $this->Encomenda->find('all', [
            'conditions' => [
                'Embarcador.id' => 14718,
                'Encomenda.cancelado IS NULL',
                'Encomenda.codigo_rastreamento IS NULL',
            ],
            'page' => $page,
            'limit' => 100,
        ]);
        $total = count($itens);
        foreach ($itens as $i){
            $FaturamentoArquivoLinha = $this->FaturamentoArquivoLinha->find('first', [
                'conditions' => [
                    'FaturamentoArquivoLinha.nfe_chave' => $i['Encomenda']['nfe_chave'],
                    'IFNULL(FaturamentoArquivoLinha.tracking_number,\'\') != \'\'',
                ],
            ]);
            if (!isset($FaturamentoArquivoLinha['FaturamentoArquivoLinha']['tracking_number'])) continue;
            $this->Encomenda->setCodigoRastreamento($i['Encomenda']['id'], $FaturamentoArquivoLinha['FaturamentoArquivoLinha']['tracking_number']);
        }
        if ($total>=100){
            $page++;
            goto paginacao;
        }
    }
    
    public function informe_rastreio(){
        $this->out("Informe Rastreios...");
        $page = 1;
        paginacao:
        $itens = $this->Encomenda->find('all', [
            'conditions' => [
                'Embarcador.id' => 14718,
                'Encomenda.cancelado IS NULL',
                'Encomenda.codigo_rastreamento IS NOT NULL',
                '(SELECT FaturamentoArquivoLinha.nfe_chave FROM legado_izettle_faturamentos_arquivos_linhas FaturamentoArquivoLinha WHERE FaturamentoArquivoLinha.nfe_chave LIKE Encomenda.nfe_chave AND (IFNULL(FaturamentoArquivoLinha.tracking_number,\'\') LIKE \'\' OR IFNULL(FaturamentoArquivoLinha.tracking_number,\'\') NOT LIKE Encomenda.codigo_rastreamento) LIMIT 1) IS NOT NULL',
            ],
            'page' => $page,
            'limit' => 100,
        ]);
        $total = count($itens);
        foreach ($itens as $i){
            $FaturamentoArquivoLinha = $this->FaturamentoArquivoLinha->find('first', [
                'conditions' => [
                    'FaturamentoArquivoLinha.nfe_chave' => $i['Encomenda']['nfe_chave'],
                ],
            ]);
            $this->FaturamentoArquivoLinha->id = $FaturamentoArquivoLinha['FaturamentoArquivoLinha']['id'];
            $this->FaturamentoArquivoLinha->saveField('tracking_number', $i['Encomenda']['codigo_rastreamento']);
        }
        if ($total>=100){
            $page++;
            goto paginacao;
        }
    }
    
}