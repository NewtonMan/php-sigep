<?php
class TabelasController extends EmbarcadorAppController {
    
    public $uses = ['Cadastros.Destino', 'Embarcador.Modal', 'Embarcador.TmsTableCostRange', 'Embarcador.TmsTableCostRoute', 'Embarcador.TmsTableCostValue', 'Embarcador.TmsTableCostMethod', 'Embarcador.TmsRegion', 'Embarcador.TmsModel', 'Embarcador.TmsTableCost', 'Embarcador.ModalidadeFrete', 'Embarcador.City', 'Embarcador.Zone', 'Embarcador.State'];
    
    public $layout = 'popup';
    
    public function index($embarcador_id){
        $this->setRefer();
        $criterios = $this->crudModelSearch('TmsTableCost');
        $criterios = [
            'Embarcadora.id' => $embarcador_id,
            $criterios,
        ];
        $this->request->data['CRUD']['titulo'] = 'Gerenciamento de Tabelas de Frete';
        $this->request->data['CRUD']['cols'] = [
            'transportadora_id' => [
                'label' => 'Transportadora',
                'model' => 'Transportadora',
                'field' => 'fantasia',
            ],
            'embarcador_tms_cost_model_id' => [
                'label' => 'Modal',
                'model' => 'Modal',
                'field' => 'name',
            ],
            'embarcador_tms_cost_method_id' => [
                'label' => 'Tipo Tabela',
                'model' => 'TmsTableCostMethod',
                'field' => 'name',
            ],
            'name' => [
                'label' => 'Nome Tabela',
            ],
            'value_minimum' => [
                'label' => 'Valor Mínimo',
            ],
        ];
        $this->request->data['CRUD']['data'] = $this->paginate('TmsTableCost', $criterios);
        $this->request->data['CRUD']['model'] = 'TmsTableCost';
        $this->request->data['CRUD']['actions'] = [
            'custom' => [
                [
                    'href' => '/embarcador/tabelas/values/'.$embarcador_id.'/',
                    'btn' => 'default',
                    'icon' => 'fas fa-dollar-sign',
                    'label' => 'Valores',
                    'target' => '_self',
                ],
                [
                    'href' => '/embarcador/tabelas/routes/'.$embarcador_id.'/',
                    'btn' => 'default',
                    'icon' => 'fa fa-map-marked',
                    'label' => 'Rotas',
                    'target' => '_self',
                ],
                [
                    'href' => '/embarcador/tabelas/ranges/'.$embarcador_id.'/',
                    'btn' => 'default',
                    'icon' => 'fa fa-receipt',
                    'label' => 'Faixas',
                    'target' => '_self',
                ],
            ],
            'create' => '/embarcador/tabelas/form/'.$embarcador_id.'/',
            'update' => '/embarcador/tabelas/form/'.$embarcador_id.'/',
            'delete' => '/embarcador/tabelas/delete/'.$embarcador_id.'/',
        ];
        $this->render('SimpleCrud.Crud/index');
    }
    
    public function form($embarcadora_id, $id = null){
        $transportadoras = $this->Destino->find('list', ['conditions' => [
            'Destino.transportador' => 1,
            'Destino.embarcador' => 1,
        ]]);
        $modal = $this->Modal->find('list', ['order' => [
            'Modal.name' => 'ASC',
        ]]);
        $methods = $this->TmsTableCostMethod->find('list', ['order' => [
            'TmsTableCostMethod.name' => 'ASC',
        ]]);
        if ($this->request->is('post') || $this->request->is('put')){
            if (empty($id)){
                $this->TmsTableCost->create();
                $this->request->data['TmsTableCost']['embarcadora_id'] = $embarcadora_id;
            } else {
                $this->TmsTableCost->id = $id;
            }
            if ($this->TmsTableCost->save($this->request->data)){
                $this->Session->setFlash('Definições de Tabela Salva!', 'mensagens/sucesso');
                return $this->redirect("/embarcador/tabelas/index/{$embarcadora_id}");
            } else {
                $this->Session->setFlash('ERRO: Verifique o Formulário!', 'mensagens/alerta');
            }
        } elseif (!empty($id)) {
            $this->request->data = $this->TmsTableCost->read(null, $id);
        }
        $this->set(compact('transportadoras', 'regions', 'modal', 'methods'));
    }
    
    public function ranges($embarcadora_id, $embarcador_tms_cost_id){
        if ($this->request->is('post') || $this->request->is('put')){
            foreach ($this->request->data as $x => $v){
                $this->request->data[$x]['TmsTableCostRange']['embarcador_tms_cost_id'] = $embarcador_tms_cost_id;
                if ($this->request->data[$x]['TmsTableCostRange']['excluir']==1 && !empty($this->request->data[$x]['TmsTableCostRange']['id'])){
                    $this->TmsTableCostRange->id = $this->request->data[$x]['TmsTableCostRange']['id'];
                    $this->TmsTableCostRange->delete($this->request->data[$x]['TmsTableCostRange']['id']);
                    unset($this->request->data[$x]);
                }
            }
            if ($this->TmsTableCostRange->saveMany($this->request->data)){
                $this->Session->setFlash('Definições de Tabela Salva!', 'mensagens/sucesso');
                return $this->redirect("/embarcador/tabelas/index/{$embarcadora_id}");
            } else {
                $this->Session->setFlash('ERRO: Verifique o Formulário!', 'mensagens/alerta');
            }
        } else {
            $this->request->data = $this->TmsTableCostRange->find('all', ['conditions' => [
                'TmsTableCostRange.embarcador_tms_cost_id' => $embarcador_tms_cost_id,
            ]]);
        }
    }
    
    public function routes($embarcadora_id, $embarcador_tms_cost_id){
        if ($this->request->is('post') || $this->request->is('put')){
            foreach ($this->request->data as $x => $v){
                $this->request->data[$x]['TmsTableCostRoute']['embarcador_tms_cost_id'] = $embarcador_tms_cost_id;
                if ($this->request->data[$x]['TmsTableCostRoute']['excluir']==1 && !empty($this->request->data[$x]['TmsTableCostRoute']['id'])){
                    $this->TmsTableCostRoute->id = $this->request->data[$x]['TmsTableCostRoute']['id'];
                    $this->TmsTableCostRoute->delete($this->request->data[$x]['TmsTableCostRoute']['id']);
                    unset($this->request->data[$x]);
                }
            }
            if ($this->TmsTableCostRoute->saveMany($this->request->data)){
                $this->Session->setFlash('Definições de Tabela Salva!', 'mensagens/sucesso');
                return $this->redirect("/embarcador/tabelas/index/{$embarcadora_id}");
            } else {
                $this->Session->setFlash('ERRO: Verifique o Formulário!', 'mensagens/alerta');
            }
        } else {
            $this->request->data = $this->TmsTableCostRoute->find('all', ['conditions' => [
                'TmsTableCostRoute.embarcador_tms_cost_id' => $embarcador_tms_cost_id,
            ]]);
        }
        $states = $this->State->find('list');
        $stateZones = [];
        $stateCities = [];
        $zoneCities = [];
        $data = [
            'states' => $this->State->find('all'),
        ];
        foreach ($data['states'] as $sx => $sv){
            $data['states'][$sx]['cities'] = $this->City->find('all', ['conditions' => [
                'City.state_id' => $sv['State']['id'],
            ]]);
            $stateZones[$sv['State']['id']] = $this->Zone->find('list', ['conditions' => [
                'Zone.state_id' => $sv['State']['id'],
            ]]);
            $stateCities[$sv['State']['id']] = $this->City->find('list', ['conditions' => [
                'City.state_id' => $sv['State']['id'],
            ]]);
            $data['states'][$sx]['zones'] = $this->Zone->find('all', ['conditions' => [
                'Zone.state_id' => $sv['State']['id'],
            ]]);
            foreach ($data['states'][$sx]['zones'] as $zx => $zv){
                $data['states'][$sx]['zones'][$zx]['cities'] = $this->City->find('all', ['conditions' => [
                    'City.state_id' => $sv['State']['id'],
                    'City.zone_id' => $zv['Zone']['id'],
                ]]);
                $zoneCities[$sv['State']['id']][$zv['Zone']['id']] = $this->City->find('list', ['conditions' => [
                    'City.state_id' => $sv['State']['id'],
                    'City.zone_id' => $zv['Zone']['id'],
                ]]);
            }
        }
        $this->set(compact('data', 'states', 'stateZones', 'stateCities', 'zoneCities'));
    }
    
    public function values($embarcadora_id, $embarcador_tms_cost_id){
        $routes = $this->TmsTableCostRoute->find('all', ['conditions' => [
            'TmsTableCostRoute.embarcador_tms_cost_id' => $embarcador_tms_cost_id,
        ], ['order' => [
            'State.uf' => 'ASC',
            'Zone.name' => 'ASC',
            'City.name' => 'ASC',
            'TmsTableCostRoute.cep_start' => 'ASC',
        ]]]);
        $this->set(compact('embarcadora_id', 'embarcador_tms_cost_id', 'routes'));
    }
    
    public function values_ranges($embarcadora_id, $embarcador_tms_cost_id, $embarcador_tms_cost_route_id){
        $faixas = $this->TmsTableCostRange->find('all', ['conditions' => [
            'TmsTableCostRange.embarcador_tms_cost_id' => $embarcador_tms_cost_id,
        ]]);
        $route = $this->TmsTableCostRoute->read(null, $embarcador_tms_cost_route_id);
        if ($this->request->is('post') || $this->request->is('put')){
            foreach ($this->request->data as $x => $v){
                $this->request->data[$x]['TmsTableCostValue']['embarcador_tms_cost_id'] = $embarcador_tms_cost_id;
                $this->request->data[$x]['TmsTableCostValue']['embarcador_tms_cost_route_id'] = $embarcador_tms_cost_route_id;
            }
            if ($this->TmsTableCostValue->saveMany($this->request->data)){
                $this->Session->setFlash('Definições de Tabela Salva!', 'mensagens/sucesso');
                return $this->redirect("/embarcador/tabelas/index/{$embarcadora_id}");
            } else {
                $this->Session->setFlash('ERRO: Verifique o Formulário!', 'mensagens/alerta');
            }
        } else {
            foreach ($faixas as $x => $f) {
                $v = $this->TmsTableCostValue->find('first', ['conditions' => [
                    'TmsTableCostValue.embarcador_tms_cost_range_id' => $f['TmsTableCostRange']['id'],
                    'TmsTableCostValue.embarcador_tms_cost_route_id' => $embarcador_tms_cost_route_id,
                ]]);
                if (empty($v['TmsTableCostValue']['id'])){
                    $v['TmsTableCostValue']['value'] = '0,00';
                    $v['TmsTableCostValue']['percent'] = '0,0000';
                }
                $this->request->data[$x] = $v;
            }
        }
        $this->set(compact('embarcadora_id', 'embarcador_tms_cost_id', 'embarcador_tms_cost_route_id', 'faixas', 'route'));
    }
    
    public function importar($embarcador_tms_cost_id){
        Configure::write('debug', 2);
        $this->TmsTableCost->ImportarPanilhaFaixaCep($embarcador_tms_cost_id, WWW_ROOT . 'files' . DS . 'aaa.csv');
    }
    
}