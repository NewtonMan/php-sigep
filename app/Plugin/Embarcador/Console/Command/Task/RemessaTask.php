<?php
App::uses('HttpSocket', 'Network/Http');
class RemessaTask extends Shell {
    
    public $uses = ['Embarcador.Encomenda', 'Legado.RemessaArquivo', 'Cadastros.Destino', 'Movimento', 'Legado.RemessaArquivo', 'Legado.Operacao'];
    
    public $pasta = null;
    public $pasta_processed = null;
    
    public function importar(){
        $this->pasta = WWW_ROOT . 'embarcador';
        $this->pasta_processed = WWW_ROOT . 'embarcador' . DS . 'processed';
        try {
            $this->captura_legado();
        } catch (Exception $ex) {
            
        }
        if (!is_dir($this->pasta_processed)) mkdir($this->pasta_processed, 0777, true);
        $this->out("Pasta: {$this->pasta}");
        if ($handle = opendir($this->pasta)) {
            while (false !== ($entry = readdir($handle))) {
                $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
                $filename = $this->pasta . DS . $entry;
                $filename_processed = $this->pasta_processed . DS . $entry;
                if ($ext=='xml' && !file_exists($filename_processed)){
                    $this->Encomenda->importNFe($filename, getenv('MKTLOG_EMPRESA_ID'));
                    copy($filename, $filename_processed);
                } elseif($entry!='.' && $entry!='..' && $entry!='processed'){
                    unlink($filename);
                }
            }
            closedir($handle);
        }
        $this->captura_romaneio();
    }
    
    public function captura_romaneio(){
        $this->out("Captura Romaneios...");
        $page = 1;
        paginacao:
        $itens = $this->Movimento->find('all', [
            'conditions' => [
                'Cliente.embarcador' => 1,
                'Movimento.nfe_chave IS NOT NULL',
                'Movimento.id IN (SELECT movimento_id FROM expedicao_romaneio_pedido RP WHERE RP.movimento_id=Movimento.id)',
                'Movimento.nfe_chave IN (SELECT nfe_chave FROM embarcador_encomendas Encomenda WHERE Encomenda.embarcador_id=Movimento.cliente_id AND Encomenda.nfe_chave=Movimento.nfe_chave AND Encomenda.data_romaneio IS NULL)',
            ],
            'page' => $page,
            'limit' => 100,
        ]);
        $total = count($itens);
        foreach ($itens as $i){
            $e = $this->Encomenda->find('first', ['conditions' => [
                'Encomenda.nfe_chave' => $i['Movimento']['nfe_chave'],
                'IFNULL(Encomenda.data_romaneio,\'\')=\'\'',
            ]]);
            if (!empty($e['Encomenda']['id'])){
                $data = DataFromSQL(substr($i['Movimento']['romaneio_data'], 0, 10));
                $this->out("\tEncomenda {$e['Encomenda']['id']} = NF-e {$i['Movimento']['nfe_chave']} = {$data}");
                $this->Encomenda->setDataRomaneio($e['Encomenda']['id'], $data);
                $this->Encomenda->setDataColeta($e['Encomenda']['id'], $data);
            }
        }
        if ($total>=100){
            $page++;
            goto paginacao;
        }
    }
    
    public function captura_legado(){
        $this->out("Embarcador capturando NFs no sistema legado...");
        $embarcadores = $this->Destino->find('all', ['conditions' => [
            'Destino.embarcador' => 1,
        ]]);
        foreach ($embarcadores as $i){
            $this->out("\t{$i['Destino']['fantasia']}...");
            $page = 1;
            paginacao:
            $arquivos = $this->RemessaArquivo->find('all', [
                'conditions' => [
                    'RemessaArquivo.tipo' => 'ped',
                    'RemessaArquivo.cliente_id' => $i['Destino']['id'],
                    'RemessaArquivo.processado' => 1,
                    'LENGTH(RemessaArquivo.nfe_chave)' => 44,
                    'RemessaArquivo.created >= DATE_ADD(NOW(), INTERVAL -7 DAY)',
                ],
                'limit' => 100,
                'page' => $page,
            ]);
            $total = count($arquivos);
            $this->out("\t\tLote {$page} = {$total} arquivos...");
            foreach ($arquivos as $a){
                $filename = $this->pasta . DS . $a['RemessaArquivo']['nfe_chave'] . '-nfe.xml';
                $filename_processed = $this->pasta_processed . DS . $a['RemessaArquivo']['nfe_chave'] . '-nfe.xml';
                $this->out("\t\tNF-e {$a['RemessaArquivo']['nfe_chave']}...", false);
                if (!file_exists($filename_processed) && !file_exists($filename)){
                    file_put_contents($filename, $a['RemessaArquivo']['arquivo_conteudo']);
                    $this->out("COLOCADA NA FILA");
                } else {
                    $this->out("JA NA FILA");
                }
            }
            if ($total==100){
                $page++;
                goto paginacao;
            }
        }
    }
    
}