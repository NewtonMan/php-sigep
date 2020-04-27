<?php

require_once ROOT . DS . 'app' . DS . 'Vendor' . DS . 'autoload.php';

class DadosViagemController extends AppController {

    public $uses = ['UserManagement.User', 'Inbox'];

    public function index() {
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->autoRender = false;
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(TRUE);
            $spreadsheet = $reader->load($this->request->data['User']['planilha']['tmp_name']);
            $worksheet = $spreadsheet->getActiveSheet();
            $data = [];
            $linha = 0;
            foreach ($worksheet->getRowIterator() as $row){
                $linha++;
                if ($linha == 1) continue; // PULAR CABECALHO
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
                $coluna = 0;
                foreach ($cellIterator as $cell) {
                    $coluna++;
                    $data[$linha][$coluna] = trim($cell->getValue());
                }
            }
            foreach ($data as $linha => $colunas){
                $CPF = (int)onlyNumbers($colunas[1]);
                $transporte_ida = $colunas[2];
                $traslado_ida = $colunas[3];
                $traslado_volta = $colunas[4];
                $transporte_volta = $colunas[5];
                $u = $this->User->find('first', ['conditions' => [
                    'User.CPF' => $CPF,
                ]]);
                if (empty($u['User']['id'])){
                    $this->Session->setFlash("ERRO, linha {$linha}: CPF {$CPF} não encontrado.", 'mensagens/alerta');
                } elseif (!empty($transporte_ida) && !empty($traslado_ida) && !empty($traslado_volta) && !empty($transporte_volta)) {
                    $this->User->id = $u['User']['id'];
                    $this->User->saveField('transporte_ida', $transporte_ida);
                    $this->User->saveField('traslado_ida', $traslado_ida);
                    $this->User->saveField('traslado_volta', $traslado_volta);
                    $this->User->saveField('transporte_volta', $transporte_volta);
                    
                    $inbox = [
                        'form_user_id' => 3,
                        'to_user_id' => $u['User']['id'],
                        'message' => 'Os dados da sua viagem já estão disponíveis no App da Convenção Extrafarma 2019. Confira agora!',
                    ];
                    $this->Inbox->create();
                    $this->Inbox->save($inbox);
                    
                    $this->Session->setFlash("OK, linha {$linha}: Dados de Viagem salvos.", 'mensagens/sucesso');
                } else {
                    $this->Session->setFlash("ERRO, linha {$linha}: Dados de Viagem incompletos.", 'mensagens/alerta');
                }
            }
            $this->getRefer();
        } else {
            $this->setRefer();
            $this->request->data = [
                'total' => $this->User->find('count', [
                    'conditions' => [
                        'User.active' => 1,
                    ],
                ]),
                'completos' => $this->User->find('count', [
                    'conditions' => [
                        'IFNULL(User.transporte_ida,\'\') != \'\'',
                        'IFNULL(User.traslado_ida,\'\') != \'\'',
                        'IFNULL(User.traslado_volta,\'\') != \'\'',
                        'IFNULL(User.transporte_volta,\'\') != \'\'',
                        'User.active' => 1,
                    ],
                ]),
            ];
        }
    }

}
