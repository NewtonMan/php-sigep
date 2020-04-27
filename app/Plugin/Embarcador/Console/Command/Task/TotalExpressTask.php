<?php
class TotalExpressTask extends Shell {
    
    public $uses = ['TotalExpress.TotalEncomenda', 'TotalExpress.TotalConta', 'Embarcador.Encomenda', 'Embarcador.Feriado', 'Embarcador.Status'];
    
    public function rastreamento(){
        $contas = $this->TotalConta->find('all', ['conditions' => [
            'Cliente.embarcador' => 1,
        ]]);
        foreach ($contas as $c){
            $this->out("Cliente: {$c['Cliente']['fantasia']}");
            $page = 1;
            codigo:
            $encomendas = $this->TotalEncomenda->find('all', ['conditions' => [
                'TotalEncomenda.total_express_conta_id' => $c['TotalConta']['id'],
                'TotalEncomenda.nfe_chave IN (SELECT E.nfe_chave FROM embarcador_encomendas E WHERE E.embarcador_id=? AND E.cancelado IS NULL AND E.data_conclusao IS NULL)' => $c['Cliente']['id'],
            ], 'limit' => 1000, 'page' => $page]);
            foreach ($encomendas as $e) {
                $ee = $this->Encomenda->find('first', ['conditions' => [
                    'Encomenda.nfe_chave' => $e['TotalEncomenda']['nfe_chave'],
                ]]);
                if ($e['TotalConta']['agencia_id']!=$ee['Encomenda']['transportador_id']){
                    $this->Encomenda->setTransportadorId($ee['Encomenda']['id'], $e['TotalConta']['agencia_id']);
                }
                if (empty($ee['Encomenda']['codigo_rastreamento'])) {
                    $this->Encomenda->setCodigoRastreamento($ee['Encomenda']['id'], $e['TotalEncomenda']['id']);
                }
                if (!empty($e['TotalStatus']['embarcador_status_id']) && $e['TotalStatus']['embarcador_status_id']!=$ee['Encomenda']['status_id']){
                    $this->Encomenda->setStatusId($ee['Encomenda']['id'], $e['TotalStatus']['embarcador_status_id']);
                }
                if (!empty($e['TotalStatus']['embarcador_status_id'])){
                    $st = $this->Status->read(null, $e['TotalStatus']['embarcador_status_id']);
                    if ($st['Status']['conclui']==1){
                        $dh = (empty($e['TotalEncomenda']['status_dh']) ? $e['TotalEncomenda']['modified']:$e['TotalEncomenda']['status_dh']);
                        $this->Encomenda->setDataConclusao($ee['Encomenda']['id'], $dh);
                    }
                }
            }
            $total = count($encomendas);
            if ($total==1000){
                $page++;
                goto codigo;
            }
        }
    }
    
    public function data_coleta(){
        $contas = $this->TotalConta->find('all', ['conditions' => [
            'Cliente.embarcador' => 1,
        ]]);
        foreach ($contas as $c) {
            $this->out("Data Coleta de Entragas Cliente: {$c['Cliente']['fantasia']}");
            $sql = "UPDATE embarcador_encomendas E SET E.data_romaneio=(SELECT R.created FROM movimento M JOIN expedicao_romaneio_pedido RP ON RP.movimento_id=M.id JOIN expedicao_romaneio R ON R.id=RP.expedicao_romaneio_id WHERE M.cliente_id=E.embarcador_id AND M.nfe_chave=E.nfe_chave AND fechado_em >= DATE_ADD(NOW(), INTERVAL -90 DAY) LIMIT 1), E.data_coleta=(SELECT R.created FROM movimento M JOIN expedicao_romaneio_pedido RP ON RP.movimento_id=M.id JOIN expedicao_romaneio R ON R.id=RP.expedicao_romaneio_id WHERE M.cliente_id=E.embarcador_id AND M.nfe_chave=E.nfe_chave AND fechado_em >= DATE_ADD(NOW(), INTERVAL -90 DAY) LIMIT 1) WHERE E.embarcador_id={$c['Cliente']['id']} AND E.cancelado IS NULL AND (E.data_romaneio IS NULL OR E.data_coleta IS NULL)";
            $this->Encomenda->query($sql);
        }
    }
    
    public function previsao_entrega(){
        $contas = $this->TotalConta->find('all', ['conditions' => [
            'Cliente.embarcador' => 1,
        ]]);
        foreach ($contas as $c){
            $this->out("Cliente: {$c['Cliente']['fantasia']}");
            $encomendas = $this->TotalEncomenda->find('all', ['conditions' => [
                'TotalEncomenda.total_express_conta_id' => $c['TotalConta']['id'],
                'TotalEncomenda.nfe_chave IN (SELECT E.nfe_chave FROM embarcador_encomendas E WHERE E.embarcador_id=? AND E.cancelado IS NULL AND E.data_coleta IS NOT NULL AND E.data_previsao IS NULL)' => $c['Cliente']['id'],
            ]]);
            foreach ($encomendas as $e) {
                $encomenda = $this->Encomenda->find('first', ['conditions' => [
                    'Encomenda.nfe_chave' => $e['TotalEncomenda']['nfe_chave'],
                    'Encomenda.cancelado IS NULL',
                ]]);
                if (empty($encomenda['Encomenda']['id'])) continue;
                try {
                    $data = $this->TotalConta->TotalExpressCotacao($c['TotalConta']['id'], $e['DestinoLocal']['cep'], ($e['TotalEncomenda']['nfe_peso_gr'] / 1000), $e['TotalEncomenda']['nfe_valor']);
                } catch (Exception $ex) {
                    continue;
                }
                $prazo = @$data[0]->DadosFrete->Prazo;
                if ($prazo>0) {
                    $sql = "CALL SP_EMBARCADOR_DATA_PREVISAO({$encomenda['Encomenda']['id']}, $prazo);";
                    $this->Encomenda->query($sql);
                    $this->out("Orcamento Encomenda {$e['TotalEncomenda']['nfe_chave']}: PREVISAO EM DIAS {$prazo}");
                }
            }
        }
    }
    
}