<?php
class DestinoLocal extends CadastrosAppModel {

    public $useTable = 'destino_local';
    public $actsAs = array('Brasil');
    public $displayField = 'cpf_cnpj';
    
    public $belongsTo = [
        'IbgeCidade' => [
            'className' => 'IbgeCidade',
            'foreignKey' => 'ibge_cidade_id',
        ],
        'IbgeEstado' => [
            'className' => 'IbgeEstado',
            'foreignKey' => 'ibge_estado_id',
        ],
    ];
    
    public function beforeFind($query = array()) {
        $query = parent::beforeFind($query);
        if (isset($_GET['sw'])){
            if (!empty($_GET['sw'])){
                $n = "%" . onlyNumbers($_GET['sw']) . "%";
                $n = ($n == '%%' ? 99999999999999999999999999:$n);
                $search = array('OR' => array(
                    "{$this->alias}.municipio LIKE ?" => "%".  str_replace(' ', '%', $_GET['sw'])."%",
                    "{$this->alias}.uf = ?" => $_GET['sw'],
                    "{$this->alias}.cpf_cnpj LIKE ?" => exibirCpfCnpj($n),
                    "ONLY_NUMBERS({$this->alias}.cpf_cnpj) LIKE ?" => $n,
                    "ONLY_NUMBERS({$this->alias}.cep) LIKE ?" => $n,
                ));
                $query['conditions'] = array($query['conditions'], $search);
            }
        }
        return $query;
    }
    
}