<?php
class IbgeCidade extends AppModel {
    public $useTable = 'ibge_cidade';
    public $name = 'IbgeCidade';
    public $displayField = 'nome';
    public $order = array(
        'IbgeCidade.nome' => 'ASC',
    );
    
    public function getNear($latitude, $longitude, $radius=0){
        $radius = (int)$radius;
        return $this->query("SELECT IbgeCidade.*,IbgeEstado.*,(((acos(sin((".$latitude."*pi()/180)) * sin((`lat`*pi()/180))+cos((".$latitude."*pi()/180)) * cos((`lat`*pi()/180)) * cos(((".$longitude."- `lon`)*pi()/180))))*180/pi())*60*1.1515*1.609344) as distancia FROM `ibge_cidade` as IbgeCidade JOIN `ibge_estado` as IbgeEstado ON IbgeEstado.id=IbgeCidade.id_uf HAVING distancia <= ". $radius. " ORDER BY distancia");
    }
    
    public function getNameByCod($ibge_cidade_id){
        $data = $this->read(null, $ibge_cidade_id);
        return $data[$this->alias]['nome'];
    }
    
    public function getCodByUfAndName($uf, $name){
        $cod = false;
        $city = $this->find('first', array('conditions'=>array(
            "{$this->alias}.id_uf=(SELECT id FROM ibge_estado WHERE sigla=?)" => $uf,
            "{$this->alias}.nome" => $name,
        )));
        if (!empty($city[$this->alias]['id'])){
            $cod = $city[$this->alias]['id'];
        }
        return $cod;
    }
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields = array(
            'uf' => "SELECT sigla FROM ibge_estado WHERE id={$this->alias}.id_uf",
        );
    }
    
}