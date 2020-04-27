<?php
class SigepConta extends SigepwebAppModel {
    
    public $useTable = 'sigepweb_contas';
    
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
    
    public $hasAndBelongsToMany = [
        'Servico' => [
            'className' => 'Sigepweb.Servico',
            'joinTable' => 'sigepweb_contas_servicos',
            'foreignKey' => 'sigepweb_conta_id',
            'associationForeignKey' => 'sigepweb_servico_id',
            'unique' => true,
        ],
    ];
    
    public $hasMany = [
        'ContaEstrategia' => [
            'className' => 'Sigepweb.ContaEstrategia',
            'foreignKey' => 'sigepweb_conta_id',
        ],
        'ContaServico' => [
            'className' => 'Sigepweb.ContaServico',
            'foreignKey' => 'sigepweb_conta_id',
        ],
    ];
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['displayField'] = "CONCAT({$this->alias}.id, ' - ', {$this->alias}.cartao_postagem)";
    }
    
    public function SigepwebCotacao($conta_id, $cepOrigem, $cepDestino, $peso){
        $cotacao = [];
        $conta = $this->read(null, $conta_id);
        if ($this->sigepweb_start($conta)){
            $lista_servicos = $this->Servico->find('all', [
                'conditions' => [
                    'Servico.id IN (SELECT sigepweb_servico_id FROM sigepweb_contas_servicos WHERE sigepweb_conta_id=?)' => $conta_id,
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
            $params->setAccessData($this->sigepweb_access_data($conta));
            $params->setCepOrigem($cepOrigem);
            $params->setCepDestino($cepDestino);
            $params->setAjustarDimensaoMinima(true);
            $params->setDimensao($dimensao);
            $params->setPeso($peso);
            try {
                $phpSigep = new \PhpSigep\Services\SoapClient\Real();
                $result = $phpSigep->calcPrecoPrazo($params)->getResult();
                if (isset($result[0])){
                    foreach ($result as $r){
                        $data = $r->toArray();
                        if ($data['valor']==0 || $data['prazoEntrega']==0) continue;
                        $cotacao[] = $data;
                    }
                }
            } catch (Exception $ex) {
                
            }
        }
        return $cotacao;
    }
    
}