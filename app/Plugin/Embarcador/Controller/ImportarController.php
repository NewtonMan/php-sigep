<?php
require_once ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\RowIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\CellIterator;

class ImportarController extends EmbarcadorAppController {
    
    public $uses = ['Embarcador.Encomenda', 'Cadastros.Destino'];
    
    public function index() {
        if ($this->request->is('post') || $this->request->is('put')){
            $posicaoCampo = [
                'nada',
                'declaracao_numero',
                'nfe_serie',
                'nfe_numero',
                'nfe_chave',
                'transportador_cpf_cnpj',
                'data_romaneio',
                'data_coleta',
                'data_previsao',
                'data_conclusao',
                'codigo_rastreamento',
                'encomenda_status'
            ];
            //die(print_r($this->request->data));
            if ($this->request->data['User']['planilha']['error']==0){
                $processadas = 0;
                $naoEncontradas = 0;
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(TRUE);
                $spreadsheet = $reader->load($this->request->data['User']['planilha']['tmp_name']);
                $spreadsheet->setActiveSheetIndex(0);
                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();
                for ($l=2; $l <= $highestRow; $l++) {
                    foreach ($posicaoCampo as $position => $campo) {
                        $data[$campo] = $worksheet->getCellByColumnAndRow($position, $l)->getCalculatedValue();
                    }
                    $data['transportador_cpf_cnpj'] = onlyNumbers($data['transportador_cpf_cnpj']);
                    $data['encomenda_status'] = explode('-', $data['encomenda_status'], 2);
                    $data['encomenda_status'] = onlyNumbers(@$data['encomenda_status'][0]);
                    $data['data_romaneio'] = (empty($data['data_romaneio']) ? '':date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($data['data_romaneio'])));
                    $data['data_coleta'] = (empty($data['data_coleta']) ? '':date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($data['data_coleta'])));
                    $data['data_previsao'] = (empty($data['data_previsao']) ? '':date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($data['data_previsao'])));
                    $data['data_conclusao'] = (empty($data['data_conclusao']) ? '':date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($data['data_conclusao'])));
                    //die(print_r($data));
                    $encomenda_id = null;
                    if (!empty($data['declaracao_numero'])) {
                        $e = $this->Encomenda->find('first', ['conditions' => [
                            'Encomenda.embarcador_id' => $this->request->data['User']['cliente_id'],
                            'Encomenda.declaracao_numero' => $data['declaracao_numero'],
                        ]]);
                        if (!empty($e['Encomenda']['id'])){
                            $encomenda_id = $e['Encomenda']['id'];
                        } else {
                            $naoEncontradas++;
                        }
                    } elseif (strlen($data['nfe_chave'])==44) {
                        $e = $this->Encomenda->find('first', ['conditions' => [
                            'Encomenda.embarcador_id' => $this->request->data['User']['cliente_id'],
                            'Encomenda.nfe_chave' => $data['nfe_chave'],
                        ]]);
                        if (!empty($e['Encomenda']['id'])){
                            $encomenda_id = $e['Encomenda']['id'];
                        } else {
                            $naoEncontradas++;
                        }
                    } elseif ($data['nfe_serie']!='' && !empty($data['nfe_numero'])) {
                        $e = $this->Encomenda->find('first', ['conditions' => [
                            'Encomenda.embarcador_id' => $this->request->data['User']['cliente_id'],
                            'Encomenda.nfe_serie' => $data['nfe_serie'],
                            'Encomenda.nfe_numero' => $data['nfe_numero'],
                        ]]);
                        if (!empty($e['Encomenda']['id'])){
                            $encomenda_id = $e['Encomenda']['id'];
                        } else {
                            $naoEncontradas++;
                        }
                    }
                    
                    if (!is_null($encomenda_id)) {
                        $processadas++;
                        $transportador = (empty($data['transportador_cpf_cnpj']) ? $this->Destino->read(null, $this->request->data['User']['transportador_id']):$this->Destino->find('first', ['conditions' => [
                            'Destino.cpf_cnpj' => $data['transportador_cpf_cnpj'],
                            'Destino.transportador' => 1,
                        ]]));
                        $transportador_id = $transportador['Destino']['id'];
                        
                        $this->Encomenda->id = $encomenda_id;
                        $e = $this->Encomenda->read(null, $encomenda_id);
                        
                        $this->Encomenda->setHistorico($encomenda_id, 'Alterações via planilha iniciando...');
                        
                        if ($e['Encomenda']['transportador_id']!=$transportador_id){
                            $this->Encomenda->setTransportadorId($encomenda_id, $transportador_id);
                        }
                        
                        if (!empty($data['data_romaneio']) && $e['Encomenda']['data_romaneio']!=$data['data_romaneio']){
                            $this->Encomenda->setDataRomaneio($encomenda_id, $data['data_romaneio']);
                        }
                        
                        if (!empty($data['data_coleta']) && $e['Encomenda']['data_coleta']!=$data['data_coleta']){
                            $this->Encomenda->setDataColeta($encomenda_id, $data['data_coleta']);
                        }
                        
                        if (!empty($data['data_previsao']) && $e['Encomenda']['data_previsao']!=$data['data_previsao']){
                            $this->Encomenda->setDataPrevisao($encomenda_id, $data['data_previsao']);
                        }
                        
                        if (!empty($data['encomenda_status']) && $e['Status']['id']!=$data['encomenda_status']){
                            $this->Encomenda->setStatusId($encomenda_id, $data['encomenda_status']);
                        }
                        
                        if (!empty($data['data_conclusao']) && $e['Encomenda']['data_conclusao']!=$data['data_conclusao']){
                            $this->Encomenda->setDataConclusao($encomenda_id, $data['data_conclusao']);
                        }
                        
                        if (!empty($data['codigo_rastreamento']) && $e['Encomenda']['codigo_rastreamento']!=$data['codigo_rastreamento']){
                            $this->Encomenda->setCodigoRastreamento($encomenda_id, $data['codigo_rastreamento']);
                        }
                        
                        $this->Encomenda->setHistorico($encomenda_id, 'Alterações via planilha finalizada');
                    }
                }
                $this->Session->setFlash("Linhas processadas: {$processadas} / Linhas não localizadas: {$naoEncontradas}", 'mensagens/informacao');
            } elseif ($this->request->data['User']['planilha']['error']>0){
                $this->Session->setFlash('ERRO: Tamanho do arquivo excede o tamanho limite de 10MB.');
            }
        }
        $clientes = $this->Destino->find('list', ['conditions' => [
            'Destino.cliente' => 1,
            'Destino.id' => $this->Destino->meusClientes(AuthComponent::User('id')),
            'Destino.embarcador' => 1,
        ]]);
        $transportadores = $this->Destino->find('list', ['conditions' => [
            'Destino.transportador' => 1,
            'Destino.embarcador' => 1,
        ]]);
        $this->set(compact('transportadores','clientes'));
    }
    
}