<?php
class GerenciarController extends TotalExpressAppController {
    
    public $uses = ['Cadastros.Destino', 'TotalExpress.TotalConta', 'TotalExpress.ContaServico', 'TotalExpress.ContaEstrategia', 'TotalExpress.Estrategia', 'TotalExpress.Servico', 'TotalExpress.Encomenda', 'TotalExpress.Orcamento', 'IbgeEstado', 'IbgeCidade'];
    
    public function contas(){
        $this->setRefer();
        $criterios = $this->crudModelSearch('TotalConta');
        $cliente_id = AuthComponent::User('acesso_cliente_id');
        if (!empty($cliente_id)){
            $criterios = [
                'TotalConta.cliente_id' => $cliente_id,
                $criterios,
            ];
        } elseif (isset($_GET['cliente_id'])) {
            $criterios = [
                'TotalConta.cliente_id' => $_GET['cliente_id'],
                $criterios,
            ];
        }
        $this->request->data['CRUD']['titulo'] = 'Gerenciamento de Contas TotalExpress';
        $this->request->data['CRUD']['cols'] = [
            'cliente_id' => [
                'label' => 'Cliente',
                'model' => 'Cliente',
                'field' => 'fantasia',
            ],
            'agencia_id' => [
                'label' => 'Agência',
                'model' => 'Agencia',
                'field' => 'fantasia',
            ],
            'created' => [
                'label' => 'Criado em',
            ],
            'modified' => [
                'label' => 'Alterado em',
            ],
        ];
        $this->request->data['CRUD']['data'] = $this->paginate('TotalConta', $criterios);
        $this->request->data['CRUD']['model'] = 'TotalConta';
        $this->request->data['CRUD']['actions'] = [
            'custom' => [
                [
                    'href' => '/total_express/encomenda/index?total_express_conta_id=',
                    'btn' => 'default',
                    'icon' => 'fa fa-archive',
                    'label' => 'Encomendas',
                    'target' => '_self',
                ],
            ],
            'create' => '/total_express/gerenciar/contas_form',
            'update' => '/total_express/gerenciar/contas_form/',
            'delete' => '/total_express/gerenciar/contas_delete/',
        ];
    }
    
    public function contas_form($id=null){
        $this->crudFormAction('TotalConta', $id);
        $clientes = $this->Destino->find('list', ['conditions' => [
            'Destino.cliente' => 1,
        ]]);
        $agencias = $this->Destino->find('list', ['conditions' => [
            'Destino.transportador' => 1,
        ]]);
        $servicos = $this->Servico->find('list');
        $this->request->data['CRUD']['titulo'] = 'Gerenciamento de Contas TotalExpress';
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
                    'label' => 'Agência do Correios',
                    'options' => $agencias,
                    'empty' => false,
                ],
            ],
            'endpoint' => [
                'options' => [
                    'label' => 'EndPoint API',
                ]
            ],
            'usuario' => [
                'options' => [
                    'label' => 'Usuário',
                ]
            ],
            'senha' => [
                'options' => [
                    'label' => 'Senha',
                    'type' => 'password',
                ]
            ],
            'padrao_total_express_servico_id' => [
                'options' => [
                    'label' => 'Serviço Padrão',
                    'options' => $servicos,
                    'empty' => false,
                ]
            ],
            'natureza' => [
                'options' => [
                    'label' => 'Produto Predominante',
                ]
            ],
            'cfop' => [
                'options' => [
                    'label' => 'CFOP PRedominante',
                ]
            ],
            'eid' => [
                'options' => [
                    'label' => 'EID para Rastreio',
                ]
            ],
        ];
        $this->request->data['CRUD']['model'] = 'TotalConta';
    }
    
}