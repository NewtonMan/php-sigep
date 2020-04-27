<?php
class Destino extends CadastrosAppModel {
    public $useTable = 'destino';
    public $actsAs = array('Brasil');
    public $displayField = 'info';
    
    public function createIfNotExists($data){
        $data['cpf_cnpj'] = (int)onlyNumbers($data['cpf_cnpj']);
        $cadastrado = $this->find('count', ['conditions' => [
            "{$this->alias}.cpf_cnpj" => $data['cpf_cnpj'],
        ]]);
        if ($cadastrado==0){
            $this->create();
            $this->save($data, false);
            return $this->id;
        } else {
            $i = $this->find('first', ['conditions' => [
                "{$this->alias}.cpf_cnpj" => $data['cpf_cnpj'],
            ]]);
            return $i[$this->alias]['id'];
        }
    }
    
    public function beforeSave($options = array()) {
        unset($this->data[$this->alias]['icms_nao_cobrar']);
        if (isset($this->data[$this->alias]['cpf_cnpj'])){
            $this->data[$this->alias]['cpf_cnpj'] = onlyNumbers($this->data[$this->alias]['cpf_cnpj']);
        }
        return parent::beforeSave($options);
    }
    
    public function ICMS_Nao_Cobrar($id) {
        $this->query("UPDATE destino SET icms_nao_cobrar=1 WHERE id={$id}");
    }
    
    public function ICMS_Cobrar($id) {
        $this->query("UPDATE destino SET icms_nao_cobrar=NULL WHERE id={$id}");
    }
    
    public function beforeFind($query = array()) {
        $query = parent::beforeFind($query);
        if (isset($_GET['sw'])){
            if (!empty($_GET['sw'])){
                $n = "%" . onlyNumbers($_GET['sw']) . "%";
                $n = ($n == '%%' ? 99999999999999999999999999:$n);
                $search = array('OR' => array(
                    "{$this->alias}.fantasia LIKE ?" => "%".  str_replace(' ', '%', $_GET['sw'])."%",
                    "{$this->alias}.nome_razao LIKE ?" => "%".  str_replace(' ', '%', $_GET['sw'])."%",
                    "{$this->alias}.municipio LIKE ?" => "%".  str_replace(' ', '%', $_GET['sw'])."%",
                    "{$this->alias}.uf = ?" => $_GET['sw'],
                    "{$this->alias}.cpf_cnpj" => onlyNumbers($n),
                    "ONLY_NUMBERS({$this->alias}.cep)" => onlyNumbers($n),
                ));
                $query['conditions'] = array($query['conditions'], $search);
            }
        }
        return $query;
    }
    
    public function getSelectOptions($kind){
        $criteria = array();
        $marks = explode(',', $kind);
        foreach ($marks as $m) {
            if (substr($m,0,1)=='!'){
                $m = substr($m, 1);
                $criteria["({$this->alias}.{$m} IS NULL OR {$this->alias}.{$m} = ?)"] = 0;
            } else {
                $criteria["{$this->alias}.{$m}"] = 1;
                if ($m=='cliente'){
                    $uid = AuthComponent::User('id');
                    $criteria["{$this->alias}.id"] = $this->meusClientes($uid);
                }
            }
        }
        return $this->find('list', array('conditions' => $criteria, 'order' => array("{$this->alias}.info" => 'ASC')));
    }
    
    public function meusArmazens(){
        $uid = AuthComponent::User('id');
        $acesso_armazem_id = AuthComponent::User('acesso_armazem_id');
        if (!empty($acesso_armazem_id)){
            return [$acesso_armazem_id];
        } else {
            $key = "meus-aids-".$uid;
            $ids = Cache::read($key);
            if (!is_array($ids)){
                $ids = array();
                $tmp = $this->find('all', array('conditions' => array(
                    "{$this->alias}.armazem" => 1,
                )));
                foreach ($tmp as $i){
                    $ids[] = $i[$this->alias]['id'];
                }
                Cache::write($key, $ids);
            }
            return $ids;
        }
    }
    
    public function meusClientes($uid){
        $acesso_cliente_id = AuthComponent::User('acesso_cliente_id');
        if (!empty($acesso_cliente_id)){
            return [$acesso_cliente_id];
        }
        $key = 'meus-cids-'.$uid;
        $ids = Cache::read($key);
        if (!is_array($ids)){
            $ids = array();
            $UC = ClassRegistry::init('UsuarioCliente');
            $tmp = $UC->find('all', array('conditions' => array(
                'usuario_id' => AuthComponent::User('id'),
            )));
            foreach ($tmp as $UCI){
                $ids[] = $UCI['UsuarioCliente']['cliente_id'];
            }
            $total = count($ids);
            if ($total == 0){
                $tmp = $this->find('all', array('conditions' => array(
                    "{$this->alias}.cliente" => 1,
                )));
                foreach ($tmp as $UCI){
                    $ids[] = $UCI[$this->alias]['id'];
                }
            }
            Cache::write($key, $ids);
        }
        return $ids;
    }
    
    public $validate = array(
        'nome_razao' => array(
            'rule' => 'notBlank',
            'message' => 'Informe a Razão Social ou Nome da Pessoa Física',
        ),
        'fantasia' => array(
            'rule' => 'notBlank',
            'message' => 'Informe o Nome Fantasia ou Apelido para Pessoa Física',
        ),
        'cpf_cnpj' => array(
            'rule' => 'isUnique',
            'message' => 'CPF/CNPJ já duplicado.',
            'on' => 'create',
        ),
        'endereco' => array(
            'rule' => 'notBlank',
            'message' => 'Informe o Logradouro',
        ),
        'uf' => array(
            'rule' => 'notBlank',
            'message' => 'Defina a UF',
        ),
        'ibge_cidade_id' => array(
            'rule' => 'notBlank',
            'message' => 'Defina a Cidade',
        ),
        'bairro' => array(
            'rule' => 'notBlank',
            'message' => 'Digite o Bairro',
        ),
        'numero' => array(
            'rule' => 'notBlank',
            'message' => 'Digite o Número/Altura',
        ),
    );
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['info'] = sprintf("CONCAT((CASE WHEN IFNULL(%s.codigo_cliente,'') != '' THEN CONCAT(%s.codigo_cliente, ' - ') ELSE '' END), %s.fantasia, ' CPF/CNPJ ', %s.cpf_cnpj)", $this->alias, $this->alias, $this->alias, $this->alias);
        //$this->virtualFields['info'] = sprintf("CONCAT(%s.fantasia, ' CPF/CNPJ ', %s.cpf_cnpj)", $this->alias, $this->alias);
        $this->order = [
            "{$this->alias}.fantasia" => 'ASC',
        ];
    }
    
}