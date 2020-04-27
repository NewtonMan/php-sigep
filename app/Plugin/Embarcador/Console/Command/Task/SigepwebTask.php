<?php
require_once ROOT . DS . 'lib' . DS . 'fpdf' . DS . 'fpdf.php';
require_once ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';
require_once ROOT . DS . APP_DIR . DS . 'Plugin' . DS . 'Sigepweb' . DS . 'vendor' . DS . 'vendor' . DS . 'autoload.php';

App::uses('HttpSocket', 'Network/Http');

class SigepwebTask extends Shell {
    
    public $uses = ['Sigepweb.Encomenda', 'Sigepweb.Conta', 'Embarcador.CorreioStatus'];
    
    public function rastreamento(){
        $contas = $this->Conta->find('all', ['conditions' => [
            'Cliente.embarcador' => 1,
        ]]);
        foreach ($contas as $c){
            $this->out("Cliente: {$c['Cliente']['fantasia']}");
            $page = 1;
            codigo:
            $encomendas = $this->Encomenda->find('all', ['conditions' => [
                'Encomenda.sigepweb_conta_id' => $c['Conta']['id'],
                '(SELECT COUNT(*) FROM embarcador_encomendas E WHERE E.embarcador_id=? AND E.nfe_chave=Encomenda.nfe_chave AND E.codigo_rastreamento IS NULL)' => $c['Cliente']['id'],
                'Etiqueta.id IS NOT NULL',
            ], 'limit' => 100, 'page' => $page]);
            foreach ($encomendas as $e) {
                $this->Encomenda->query("UPDATE embarcador_encomendas SET codigo_rastreamento='{$e['Etiqueta']['codigo_com_dv']}' WHERE nfe_chave='{$e['Encomenda']['nfe_chave']}'");
                $this->out("Atualizando Encomenda {$e['Encomenda']['nfe_chave']}: {$e['Etiqueta']['codigo_com_dv']}");
            }
            $total = count($encomendas);
            if ($total==100){
                $page++;
                goto codigo;
            }
        }
    }
    
    public function data_coleta(){
        $contas = $this->Conta->find('all', ['conditions' => [
            'Cliente.embarcador' => 1,
        ]]);
        foreach ($contas as $c) {
            $this->out("Data Coleta de Entragas Cliente: {$c['Cliente']['fantasia']}");
            $sql = "UPDATE embarcador_encomendas E SET E.data_romaneio=(SELECT R.created FROM movimento M JOIN expedicao_romaneio_pedido RP ON RP.movimento_id=M.id JOIN expedicao_romaneio R ON R.id=RP.expedicao_romaneio_id WHERE M.cliente_id=E.embarcador_id AND M.nfe_chave=E.nfe_chave AND fechado_em >= DATE_ADD(NOW(), INTERVAL -90 DAY) LIMIT 1), E.data_coleta=(SELECT R.created FROM movimento M JOIN expedicao_romaneio_pedido RP ON RP.movimento_id=M.id JOIN expedicao_romaneio R ON R.id=RP.expedicao_romaneio_id WHERE M.cliente_id=E.embarcador_id AND M.nfe_chave=E.nfe_chave AND fechado_em >= DATE_ADD(NOW(), INTERVAL -90 DAY) LIMIT 1) WHERE E.embarcador_id={$c['Cliente']['id']} AND E.cancelado IS NULL AND (E.data_romaneio IS NULL OR E.data_coleta IS NULL)";
            $this->Encomenda->query($sql);
        }
    }
    
    public function previsao_entrega(){
        $contas = $this->Conta->find('all', ['conditions' => [
            'Cliente.embarcador' => 1,
        ]]);
        foreach ($contas as $c){
            $this->out("Previsão de Entragas Cliente: {$c['Cliente']['fantasia']}");
            $page = 1;
            previsoes:
            $encomendas = $this->Encomenda->find('all', ['conditions' => [
                'Encomenda.sigepweb_conta_id' => $c['Conta']['id'],
                '(SELECT COUNT(*) FROM embarcador_encomendas E WHERE E.embarcador_id=? AND E.cancelado IS NULL AND E.nfe_chave=Encomenda.nfe_chave AND E.data_coleta IS NOT NULL AND E.data_previsao IS NULL) > 0' => $c['Cliente']['id'],
                'Etiqueta.id IS NOT NULL',
            ], 'limit' => 1000, 'page' => $page]);
            foreach ($encomendas as $e) {
                $prazo = 0; 
                $orcamentos = count($e['Orcamento']);
                if ($orcamentos==0){
                    $this->out("Orcamento Encomenda {$e['Encomenda']['nfe_chave']}: FAZENDO");
                    $this->Encomenda->SigepwebOrcamentos($e['Encomenda']['id']);
                    $e = $this->Encomenda->read(null, $e['Encomenda']['id']);
                }
                foreach ($e['Orcamento'] as $o){
                    if ($o['sigepweb_servico_id']==$e['Etiqueta']['sigepweb_servico_id']){
                        $prazo = $o['prazoEntrega'];
                    }
                }
                if ($prazo>0) {
                    $sql = "UPDATE embarcador_encomendas SET data_previsao=DATE_ADD(data_coleta, INTERVAL {$prazo} DAY) WHERE nfe_chave='{$e['Encomenda']['nfe_chave']}'";
                    $this->Encomenda->query($sql);
                    $this->out("Orcamento Encomenda {$e['Encomenda']['nfe_chave']}: PREVISAO EM DIAS {$prazo}");
                }
            }
            $total = count($encomendas);
            if ($total==1000){
                $page++;
                goto previsoes;
            }
        }
    }
    
}