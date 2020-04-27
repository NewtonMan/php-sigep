<?php
require_once ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';

use NFePHP\DA\NFe\Danfe;
use NFePHP\DA\Legacy\FilesFolders;

class EncomendasController extends EmbarcadorAppController {
    
    public $uses = ['Embarcador.Encomenda', 'TotalExpress.TotalConta', 'Embarcador.State', 'Embarcador.Zone', 'Embarcador.City', 'Cadastros.Destino'];
    
    public function index(){
        $this->setRefer();
        $criterios = $this->crudModelSearch('Destino');
        $criterios['Destino.cliente'] = 1;
        $criterios['Destino.embarcador'] = 1;
        $this->request->data['lista'] = $this->paginate('Destino', $criterios);
    }
    
    public function danfe($encomenda_id){
        $encomenda = $this->Encomenda->read(null, $encomenda_id);
        $arqPDF = WWW_ROOT . 'files' . DS . "DANFE-P{$encomenda['Encomenda']['nfe_chave']}.PDF";
        if (!file_exists($arqPDF)){
            $filename = WWW_ROOT . 'files' . DS . 'logo-' . $encomenda['Encomenda']['embarcador_id'] . '.jpg';
            if (file_exists($filename)){
                $logo = $filename;
            }

            $rnfe = WWW_ROOT. 'rnfe' . DS . "{$encomenda['Encomenda']['nfe_chave']}-nfe.xml";

            if(is_file($rnfe)){
                $docxml = file_get_contents($rnfe);
            } else {
                die('XML não localizado.');
            }
            try {
                $danfe = new Danfe($docxml);
                $danfe->debugMode(false);
                $danfe->creditsIntegratorFooter('UNITYLOG - www.unitylog.com.br');
                $pdf = $danfe->render($logo);
                file_put_contents($arqPDF, $pdf);
            } catch (InvalidArgumentException $e) {
                echo "Ocorreu um erro durante o processamento :" . $e->getMessage();
            }
        }
        return $this->redirect(str_replace(WWW_ROOT, '/', $arqPDF));
    }
    
    public function cancelar($encomenda_id){
        $this->Encomenda->id = $encomenda_id;
        $this->Encomenda->saveField('cancelado', date('d/m/Y H:i'));
        $this->Encomenda->setHistorico($encomenda_id, "Cancelou o Monitoramento da Encomenda");
        return $this->redirect("/embarcador/encomendas/painel/{$encomenda_id}");
    }
    
    public function monitoramento($embarcador_id){
        $embarcador = $this->Destino->read(null, $embarcador_id);
        $this->crumbs[] = [
            'name' => $embarcador['Destino']['fantasia'],
            'href' => "/embarcador/encomendas/monitoramento/{$embarcador['Destino']['id']}",
            'active' => false,
        ];
        $this->set('embarcador_id', $embarcador_id);
        $this->setRefer();
        $city_id = (!empty($_GET['city_id']) ? $_GET['city_id']:null);
        $zone_id = (!empty($_GET['zone_id']) ? $_GET['zone_id']:null);
        $state_id = (!empty($_GET['state_id']) ? $_GET['state_id']:null);
        $criterios = $this->crudModelSearch('Encomenda');
        $criterios["Encomenda.cancelado"] = null;
        $criterios['Encomenda.embarcador_id'] = $embarcador_id;
        $transportador_id = (isset($_GET['transportador_id']) ? ($_GET['transportador_id']=='0' ? null:$_GET['transportador_id']):null);
        if (@"{$_GET['transportador_id']}"!=='') $criterios['Encomenda.transportador_id'] = $transportador_id;
        $criterios[] = '(Transportador.embarcador=1 OR Encomenda.transportador_id IS NULL)';
        if (!is_null($city_id)){
            $criterios['LocalEntrega.cMun'] = $city_id;
            $state = $this->State->read(null, $state_id);
            $this->crumbs[] = [
                'name' => "{$state['State']['letter']} - {$state['State']['name']}",
                'href' => "/embarcador/encomendas/monitoramento/{$embarcador['Destino']['id']}?state_id={$state_id}",
                'active' => false,
            ];
            $zone = $this->Zone->read(null, $zone_id);
            $this->crumbs[] = [
                'name' => "DDD {$zone['Zone']['DDD']} - {$zone['Zone']['nome']}",
                'href' => "/embarcador/encomendas/monitoramento/{$embarcador['Destino']['id']}?state_id={$state_id}&zone_id={$zone_id}",
                'active' => false,
            ];
            $city = $this->City->read(null, $city_id);
            $this->crumbs[] = [
                'name' => "{$city['City']['name']}",
                'href' => "/embarcador/encomendas/monitoramento/{$embarcador['Destino']['id']}?state_id={$state_id}&zone_id={$zone_id}&city_id={$city_id}",
                'active' => false,
            ];
        } elseif (!is_null($zone_id)){
            $criterios['City.zone_id'] = $zone_id;
            $state = $this->State->read(null, $state_id);
            $this->crumbs[] = [
                'name' => "{$state['State']['letter']} - {$state['State']['name']}",
                'href' => "/embarcador/encomendas/monitoramento/{$embarcador['Destino']['id']}?state_id={$state_id}",
                'active' => false,
            ];
            $zone = $this->Zone->read(null, $zone_id);
            $this->crumbs[] = [
                'name' => "DDD {$zone['Zone']['DDD']} - {$zone['Zone']['nome']}",
                'href' => "/embarcador/encomendas/monitoramento/{$embarcador['Destino']['id']}?state_id={$state_id}&zone_id={$zone_id}",
                'active' => false,
            ];
        } elseif (!is_null($state_id)){
            $criterios['City.state_id'] = $state_id;
            $state = $this->State->read(null, $state_id);
            $this->crumbs[] = [
                'name' => "{$state['State']['letter']} - {$state['State']['name']}",
                'href' => "/embarcador/encomendas/monitoramento/{$embarcador['Destino']['id']}?state_id={$state_id}",
                'active' => false,
            ];
        }
        if (!empty(@$_GET['nf'])){
            $criterios[] = "(Encomenda.nfe_numero LIKE '%{$_GET['nf']}%' OR Encomenda.nfe_chave LIKE '%{$_GET['nf']}%')";
        }
        if (!empty(@$_GET['dest'])){
            $criterios[] = [
                'OR' => [
                    'Destinatario.fantasia LIKE ?' => "%{$_GET['dest']}%",
                    'Destinatario.nome_razao LIKE ?' => "%{$_GET['dest']}%",
                    'Destinatario.endereco LIKE ?' => "%{$_GET['dest']}%",
                    'Destinatario.cpf_cnpj' => onlyNumbers($_GET['dest']),
                ],
            ];
        }
        //die(print_r($criterios));
        $this->request->data['stage_criterios'] = [
            1 => [
                'Encomenda.cancelado IS NULL',
                'Encomenda.data_coleta IS NULL',
                'Encomenda.status_id NOT IN (2,3)',
                'Status.ocorrencia IS NULL',
            ],
            2 => [
                'Encomenda.cancelado IS NULL',
                'Encomenda.data_coleta IS NOT NULL',
                'Encomenda.data_previsao IS NULL',
                'Encomenda.status_id NOT IN (2,3)',
                'Status.ocorrencia IS NULL',
            ],
            3 => [
                'Encomenda.cancelado IS NULL',
                'Encomenda.data_coleta IS NOT NULL',
                'Encomenda.data_previsao >= DATE_ADD(NOW(), INTERVAL -2 DAY)',
                'Encomenda.status_id NOT IN (2,3)',
                'Status.ocorrencia IS NULL',
            ],
            4 => [
                'Encomenda.cancelado IS NULL',
                'Encomenda.data_coleta IS NOT NULL',
                'Encomenda.data_previsao < DATE_ADD(NOW(), INTERVAL -2 DAY)',
                'Encomenda.status_id NOT IN (2,3)',
                'Status.ocorrencia IS NULL',
            ],
            5 => [
                'Encomenda.cancelado IS NULL',
                'Encomenda.status_id NOT IN (2,3)',
                'Status.ocorrencia IS NOT NULL',
                '(Encomenda.ocorrencia_responsavel_id IS NULL OR Encomenda.ocorrencia_responsavel_id=?)' => AuthComponent::User('id'),
            ],
            6 => [
                'Encomenda.cancelado IS NULL',
                'Encomenda.status_id IN (2,3)',
                'Encomenda.data_conclusao <= IFNULL(Encomenda.data_previsao, Encomenda.data_conclusao)',
                'Status.conclui' => 1,
            ],
            7 => [
                'Encomenda.cancelado IS NULL',
                'Encomenda.status_id IN (2,3)',
                'Encomenda.data_conclusao > Encomenda.data_previsao',
                'Status.conclui' => 1,
            ],
            8 => [
                'Encomenda.cancelado IS NULL',
                'Encomenda.data_coleta IS NOT NULL',
                'Encomenda.status_id NOT IN (2,3)',
                'IFNULL(Encomenda.codigo_rastreamento,\'\')=\'\'',
                'Status.ocorrencia IS NULL',
                'Status.conclui IS NULL',
            ],
        ];

        $this->request->data['criterios'] = $criterios;
        
        if (!empty($_GET['stage_id'])){
            $criterios = array_merge($criterios, $this->request->data['stage_criterios'][$_GET['stage_id']]);
        }
        $this->request->data['lista'] = $this->paginate('Encomenda', $criterios);
        $embarcador = $this->Destino->read(null, $embarcador_id);
        $this->request->data['Embarcador'] = $embarcador['Destino'];
    }
    
    public function painel($id){
        $this->layout = 'popup';
        if ($this->request->is('post') || $this->request->is('put')){
            $old = $this->Encomenda->read(null, $id);
            if ($old['Encomenda']['status_id']!=$this->request->data['Encomenda']['status_id']){
                $this->Encomenda->setStatusId($id, $this->request->data['Encomenda']['status_id']);
            }
            if ($old['Encomenda']['data_romaneio']!=$this->request->data['Encomenda']['data_romaneio']){
                $this->Encomenda->setDataRomaneio($id, $this->request->data['Encomenda']['data_romaneio']);
            }
            if ($old['Encomenda']['data_coleta']!=$this->request->data['Encomenda']['data_coleta']){
                $this->Encomenda->setDataColeta($id, $this->request->data['Encomenda']['data_coleta']);
            }
            if ($old['Encomenda']['codigo_rastreamento']!=$this->request->data['Encomenda']['codigo_rastreamento']){
                $this->Encomenda->setCodigoRastreamento($id, $this->request->data['Encomenda']['codigo_rastreamento']);
            }
            if ($old['Encomenda']['data_previsao']!=$this->request->data['Encomenda']['data_previsao']){
                $this->Encomenda->setDataPrevisao($id, $this->request->data['Encomenda']['data_previsao']);
            }
            if ($old['Encomenda']['data_conclusao']!=$this->request->data['Encomenda']['data_conclusao']){
                $this->Encomenda->setDataConclusao($id, $this->request->data['Encomenda']['data_conclusao']);
            }
            if (!empty($this->request->data['Encomenda']['ocorrencia_responsavel_id'])){
                $this->Encomenda->setOcorrenciaResponsavelId($id, $this->request->data['Encomenda']['ocorrencia_responsavel_id']);
            }
            if (!empty($this->request->data['Encomenda']['ocorrencia_responsavel_id'])){
                $this->Encomenda->setTransportadorId($id, $this->request->data['Encomenda']['transportador_id']);
            }
            if (!empty($this->request->data['Encomenda']['historico'])){
                $this->Encomenda->setHistorico($id, $this->request->data['Encomenda']['historico']);
            }
            return $this->redirect('/embarcador/encomendas/painel/'.$id.'?v='.time());
        } else {
            $this->Encomenda->recursive = 2;
            $this->request->data = $this->Encomenda->read(null, $id);
            $eid = 0;
            if ($this->request->data['Transportador']['cpf_cnpj']==73939449000193){
                $tc = $this->TotalConta->find('first', ['conditions' => [
                    'TotalConta.cliente_id' => $this->request->data['Embarcador']['id'],
                ]]);
                $eid = $tc['TotalConta']['eid'];
            }
            $this->set(compact('eid'));
            if ($this->request->data['Status']['ocorrencia']==1){
                if (empty($this->request->data['Encomenda']['ocorrencia_responsavel_id'])){
                    $this->Encomenda->setOcorrenciaResponsavelId($id, AuthComponent::User('id'));
                }
                if (empty($this->request->data['Encomenda']['ocorrencia_lida'])){
                    $this->Encomenda->setOcorrenciaLida($id);
                }
            }
        }
        $status = $this->Encomenda->Status->find('list');
        $modals = $this->Encomenda->Modal->find('list');
        $modalidadeFretes = $this->Encomenda->ModalidadeFrete->find('list');
        $usuarios = $this->Encomenda->OcorrenciaResponsavel->find('list', ['conditions' => [
            'modulo_embarcador' => 1,
        ]]);
        $transportadoras = $this->Encomenda->Transportador->find('list', ['conditions' => [
            'Transportador.transportador' => 1,
            'Transportador.embarcador' => 1,
        ]]);
        $this->set(compact('modalidadeFretes', 'modals', 'status', 'usuarios', 'transportadoras'));
    }
   
        public function relatorio($id) {
            
        if ($this->request->is('post') || $this->request->is('put')) {
            $data1 = $this->request->data['data1'];
            $data2 = $this->request->data['data2'];
            $lista = $this->Encomenda->find('all', ['conditions' => [
                    'Encomenda.embarcador_id' => $id,
                    'Encomenda.data_romaneio IS NOT NULL',
                    'Encomenda.created BETWEEN ? AND ?' => [$data1, $data2],
            ]]);
            $this->set('lista', $lista);

            //print_r($lista);
             $this->render('relatorio_csv', 'exportar_excel');
        }
    }
}