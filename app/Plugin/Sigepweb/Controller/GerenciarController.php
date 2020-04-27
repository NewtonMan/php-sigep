<?php
class GerenciarController extends SigepwebAppController {
    
    public $uses = ['Cadastros.Destino', 'Sigepweb.SigepConta', 'Sigepweb.ContaServico', 'Sigepweb.ContaEstrategia', 'Sigepweb.Estrategia', 'Sigepweb.Servico', 'Sigepweb.Encomenda', 'Sigepweb.Orcamento', 'IbgeEstado', 'IbgeCidade'];
    
    public function contas(){
        $this->setRefer();
        $criterios = $this->crudModelSearch('SigepConta');
        $cliente_id = AuthComponent::User('acesso_cliente_id');
        if (!empty($cliente_id)){
            $criterios = [
                'SigepConta.cliente_id' => $cliente_id,
                $criterios,
            ];
        } elseif (isset($_GET['cliente_id'])) {
            $criterios = [
                'SigepConta.cliente_id' => $_GET['cliente_id'],
                $criterios,
            ];
        }
        $this->request->data['CRUD']['titulo'] = 'Gerenciamento de Contas Sigepweb';
        $this->request->data['CRUD']['cols'] = [
            'id' => [
                'label' => 'ID',
            ],
            'cliente_id' => [
                'label' => 'Cliente',
                'model' => 'Cliente',
                'field' => 'fantasia',
            ],
            'agencia_id' => [
                'label' => 'Ag�ncia',
                'model' => 'Agencia',
                'field' => 'fantasia',
            ],
            'cartao_postagem' => [
                'label' => 'Cart�o Postagem',
            ],
            'numero_contrato' => [
                'label' => 'N�mero do Contrato',
            ],
            'cnpj' => [
                'label' => 'CNPJ',
            ],
            'usuario' => [
                'label' => 'Usu�rio',
            ],
            'created' => [
                'label' => 'Criado em',
            ],
            'modified' => [
                'label' => 'Alterado em',
            ],
        ];
        $this->request->data['CRUD']['data'] = $this->paginate('SigepConta', $criterios);
        $this->request->data['CRUD']['model'] = 'SigepConta';
        $this->request->data['CRUD']['actions'] = [
            'custom' => [
                [
                    'href' => '/sigepweb/testes/index/',
                    'target' => '_blank',
                    'btn' => 'default',
                    'icon' => 'fa fa-archive',
                    'label' => 'Debugar Acesso',
                    'target' => '_self',
                ],
                [
                    'href' => '/sigepweb/encomenda/index?sigepweb_conta_id=',
                    'btn' => 'default',
                    'icon' => 'fa fa-archive',
                    'label' => 'Encomendas',
                    'target' => '_self',
                ],
                [
                    'href' => '/sigepweb/gerenciar/conta_estrategia/',
                    'btn' => 'default',
                    'icon' => 'fa fa-globe',
                    'label' => 'Estrat�gia',
                    'target' => '_self',
                ],
                [
                    'href' => '/sigepweb/gerenciar/conta_servico/',
                    'btn' => 'default',
                    'icon' => 'fa fa-flag',
                    'label' => 'Servi�os',
                    'target' => '_self',
                ],
                [
                    'href' => '/sigepweb/gerenciar/relatorio/',
                    'btn' => 'default',
                    'icon' => 'fa fa-file',
                    'label' => 'Relat�rio',
                    'target' => '_self',
                ],
            ],
            'create' => '/sigepweb/gerenciar/contas_form',
            'update' => '/sigepweb/gerenciar/contas_form/',
            'delete' => '/sigepweb/gerenciar/contas_delete/',
        ];
    }
    
    public function contas_delete($id){
        $this->SigepConta->id = $id;
        $this->SigepConta->delete($id);
        $this->getRefer();
    }
    
    public function contas_form($id=null){
        $this->crudFormAction('SigepConta', $id);
        $clientes = $this->Destino->find('list', ['conditions' => [
            'Destino.cliente' => 1,
        ]]);
        $agencias = $this->Destino->find('list', ['conditions' => [
            'Destino.correios' => 1,
        ]]);
        $servicos = $this->Servico->find('list');
        $this->request->data['CRUD']['titulo'] = 'Gerenciamento de Contas Sigepweb';
        $this->request->data['CRUD']['form'] = [
            'cliente_id' => [
                'options' => [
                    'label' => 'Cliente',
                    'options' => $clientes,
                    'empty' => false,
                ]
            ],
            'agencia_id' => [
                'options' => [
                    'label' => 'Ag�ncia do Correios',
                    'options' => $agencias,
                    'empty' => false,
                ],
            ],
            'codigo_administrativo' => [
                'options' => [
                    'label' => 'C�digo Administrativo',
                ]
            ],
            'numero_contrato' => [
                'options' => [
                    'label' => 'N�mero do Contrato',
                ]
            ],
            'cartao_postagem' => [
                'options' => [
                    'label' => 'Cart�o Postagem',
                ]
            ],
            'cnpj' => [
                'options' => [
                    'label' => 'CNPJ (somente n�meros)',
                ]
            ],
            'usuario' => [
                'options' => [
                    'label' => 'Usu�rio',
                ]
            ],
            'senha' => [
                'options' => [
                    'label' => 'Senha',
                    'type' => 'password',
                ]
            ],
            'padrao_sigepweb_servico_id' => [
                'options' => [
                    'label' => 'Servi�o Padr�o',
                    'options' => $servicos,
                    'empty' => false,
                ]
            ],
        ];
        $this->request->data['CRUD']['model'] = 'SigepConta';
    }
    
    public function conta_servico($sigepweb_conta_id){
        $this->setRefer();
        $criterios = $this->crudModelSearch('ContaServico');
        $criterios = [
            'ContaServico.sigepweb_conta_id' => $sigepweb_conta_id,
            $criterios,
        ];
        $this->request->data['CRUD']['titulo'] = 'Gerenciamento de Servi�os na Conta';
        $this->request->data['CRUD']['cols'] = [
            'id' => [
                'label' => 'ID',
            ],
            'sigepweb_servico_id' => [
                'label' => 'Servi�o',
                'model' => 'Servico',
                'field' => 'name',
            ],
            'ibge_estado_id' => [
                'label' => 'Estado',
                'model' => 'Estado',
                'field' => 'sigla',
            ],
        ];
        $this->request->data['CRUD']['data'] = $this->paginate('ContaServico', $criterios);
        $this->request->data['CRUD']['model'] = 'ContaServico';
        $this->request->data['CRUD']['actions'] = [
            'create' => '/sigepweb/gerenciar/conta_servico_form/'.$sigepweb_conta_id,
            'update' => '/sigepweb/gerenciar/conta_servico_form/'.$sigepweb_conta_id.'/',
            'delete' => '/sigepweb/gerenciar/contas_servico_delete/'.$sigepweb_conta_id.'/',
        ];
    }
    
    public function contas_servico_delete($sigepweb_conta_id, $id){
        $this->ContaServico->id = $id;
        $this->ContaServico->delete($id);
        $this->getRefer();
    }
    
    public function conta_servico_form($sigepweb_conta_id, $id=null){
        $this->request->data['ContaServico']['sigepweb_conta_id'] = $sigepweb_conta_id;
        $this->crudFormAction('ContaServico', $id);
        $servicos = $this->Servico->find('list');
        $estados = $this->IbgeEstado->find('list');
        $this->request->data['CRUD']['titulo'] = 'Gerenciamento de Servi�os em Contas Sigepweb';
        $this->request->data['CRUD']['form'] = [
            'ibge_estado_id' => [
                'options' => [
                    'label' => 'Estado',
                    'options' => $estados,
                    'empty' => ' - Aplica a todos os Estados - ',
                ]
            ],
            'sigepweb_servico_id' => [
                'options' => [
                    'label' => 'Servi�o',
                    'options' => $servicos,
                    'empty' => false,
                ]
            ],
        ];
        $this->request->data['CRUD']['model'] = 'ContaServico';
    }
    
    public function conta_estrategia($sigepweb_conta_id){
        $this->setRefer();
        $criterios = $this->crudModelSearch('ContaEstrategia');
        $criterios = [
            'ContaEstrategia.sigepweb_conta_id' => $sigepweb_conta_id,
            $criterios,
        ];
        $this->request->data['CRUD']['titulo'] = 'Gerenciamento de Estrat�gias de Servi�os na Conta';
        $this->request->data['CRUD']['cols'] = [
            'id' => [
                'label' => 'ID',
            ],
            'sigepweb_estrategia_id' => [
                'label' => 'Estrat�gia',
                'model' => 'Estrategia',
                'field' => 'nome',
            ],
            'ibge_estado_id' => [
                'label' => 'Estado',
                'model' => 'Estado',
                'field' => 'sigla',
            ],
            'prazo_maximo_capital' => [
                'label' => 'Prazo Max Capital',
            ],
            'prazo_maximo_interior' => [
                'label' => 'Prazo Max Interior',
            ],
        ];
        $this->request->data['CRUD']['data'] = $this->paginate('ContaEstrategia', $criterios);
        $this->request->data['CRUD']['model'] = 'ContaEstrategia';
        $this->request->data['CRUD']['actions'] = [
            'create' => '/sigepweb/gerenciar/conta_estrategia_form/'.$sigepweb_conta_id,
            'update' => '/sigepweb/gerenciar/conta_estrategia_form/'.$sigepweb_conta_id.'/',
            'delete' => '/sigepweb/gerenciar/conta_estrategia_delete/'.$sigepweb_conta_id.'/',
        ];
    }
    
    public function conta_estrategia_form($sigepweb_conta_id, $id=null) {
        Configure::write('debug', 2);
        $this->request->data['ContaEstrategia']['sigepweb_conta_id'] = $sigepweb_conta_id;
        $this->crudFormAction('ContaEstrategia', $id);
        $estrategias = $this->Estrategia->find('list');
        $estados = $this->IbgeEstado->find('list');
        $this->request->data['CRUD']['titulo'] = 'Gerenciamento de Estrat�gias para Servi�os em Contas Sigepweb';
        $this->request->data['CRUD']['form'] = [
            'ibge_estado_id' => [
                'options' => [
                    'label' => 'Estado',
                    'options' => $estados,
                    'empty' => ' - Aplica a todos os Estados - ',
                ]
            ],
            'sigepweb_estrategia_id' => [
                'options' => [
                    'label' => 'Servi�o',
                    'options' => $estrategias,
                    'empty' => false,
                ]
            ],
            'prazo_maximo_capital' => [
                'label' => 'Prazo M�ximo para Capital',
                'class' => 'mask_int',
            ],
            'prazo_maximo_interior' => [
                'label' => 'Prazo M�ximo para Interior',
                'class' => 'mask_int',
            ],
        ];
        $this->request->data['CRUD']['model'] = 'ContaEstrategia';
    }
    public function relatorio($id) {
        //die(print_r($this->Encomenda->find('first')));
        if ($this->request->is('post') || $this->request->is('put')) {
            $data1 = $this->request->data['data1'];
            $data2 = $this->request->data['data2'];
            $lista = $this->Encomenda->find('all', ['conditions' => [
                'Conta.id' => $id,
                'Encomenda.nfe_data BETWEEN ? AND ?' => [$data1, $data2],
            ]]);
            $this->set('lista', $lista);

            //print_r($lista);
            $this->render('relatorio_csv', 'exportar_excel');
        }
    }
}