<?php
App::uses('AuthComponent', 'Controller/Component');
class Destino extends AppModel {

    public $useTable = 'destino';
    
    public $displayField = 'fantasia';
    
    public $validate = array(
        'cpf_cnpj' => array(
            'rule1' => array(
                'rule' => array('notBlank'),
                'message' => 'Informe seu CPF/CNPJ.',
            ),
            'rule2' => array(
                'rule' => array('isUnique'),
                'message' => 'CPF/CNPJ já está em uso.',
            ),
        ),
    );
    
}
