<?php
class IbgeEstado extends AppModel {
    public $useTable = 'ibge_estado';
    public $name = 'IbgeEstado';
    public $displayField = 'sigla';
    
    public $order = array(
        'IbgeEstado.sigla' => 'ASC',
    );
    
    public function ufList(){
        $options = array();
        $estados = $this->find('all', array('recursive'=>-1));
        foreach ($estados as $estado){
            $options[$estado[$this->alias]['sigla']] = $estado[$this->alias]['sigla'];
        }
        return $options;
    }
    
    public function getUFByCod($id){
        $data = $this->read(null, $id);
        return $data[$this->alias]['sigla'];
    }
}