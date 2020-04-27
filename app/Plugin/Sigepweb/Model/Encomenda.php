<?php
require_once ROOT . DS . 'lib' . DS . 'fpdf' . DS . 'fpdf.php';
require_once ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';
require_once ROOT . DS . APP_DIR . DS . 'Plugin' . DS . 'Sigepweb' . DS . 'vendor' . DS . 'vendor' . DS . 'autoload.php';

use PhpSigep\Model\Diretoria;
use NFePHP\NFe\Common\Standardize;

class Encomenda extends SigepwebAppModel {
    
    public $useTable = 'sigepweb_encomendas';
    
    public $order = [
        'Encomenda.id' => 'DESC',
    ];
    
    public $virtualFields = [
        'sigepweb_postagem_id' => 'SELECT PE.sigepweb_postagem_id FROM sigepweb_postagens_encomendas PE WHERE PE.sigepweb_encomenda_id=Encomenda.id',
        'sigepweb_postagem_data' => 'SELECT P.created FROM sigepweb_postagens_encomendas PE JOIN sigepweb_postagens P ON P.id=PE.sigepweb_postagem_id WHERE PE.sigepweb_encomenda_id=Encomenda.id',
    ];
    
    public $hasMany = [
        'Orcamento' => [
            'className' => 'Sigepweb.Orcamento',
            'foreignKey' => 'sigepweb_encomenda_id',
        ],
    ];
    
    public $hasOne = [
        'Etiqueta' => [
            'className' => 'Sigepweb.Etiqueta',
            'foreignKey' => 'sigepweb_encomenda_id',
        ],
    ];
    
    public $belongsTo = [
        'Conta' => [
            'className' => 'Sigepweb.Conta',
            'foreignKey' => 'sigepweb_conta_id',
        ],
        'SigepConta' => [
            'className' => 'Sigepweb.SigepConta',
            'foreignKey' => 'sigepweb_conta_id',
        ],
        'Servico' => [
            'className' => 'Sigepweb.Servico',
            'foreignKey' => 'sigepweb_servico_id',
        ],
        'Emitente' => [
            'className' => 'Cadastros.Destino',
            'foreignKey' => 'nfe_emitente_id',
        ],
        'Destino' => [
            'className' => 'Cadastros.Destino',
            'foreignKey' => 'nfe_destino_id',
        ],
        'DestinoLocal' => [
            'className' => 'Sigepweb.Local',
            'foreignKey' => 'nfe_destino_local_id',
        ],
        'Transportadora' => [
            'className' => 'Cadastros.Destino',
            'foreignKey' => 'nfe_transportadora_id',
        ],
    ];
    
    public $validate = [
        'nfe_chave' => [
            'rule1' => [
                'rule' => ['isUnique'],
                'required' => true,
                'message' => 'NF-e já cadastrada!',
            ],
        ],
    ];
    
    public function getObjeto($encomenda_id){
        $encomenda = $this->read(null, $encomenda_id);
        $this->sigepweb_start($encomenda);

        $dimensao = new \PhpSigep\Model\Dimensao();
        $dimensao->setAltura(4);
        $dimensao->setLargura(11);
        $dimensao->setComprimento(16);
        $dimensao->setTipo(\PhpSigep\Model\Dimensao::TIPO_PACOTE_CAIXA);

        $destinatario = new \PhpSigep\Model\Destinatario();
        $destinatario->setNome($encomenda['Destino']['nome_razao']);
        $destinatario->setLogradouro($encomenda['DestinoLocal']['endereco']);
        $destinatario->setNumero($encomenda['DestinoLocal']['numero']);
        $destinatario->setComplemento($encomenda['DestinoLocal']['complemento']);

        $destino = new \PhpSigep\Model\DestinoNacional();
        $destino->setBairro($encomenda['DestinoLocal']['bairro']);
        $destino->setCep($encomenda['DestinoLocal']['cep']);
        $destino->setCidade($encomenda['DestinoLocal']['municipio']);
        $destino->setUf($encomenda['DestinoLocal']['uf']);
        $destino->setNumeroNotaFiscal(addLeading($encomenda['Encomenda']['nfe_numero'], 15));
        //$destino->setNumeroPedido(addLeading($encomenda['Encomenda']['nfe_numero'], 15));

        $etiqueta = new \PhpSigep\Model\Etiqueta();
        $etiqueta->setEtiquetaComDv($encomenda['Etiqueta']['codigo_com_dv']);

        /*
        $servicoAdicional = new \PhpSigep\Model\ServicoAdicional();
        //$servicoAdicional->setCodigoServicoAdicional(\PhpSigep\Model\ServicoAdicional::SERVICE_REGISTRO);
        //$servicoAdicional->setCodigoServicoAdicional(\PhpSigep\Model\ServicoAdicional::SERVICE_AVISO_DE_RECEBIMENTO);

        $servicoAdicional2 = new \PhpSigep\Model\ServicoAdicional();
        $servicoAdicional2->setCodigoServicoAdicional(\PhpSigep\Model\ServicoAdicional::SERVICE_REGISTRO);
        $servicoAdicional2->setCodigoServicoAdicional(\PhpSigep\Model\ServicoAdicional::SERVICE_VALOR_DECLARADO_PAC);
        $servicoAdicional2->setValorDeclarado(100);
        */

        $objeto = new \PhpSigep\Model\ObjetoPostal();
        //$objeto->setServicosAdicionais([]);
        $objeto->setDestinatario($destinatario);
        $objeto->setDestino($destino);
        $objeto->setDimensao($dimensao);
        $objeto->setEtiqueta($etiqueta);
        $objeto->setPeso((($encomenda['Encomenda']['nfe_peso_gr']<100 ? 100:$encomenda['Encomenda']['nfe_peso_gr']) / 1000));
        //$objeto->setObservacao($encomenda['Encomenda']['nfe_observacao']);
        $objeto->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem($encomenda['Servico']['codigo']));
        
        return $objeto;
    }
    
    public function getEtiqueta($encomenda_id){
        $encomenda = $this->read(null, $encomenda_id);
        $encomenda = to_utf8($encomenda);
        $pasta = WWW_ROOT . "sigepweb/{$encomenda['SigepConta']['id']}";
        if (!is_dir($pasta)){
            mkdir($pasta, 0777, true);
        }
        $arqPDF = $pasta . "/{$encomenda['Etiqueta']['codigo_com_dv']}.PDF";
        if (!file_exists($arqPDF)) {
            $objeto = $this->getObjeto($encomenda_id);
            $remetente = new \PhpSigep\Model\Remetente();
            $remetente->setNome($encomenda['Emitente']['fantasia']);
            $remetente->setLogradouro($encomenda['Emitente']['endereco']);
            $remetente->setNumero($encomenda['Emitente']['numero']);
            $remetente->setComplemento($encomenda['Emitente']['complemento']);
            $remetente->setBairro($encomenda['Emitente']['bairro']);
            $remetente->setCep($encomenda['Emitente']['cep']);
            $remetente->setUf($encomenda['Emitente']['uf']);
            $remetente->setCidade($encomenda['Emitente']['municipio']);
            
            $plp = new \PhpSigep\Model\PreListaDePostagem();
            $plp->setAccessData($this->sigepweb_access_data($encomenda));
            $plp->setRemetente($remetente);
            $plp->setEncomendas([$objeto]);
            
            $logoFile = WWW_ROOT . '/img/' . $encomenda['Emitente']['id'] . '.jpg';
            if (!file_exists($logoFile)) $logoFile = null;
            
            $pdf = new \PhpSigep\Pdf\CartaoDePostagem2018($plp, time(), $logoFile, array());
            $pdf->render('F', $arqPDF);
        }
        return $arqPDF;
    }
    
    public function importNFe($arquivo, $sigepweb_conta_id, $sigepweb_servico_id=null, $codigo_com_dv=null) {
        if (strlen($codigo_com_dv)!=13){
            $codigo_com_dv = null;
        }
        try {
            $std = new NFePHP\NFe\Common\Standardize();
            $nfe = $std->toArray(file_get_contents($arquivo));
            switch ($nfe['attributes']['versao']) {
                case '4.00':
                case '3.10':
                    return $this->schemaFromNFe400($nfe, $sigepweb_conta_id, $sigepweb_servico_id, $codigo_com_dv);
                    break;

                default:
                    return false;
                    break;
            }
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
    
    private function schemaFromNFe400($nfe, $sigepweb_conta_id, $sigepweb_servico_id=null, $codigo_com_dv=null){
        $nfe_chave = substr($nfe['NFe']['infNFe']['attributes']['Id'], 3, 44);
        $e = $this->find('count', ['conditions' => [
            "{$this->alias}.nfe_chave" => $nfe_chave,
        ]]);
        if ($e==0){
            $Destino = ClassRegistry::init('Cadastros.Destino');
            $DestinoLocal = ClassRegistry::init('Sigepweb.Local');
            $emitente = $Destino->find('first', ['conditions' => [
                'Destino.cpf_cnpj' => (int)$nfe['NFe']['infNFe']['emit']['CNPJ'],
            ]]);
            if (empty($emitente['Destino']['id'])){
                $destino_emitente = [
                    'cpf_cnpj' => (int)$nfe['NFe']['infNFe']['emit']['CNPJ'],
                    'fantasia' => (!isset($nfe['NFe']['infNFe']['emit']['xFant']) ? $nfe['NFe']['infNFe']['emit']['xNome']:$nfe['NFe']['infNFe']['emit']['xFant']),
                    'nome_razao' => $nfe['NFe']['infNFe']['emit']['xNome'],
                    'rg_insc_estadual' => @$nfe['NFe']['infNFe']['emit']['IE'],
                    'endereco' => $nfe['NFe']['infNFe']['emit']['enderEmit']['xLgr'],
                    'numero' => $nfe['NFe']['infNFe']['emit']['enderEmit']['nro'],
                    'complemento' => @$nfe['NFe']['infNFe']['emit']['enderEmit']['xCpl'],
                    'bairro' => $nfe['NFe']['infNFe']['emit']['enderEmit']['xBairro'],
                    'municipio' => $nfe['NFe']['infNFe']['emit']['enderEmit']['xMun'],
                    'uf' => $nfe['NFe']['infNFe']['emit']['enderEmit']['UF'],
                    'cep' => $nfe['NFe']['infNFe']['emit']['enderEmit']['CEP'],
                    'ibge_cidade_id' => $nfe['NFe']['infNFe']['emit']['enderEmit']['cMun'],
                    'ibge_estado_id' => substr($nfe['NFe']['infNFe']['emit']['enderEmit']['cMun'], 0, 2),
                ];
                $Destino->create();
                $Destino->save($destino_emitente);
                $emitente = $Destino->find('first', ['conditions' => [
                    'Destino.cpf_cnpj' => (int)$nfe['NFe']['infNFe']['emit']['CNPJ'],
                ]]);
            }
            $destino = $Destino->find('first', ['conditions' => [
                'Destino.cpf_cnpj' => (int)(isset($nfe['NFe']['infNFe']['dest']['CNPJ']) ? $nfe['NFe']['infNFe']['dest']['CNPJ']:$nfe['NFe']['infNFe']['dest']['CPF']),
            ]]);
            if (empty($destino['Destino']['id'])){
                $destino_destino = [
                    'cpf_cnpj' => (int)(isset($nfe['NFe']['infNFe']['dest']['CNPJ']) ? $nfe['NFe']['infNFe']['dest']['CNPJ']:$nfe['NFe']['infNFe']['dest']['CPF']),
                    'fantasia' => (isset($nfe['NFe']['infNFe']['dest']['xFant']) ? $nfe['NFe']['infNFe']['dest']['xFant']:$nfe['NFe']['infNFe']['dest']['xNome']),
                    'nome_razao' => $nfe['NFe']['infNFe']['dest']['xNome'],
                    'rg_insc_estadual' => @$nfe['NFe']['infNFe']['dest']['IE'],
                    'endereco' => $nfe['NFe']['infNFe']['dest']['enderDest']['xLgr'],
                    'numero' => $nfe['NFe']['infNFe']['dest']['enderDest']['nro'],
                    'complemento' => @$nfe['NFe']['infNFe']['dest']['enderDest']['xCpl'],
                    'bairro' => $nfe['NFe']['infNFe']['dest']['enderDest']['xBairro'],
                    'municipio' => $nfe['NFe']['infNFe']['dest']['enderDest']['xMun'],
                    'uf' => $nfe['NFe']['infNFe']['dest']['enderDest']['UF'],
                    'cep' => $nfe['NFe']['infNFe']['dest']['enderDest']['CEP'],
                    'ibge_cidade_id' => $nfe['NFe']['infNFe']['dest']['enderDest']['cMun'],
                    'ibge_estado_id' => substr($nfe['NFe']['infNFe']['dest']['enderDest']['cMun'], 0, 2),
                ];
                $Destino->create();
                $Destino->save($destino_destino);
                $destino = $Destino->find('first', ['conditions' => [
                    'Destino.cpf_cnpj' => (int)(isset($nfe['NFe']['infNFe']['dest']['CNPJ']) ? $nfe['NFe']['infNFe']['dest']['CNPJ']:$nfe['NFe']['infNFe']['dest']['CPF']),
                ]]);
            }
            $nfe_destino_local_id = null;
            if (isset($nfe['NFe']['infNFe']['entrega'])){
                $entrega = $nfe['NFe']['infNFe']['entrega'];
                $destino_local = [
                    'cpf_cnpj' => (int)(isset($entrega['CNPJ']) ? $entrega['CNPJ']:$entrega['CPF']),
                    'endereco' => $entrega['xLgr'],
                    'numero' => $entrega['nro'],
                    'complemento' => $entrega['xCpl'],
                    'bairro' => $entrega['xBairro'],
                    'ibge_cidade_id' => $entrega['cMun'],
                    'ibge_estado_id' => substr($entrega['cMun'], 0, 2),
                    'municipio' => $entrega['xMun'],
                    'uf' => $entrega['UF'],
                ];
                $DestinoLocal->create();
                $DestinoLocal->save($destino_local);
                $nfe_destino_local_id = $DestinoLocal->id;
            } else {
                $destino_local = [
                    'cpf_cnpj' => (int)(isset($nfe['NFe']['infNFe']['dest']['CNPJ']) ? $nfe['NFe']['infNFe']['dest']['CNPJ']:$nfe['NFe']['infNFe']['dest']['CPF']),
                    'endereco' => $nfe['NFe']['infNFe']['dest']['enderDest']['xLgr'],
                    'numero' => $nfe['NFe']['infNFe']['dest']['enderDest']['nro'],
                    'complemento' => @$nfe['NFe']['infNFe']['dest']['enderDest']['xCpl'],
                    'bairro' => $nfe['NFe']['infNFe']['dest']['enderDest']['xBairro'],
                    'municipio' => $nfe['NFe']['infNFe']['dest']['enderDest']['xMun'],
                    'uf' => $nfe['NFe']['infNFe']['dest']['enderDest']['UF'],
                    'cep' => $nfe['NFe']['infNFe']['dest']['enderDest']['CEP'],
                    'ibge_cidade_id' => $nfe['NFe']['infNFe']['dest']['enderDest']['cMun'],
                    'ibge_estado_id' => substr($nfe['NFe']['infNFe']['dest']['enderDest']['cMun'], 0, 2),
                ];
                $DestinoLocal->create();
                $DestinoLocal->save($destino_local);
                $nfe_destino_local_id = $DestinoLocal->id;
            }
            if (empty($sigepweb_servico_id)) {
                $obsPalavras = explode(' ', strtolower($nfe['NFe']['infNFe']['infAdic']['infCpl']));
                if (in_array('pac', $obsPalavras)){
                    $cs = $this->SigepConta->ContaServico->find('first', ['conditions' => [
                        'ContaServico.sigepweb_conta_id' => $e['Encomenda']['sigepweb_servico_id'],
                        "Servico.nome LIKE '%PAC%'",
                    ]]);
                    if (isset($cs['Servico']['id'])){
                        $sigepweb_servico_id = $cs['Servico']['id'];
                    }
                }
                if (in_array('sedex', $obsPalavras) && empty($sigepweb_servico_id)){
                    $cs = $this->SigepConta->ContaServico->find('first', ['conditions' => [
                        'ContaServico.sigepweb_conta_id' => $e['Encomenda']['sigepweb_servico_id'],
                        "Servico.nome LIKE '%SEDEX%'",
                    ]]);
                    if (isset($cs['Servico']['id'])){
                        $sigepweb_servico_id = $cs['Servico']['id'];
                    }
                }
            }
            $data = [
                'sigepweb_conta_id' => $sigepweb_conta_id,
                'sigepweb_servico_id' => $sigepweb_servico_id,
                'nfe_chave' => $nfe_chave,
                'nfe_serie' => $nfe['NFe']['infNFe']['ide']['serie'],
                'nfe_numero' => $nfe['NFe']['infNFe']['ide']['nNF'],
                'nfe_data' => substr($nfe['NFe']['infNFe']['ide']['dhEmi'], 0, 10),
                'nfe_emitente_id' => $emitente['Destino']['id'],
                'nfe_destino_id' => $destino['Destino']['id'],
                'nfe_destino_local_id' => $nfe_destino_local_id,
                'nfe_observacao' => $nfe['NFe']['infNFe']['infAdic']['infCpl'],
                'nfe_valor' => $nfe['NFe']['infNFe']['total']['ICMSTot']['vNF'],
                'nfe_peso_gr' => str_replace(['.',','], ['', '.'], @$nfe['NFe']['infNFe']['transp']['vol']['pesoB']),
            ];
            $this->create();
            $this->save($data);
        }
        $e = $this->find('first', ['conditions' => [
            "{$this->alias}.nfe_chave" => $nfe_chave,
        ]]);
        if (empty($e['Encomenda']['sigepweb_servico_id'])) {
            //echo "\tSEM SERVICO\n";
            $this->SigepwebOrcamentos($e['Encomenda']['id']);
            $this->SigepwebEstrategiaServico($e['Encomenda']['id']);
            $e = $this->find('first', ['conditions' => [
                "{$this->alias}.nfe_chave" => $nfe_chave,
            ]]);
        }
        if (!empty($codigo_com_dv)){
            //echo " -> TEM CODIGO COM DV";
            $cadastrada = $this->Etiqueta->find('count', ['conditions' => [
                'Etiqueta.codigo_com_dv' => $codigo_com_dv,
            ]]);
            if ($cadastrada==0){
                //echo " -> CADASTRAR CODIGO";
                $etiqueta = [
                    'Etiqueta' => [
                        'sigepweb_conta_id' => $sigepweb_conta_id,
                        'sigepweb_servico_id' => $sigepweb_servico_id,
                        'sigepweb_encomenda_id' => $e['Encomenda']['id'],
                        'codigo' => substr($codigo_com_dv, 0, 10).'BR',
                        'digito' => substr($codigo_com_dv, 11, 1),
                        'codigo_com_dv' => $codigo_com_dv,
                    ],
                ];
                $this->Etiqueta->create();
                $this->Etiqueta->save($etiqueta);
            } else {
                $etiqueta = $this->Etiqueta->find('first', ['conditions' => [
                    'Etiqueta.codigo_com_dv' => $codigo_com_dv,
                ]]);
                $this->Etiqueta->id = $etiqueta['Etiqueta']['id'];
                $this->Etiqueta->saveField('sigepweb_encomenda_id', $e['Encomenda']['id']);
                //echo " -> CODIGO ATUALIZADO";
            }
        }
        $this->SigepwebEtiquetas($e['Encomenda']['id']);
        $this->getEtiqueta($e['Encomenda']['id']);
        return $this->find('first', ['conditions' => [
            "{$this->alias}.nfe_chave" => $nfe_chave,
        ]]);
    }
    
    public function SigepwebEstrategiaServico($encomenda_id) {
        $encomenda = $this->read(null, $encomenda_id);
        $conta_estrategia = $this->SigepConta->ContaEstrategia->find('first', ['conditions' => [
            'SigepConta.id' => $encomenda['Encomenda']['sigepweb_conta_id'],
            'ContaEstrategia.ibge_estado_id' => $encomenda['DestinoLocal']['ibge_estado_id'],
        ]]);
        if (empty($conta_estrategia['ContaEstrategia']['id'])) {
            $conta_estrategia = $this->SigepConta->ContaEstrategia->find('first', ['conditions' => [
                'SigepConta.id' => $encomenda['Encomenda']['sigepweb_conta_id'],
                'ContaEstrategia.ibge_estado_id' => null,
            ]]);
        }
        if (!empty($conta_estrategia['ContaEstrategia']['id'])) {
            $cidade = $this->DestinoLocal->IbgeCidade->read(null, $encomenda['DestinoLocal']['ibge_cidade_id']);
            $prazo_max = ($cidade['IbgeCidade']['capital']==1 ? $conta_estrategia['ContaEstrategia']['prazo_maximo_capital']:$conta_estrategia['ContaEstrategia']['prazo_maximo_interior']);
            switch ($conta_estrategia['Estrategia']['id']) {
                case 1:
                    $servico = $this->Orcamento->find('first', [
                        'conditions' => [
                            'Orcamento.sigepweb_encomenda_id' => $encomenda_id,
                        ],
                        'order' => [
                            'Orcamento.prazoEntrega' => 'ASC',
                            'Orcamento.valor' => 'ASC',
                        ],
                    ]);
                    break;

                case 2:
                    $servico = $this->Orcamento->find('first', [
                        'conditions' => [
                            'Orcamento.sigepweb_encomenda_id' => $encomenda_id,
                        ],
                        'order' => [
                            'Orcamento.valor' => 'ASC',
                            'Orcamento.prazoEntrega' => 'ASC',
                        ],
                    ]);
                    break;

                case 3:
                    $servico = $this->Orcamento->find('first', [
                        'conditions' => [
                            'Orcamento.sigepweb_encomenda_id' => $encomenda_id,
                            'Orcamento.prazoEntrega <= ' => $prazo_max,
                        ],
                        'order' => [
                            'Orcamento.valor' => 'ASC',
                            'Orcamento.prazoEntrega' => 'ASC',
                        ],
                    ]);
                    if (empty($servico['Orcamento']['id'])) {
                        $servico = $this->Orcamento->find('first', [
                            'conditions' => [
                                'Orcamento.sigepweb_encomenda_id' => $encomenda_id,
                            ],
                            'order' => [
                                'Orcamento.prazoEntrega' => 'ASC',
                                'Orcamento.valor' => 'ASC',
                            ],
                        ]);
                    }
                    break;

                default:
                    break;
            }
            if (!empty($servico['Orcamento']['id'])) {
                $this->id = $encomenda_id;
                $this->saveField('sigepweb_servico_id', $servico['Orcamento']['sigepweb_servico_id']);
            }
        } else {
            $this->id = $encomenda_id;
            $this->saveField('sigepweb_servico_id', $encomenda['SigepConta']['padrao_sigepweb_servico_id']);
        }
    }
    
    public function SigepwebOrcamentos($encomenda_id){
        $encomenda = $this->read(null, $encomenda_id);
        $this->sigepweb_start($encomenda);
        $lista_servicos = $this->SigepConta->Servico->find('all', [
            'conditions' => [
                'Servico.id IN (SELECT sigepweb_servico_id FROM sigepweb_contas_servicos WHERE sigepweb_conta_id=?)' => $encomenda['SigepConta']['id'],
            ],
        ]);
        $servicos = [];
        foreach ($lista_servicos as $s){
            try {
                $servicos[] = new \PhpSigep\Model\ServicoDePostagem($s['Servico']['id']);
            } catch (Exception $ex) {
            }
        }
        
        $dimensao = new \PhpSigep\Model\Dimensao();
        $dimensao->setTipo(\PhpSigep\Model\Dimensao::TIPO_PACOTE_CAIXA);
        
        $params = new \PhpSigep\Model\CalcPrecoPrazo();
        $params->setServicosPostagem($servicos);
        $params->setAccessData($this->sigepweb_access_data($encomenda));
        $params->setCepOrigem($encomenda['Emitente']['cep']);
        $params->setCepDestino($encomenda['DestinoLocal']['cep']);
        $params->setAjustarDimensaoMinima(true);
        $params->setDimensao($dimensao);
        $params->setPeso((($encomenda['Encomenda']['nfe_peso_gr']<100 ? 100:$encomenda['Encomenda']['nfe_peso_gr']) / 1000));
        
        $phpSigep = new \PhpSigep\Services\SoapClient\Real();
        $result = $phpSigep->calcPrecoPrazo($params)->getResult();
        if (isset($result[0])){
            foreach ($result as $r){
                $data = $r->toArray();
                if ($data['valor']==0 || $data['prazoEntrega']==0) continue;
                $data['sigepweb_encomenda_id'] = $encomenda_id;
                $data['sigepweb_servico_id'] = (int)$data['servico']['codigo'];
                $this->Orcamento->create();
                $this->Orcamento->save($data);
            }
        }
    }
    
    public function SigepwebEtiquetas($encomenda_id) {
        //echo "\tSigepwebEtiquetas($encomenda_id);\n";
        //echo "CODIGO: {$codigo_com_dv}";
        $encomenda = $this->read(null, $encomenda_id);
        if (empty($encomenda['Encomenda']['sigepweb_servico_id'])){
            $this->id = $encomenda_id;
            $this->saveField('sigepweb_servico_id', $encomenda['SigepConta']['padrao_sigepweb_servico_id']);
            $encomenda = $this->read(null, $encomenda_id);
        }
        if (!empty($encomenda['Encomenda']['sigepweb_servico_id']) && empty($encomenda['Etiqueta']['id'])) {
            $saldo = $this->Etiqueta->find('count', [
                'conditions' => [
                    'Etiqueta.sigepweb_conta_id' => $encomenda['Encomenda']['sigepweb_conta_id'],
                    'Etiqueta.sigepweb_servico_id' => $encomenda['Encomenda']['sigepweb_servico_id'],
                    'Etiqueta.sigepweb_encomenda_id' => null,
                ],
            ]);
            if ($saldo<=1000){
                $this->sigepweb_start($encomenda);

                $etiquetas = new \PhpSigep\Model\SolicitaEtiquetas();
                $etiquetas->setModoUmaRequisicao();
                $etiquetas->setAccessData($this->sigepweb_access_data($encomenda));
                $etiquetas->setQtdEtiquetas(5000);
                $etiquetas->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem($encomenda['Servico']['codigo']));
                $phpSigep = new \PhpSigep\Services\SoapClient\Real();
                $data = [];
                try {
                    $result = $phpSigep->solicitaEtiquetas($etiquetas)->getResult();
                    if (is_array($result)){
                        foreach ($result as $i){
                            $e = $i->toArray();
                            $data[] = [
                                'sigepweb_conta_id' => $encomenda['Encomenda']['sigepweb_conta_id'],
                                'sigepweb_servico_id' => $encomenda['Encomenda']['sigepweb_servico_id'],
                                'codigo' => $e['etiquetaSemDv'],
                                'digito' => $e['dv'],
                                'codigo_com_dv' => $e['etiquetaComDv'],
                            ];
                        }
                        $this->Etiqueta->create();
                        if (!$this->Etiqueta->saveMany($data)) {
                            //die(print_r($this->Etiqueta->validationErrors));
                        } else {
                            //echo "ETIQUETAS CADASTRADAS CONTA: {$encomenda['Encomenda']['sigepweb_conta_id']}"."\n";
                        }
                    } else {
                        //echo 'ERRO: Falha ao solicitar etiquetas, verificar credenciais de acesso!'."\n";
                    }
                } catch (Exception $ex) {
                    die($ex->getMessage());
                }
            }
            $this->query("UPDATE sigepweb_etiquetas ET SET ET.sigepweb_encomenda_id={$encomenda_id} WHERE sigepweb_servico_id={$encomenda['Encomenda']['sigepweb_servico_id']} AND sigepweb_encomenda_id IS NULL LIMIT 1");
        }
    }
    
}