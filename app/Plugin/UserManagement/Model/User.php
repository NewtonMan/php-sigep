<?php
App::uses('AuthComponent', 'Controller/Component');
class User extends UserManagementAppModel {

    public $useTable = 'empresa_usuario';
    
    public $displayField = 'nome_completo';
    
    public $validate = array(
        'nome_completo' => array(
            'rule1' => array(
                'rule' => array('notBlank'),
                'message' => 'Informe seu Nome Completo.',
            ),
        ),
        'email' => array(
            'rule1' => array(
                'rule' => array('notBlank'),
                'message' => 'Informe seu E-mail.',
            ),
            'rule2' => array(
                'rule' => array('email'),
                'message' => 'E-mail inválido.',
            ),
            'rule3' => array(
                'rule' => array('isUnique'),
                'message' => 'E-mail já está em uso.',
            ),
        ),
    );
    
    public function beforeSave($options = array()) {
        if (!empty($this->data[$this->alias]['senha'])) {
            $this->data[$this->alias]['senha'] = AuthComponent::password($this->data[$this->alias]['senha']);
        }
        return true;
    }
    
}
