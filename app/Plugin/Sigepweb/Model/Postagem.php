<?php
require_once ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';

use PhpSigep\Model\Diretoria;
use NFePHP\NFe\Common\Standardize;

class Postagem extends SigepwebAppModel {
    
    public $useTable = 'sigepweb_postagens';
    
    public $belongsTo = [
        'Conta' => [
            'className' => 'Sigepweb.Conta',
            'foreignKey' => 'sigepweb_conta_id',
        ],
        'SigepConta' => [
            'className' => 'Sigepweb.SigepConta',
            'foreignKey' => 'sigepweb_conta_id',
        ],
    ];
    
    public $hasAndBelongsToMany = [
        'Encomenda' => [
            'className' => 'Sigepweb.Encomenda',
            'joinTable' => 'sigepweb_postagens_encomendas',
            'foreignKey' => 'sigepweb_postagem_id',
            'associationForeignKey' => 'sigepweb_encomenda_id',
            'unique' => true,
        ],
    ];
    
    public function PreListaPostagem($conta_id, $ids){
        $objetos = [];
        foreach ($ids as $encomenda_id){
            $objetos[$encomenda_id] = $this->Encomenda->getObjeto($encomenda_id);
        }
        
        $conta = $this->Conta->read(null, $conta_id);
        $remetente = new \PhpSigep\Model\Remetente();
        $remetente->setNome($conta['Cliente']['fantasia']);
        $remetente->setLogradouro($conta['Cliente']['endereco']);
        $remetente->setNumero($conta['Cliente']['numero']);
        $remetente->setComplemento($conta['Cliente']['complemento']);
        $remetente->setBairro($conta['Cliente']['bairro']);
        $remetente->setCep($conta['Cliente']['cep']);
        $remetente->setUf($conta['Cliente']['uf']);
        $remetente->setCidade($conta['Cliente']['municipio']);
        
        $plp = new \PhpSigep\Model\PreListaDePostagem();
        $plp->setAccessData($this->sigepweb_access_data($conta));
        $plp->setEncomendas($objetos);
        $plp->setRemetente($remetente);
        
        $phpSigep = new PhpSigep\Services\SoapClient\Real();
        $result = $phpSigep->fechaPlpVariosServicos($plp);
        if (!empty($result->getErrorMsg())){
            echo "ERRO PLP: {$result->getErrorMsg()}\n";
            $plpParts = explode('[', $result->getErrorMsg());
            if (isset($plpParts[1])){
                $plpParts = explode(']', $plpParts[1]);
                $etiquetas = explode(', ', $plpParts[0]);
                $ids = [];
                foreach ($etiquetas as $etq){
                    $e = $this->Encomenda->find('first', ['conditions' => [
                        'Etiqueta.codigo' => $etq,
                    ]]);
                    $ids[] = $e['Encomenda']['id'];
                }
                $postagem = [
                    'Postagem' => [
                        'sigepweb_conta_id' => $conta_id,
                        'id_plp' => 1,
                    ],
                    'Encomenda' => $ids,
                ];
                $this->create();
                $this->saveAll($postagem, ['deep' => true]);
            } else {
                $postagem = [
                    'Postagem' => [
                        'sigepweb_conta_id' => $conta_id,
                        'id_plp' => 0,
                    ],
                    'Encomenda' => $ids,
                ];
                $this->create();
                $this->saveAll($postagem, ['deep' => true]);
            }
        } elseif (!is_null($result)) {
            $data = $result->toArray();
            $postagem = [
                'Postagem' => [
                    'sigepweb_conta_id' => $conta_id,
                    'id_plp' => $data['result']['idPlp'],
                ],
                'Encomenda' => $ids,
            ];
            $this->create();
            $this->saveAll($postagem, ['deep' => true]);
        }
    }
    
}