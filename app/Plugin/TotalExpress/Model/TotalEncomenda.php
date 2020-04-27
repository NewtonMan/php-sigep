<?php
require_once ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';

use NFePHP\NFe\Common\Standardize;

class TotalEncomenda extends TotalExpressAppModel {
    
    public $useTable = 'total_express_encomendas';
    
    public $order = [
        'TotalEncomenda.id' => 'DESC',
    ];
    
    public $belongsTo = [
        'TotalConta' => [
            'className' => 'TotalExpress.TotalConta',
            'foreignKey' => 'total_express_conta_id',
        ],
        'Servico' => [
            'className' => 'TotalExpress.Servico',
            'foreignKey' => 'total_express_servico_id',
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
            'className' => 'TotalExpress.Local',
            'foreignKey' => 'nfe_destino_local_id',
        ],
        'Transportadora' => [
            'className' => 'Cadastros.Destino',
            'foreignKey' => 'nfe_transportadora_id',
        ],
        'TotalStatus' => [
            'className' => 'TotalExpress.TotalStatus',
            'foreignKey' => 'total_express_status_id',
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
    
    public function importNFe($arquivo, $total_express_conta_id, $total_express_servico_id=null) {
        try {
            $std = new NFePHP\NFe\Common\Standardize();
            $nfe = $std->toArray(file_get_contents($arquivo));
            switch ($nfe['attributes']['versao']) {
                case '4.00':
                case '3.10':
                    return $this->schemaFromNFe400($nfe, $total_express_conta_id, $total_express_servico_id);
                    break;

                default:
                    return false;
                    break;
            }
        } catch (Exception $ex) {
            return false;
        }
    }
    
    private function schemaFromNFe400($nfe, $total_express_conta_id, $total_express_servico_id=null){
        $nfe_chave = substr($nfe['NFe']['infNFe']['attributes']['Id'], 3, 44);
        $e = $this->find('count', ['conditions' => [
            "{$this->alias}.nfe_chave" => $nfe_chave,
        ]]);
        if ($e==0){
            $Destino = ClassRegistry::init('Cadastros.Destino');
            $DestinoLocal = ClassRegistry::init('TotalExpress.Local');
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
            if (empty($total_express_servico_id)) {
                $conta = $this->TotalConta->read(null, $total_express_conta_id);
                $total_express_servico_id = $conta['TotalConta']['padrao_total_express_servico_id'];
            }
            $data = [
                'total_express_conta_id' => $total_express_conta_id,
                'total_express_servico_id' => $total_express_servico_id,
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
        return $this->find('first', ['conditions' => [
            "{$this->alias}.nfe_chave" => $nfe_chave,
        ]]);
    }
    
}