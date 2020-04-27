<?php
class Local extends SigepwebAppModel {
    
    public $useTable = 'sigepweb_local';
    
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
    
}