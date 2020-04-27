<?php
class TotalConta extends TotalExpressAppModel {
    
    public $useTable = 'total_express_contas';
    
    public $displayField = 'displayField';
    
    public $belongsTo = [
        'Cliente' => [
            'className' => 'Cadastros.Destino',
            'foreignKey' => 'cliente_id',
        ],
        'Agencia' => [
            'className' => 'Cadastros.Destino',
            'foreignKey' => 'agencia_id',
        ],
    ];
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['displayField'] = "CONCAT({$this->alias}.id, ' - ', {$this->alias}.usuario)";
    }
    
    public function TotalExpressCotacao($conta_id, $cepDestino, $peso, $ValorDeclarado=0.01){
        $cotacao = [];
        if ($ValorDeclarado==0) {
            $ValorDeclarado = 1.00;
        }
        $conta = $this->read(null, $conta_id);
        try {
            $soapClient = new \SoapClient('https://edi.totalexpress.com.br/webservice_calculo_frete.php?wsdl', [ 'login' => $conta['TotalConta']['usuario'], 'password' => $conta['TotalConta']['senha'], 'exceptions' => true, 'trace' => true ]);
            $requestData = [
                'TipoServico' => 'EXP',
                'CepDestino' => $cepDestino,
                'Peso' => $peso,
                'ValorDeclarado' => $ValorDeclarado,
                'TipoEntrega' => 1,
                'ServicoCOD' => false,
                'Altura' => 0.000,
                'Largura' => 0.000,
                'Profundidade' => 0.000,
            ];
            $response = $soapClient->calcularFrete($requestData);
            $cotacao[] = $response;
        } catch (SoapFault $exception) {
        }
        return $cotacao;
    }
    
    public function TotalExpressColetaRequest($conta_id) {
        $conta = $this->read(null, $conta_id);
        
        $this->TotalEncomenda = ClassRegistry::init('TotalExpress.TotalEncomenda');
        $page = 1;
        
        paginar:
        $encomendas = $this->TotalEncomenda->find('all', ['conditions' => [
            'TotalEncomenda.total_express_conta_id' => $conta_id,
            'TotalEncomenda.postagem_id IS NULL',
        ], 'limit' => 100, 'page' => $page]);
        $total = count($encomendas);
        if ($total > 0){
            $ids = [0];
            try {
                $soapClient = new \SoapClient($conta['TotalConta']['endpoint'], [ 'login' => $conta['TotalConta']['usuario'], 'password' => $conta['TotalConta']['senha'], 'exceptions' => true, 'trace' => true ]);
                $requestData = [
                    'CodRemessa' => '',
                    'Encomendas' => [],
                ];
                foreach ($encomendas as $e){
                    $ids[] = $e['TotalEncomenda']['id'];
                    $requestData['Encomendas'][] = [
                        //'Encomenda' => [
                            'TipoServico' => $e['Servico']['id'],
                            'TipoEntrega' => 0,
                            'Peso' => ($e['TotalEncomenda']['nfe_peso_gr'] / 1000),
                            'Volumes' => 1,
                            'CondFrete' => 'CIF',
                            'Pedido' => $e['TotalEncomenda']['id'],
                            'IdCliente' => $e['TotalEncomenda']['id'],
                            'Natureza' => $conta['TotalConta']['natureza'],
                            'IsencaoIcms' => 0,
                            // DEST
                            'DestNome' => $e['Destino']['fantasia'],
                            'DestCpfCnpj' => $e['DestinoLocal']['cpf_cnpj'],
                            'DestIe' => $e['Destino']['rg_insc_estadual'],
                            'DestEnd' => $e['DestinoLocal']['endereco'],
                            'DestEndNum' => $e['DestinoLocal']['numero'],
                            'DestCompl' => $e['DestinoLocal']['complemento'],
                            'DestPontoRef' => '',
                            'DestBairro' => substr($e['DestinoLocal']['bairro'], 0, 40),
                            'DestCidade' => $e['DestinoLocal']['municipio'],
                            'DestEstado' => $e['DestinoLocal']['uf'],
                            'DestPais' => '',
                            'DestCep' => $e['DestinoLocal']['cep'],
                            'DestEmail' => $e['Destino']['email'],
                            'DestTelefone1' => $e['Destino']['telefones'],
                            'DocFiscalNFe' => [
                                [
                                    //'NFe' => [
                                        'NfeSerie' => $e['TotalEncomenda']['nfe_serie'],
                                        'NfeNumero' => $e['TotalEncomenda']['nfe_numero'],
                                        'NfeData' => $e['TotalEncomenda']['nfe_data'],
                                        'NfeValTotal' => $e['TotalEncomenda']['nfe_valor'],
                                        'NfeValProd' => $e['TotalEncomenda']['nfe_valor'],
                                        'NfeChave' => $e['TotalEncomenda']['nfe_chave'],
                                        'NfeCfop' => $conta['TotalConta']['cfop'],
                                    //],
                                ],
                            ],
                        //],
                    ];
                }
                $response = $soapClient->RegistraColeta($requestData);
                if ($response->ItensProcessados>0) {
                    $this->TotalEncomenda->updateAll([
                        'TotalEncomenda.postagem_id' => $response->NumProtocolo,
                    ], [
                        'TotalEncomenda.id' => $ids,
                    ]);
                }
                if ($response->ItensRejeitados>0) {
                    $idsErros = [];
                    foreach ($response->ErrosIndividuais as $e) {
                        if ($e->CodigoErro==3) continue;
                        $idsErros[] = $e->Pedido;
                    }
                    $this->TotalEncomenda->updateAll([
                        'TotalEncomenda.postagem_id' => null,
                    ], [
                        'TotalEncomenda.id' => $idsErros,
                    ]);
                }
            } catch (SoapFault $exception) {
                print_r($exception);
            }

            if ($total==100){
                $page++;
                goto paginar;
            }
        }
    }
    
    public function TotalExpressTrackingRequest($conta_id) {
        $conta = $this->read(null, $conta_id);
        
        $this->TotalEncomenda = ClassRegistry::init('TotalExpress.TotalEncomenda');
        $this->TotalStatus = ClassRegistry::init('TotalExpress.TotalStatus');
        
        try {
            $soapClient = new \SoapClient($conta['TotalConta']['endpoint'], [ 'login' => $conta['TotalConta']['usuario'], 'password' => $conta['TotalConta']['senha'], 'exceptions' => true, 'trace' => true ]);
            $requestData = [
                'DataConsulta' => '',
            ];
            $response = $soapClient->ObterTracking($requestData);
        } catch (SoapFault $exception) {
            print_r($exception);
        }

        if ($response->CodigoProc==1) {
            foreach ($response->ArrayLoteRetorno as $x) {
                foreach ($x->ArrayEncomendaRetorno as $y) {
                    $te = $this->TotalEncomenda->find('first', ['conditions' => [
                        'TotalEncomenda.total_express_conta_id' => $conta_id,
                        'TotalEncomenda.nfe_serie' => $y->SerieNotaFiscal,
                        'TotalEncomenda.nfe_numero' => $y->NotaFiscal,
                    ]]);
                    if (!empty($te['TotalEncomenda']['id'])) {
                        $this->TotalEncomenda->id = $te['TotalEncomenda']['id'];
                        if (empty($te['TotalEncomenda']['tracking_number']) || $te['TotalEncomenda']['tracking_number']!=$y->Pedido){
                            $this->TotalEncomenda->saveField('tracking_number', $y->Pedido);
                        }
                        if ($te['TotalStatus']['codigo']!=$y->ArrayStatusTotal[0]->CodStatus){
                            $ts = $this->TotalStatus->find('first', ['conditions' => [
                                'TotalStatus.codigo' => $y->ArrayStatusTotal[0]->CodStatus
                            ]]);
                            $this->TotalEncomenda->saveField('total_express_status_id', $ts['TotalStatus']['id']);
                            $this->TotalEncomenda->saveField('status_dh', $y->ArrayStatusTotal[0]->DataStatus);
                        }
                    }
                }
            }
        } else {
            print_r($response);
        }
    }
    
}


/*


 */