<?php
class ContaEstrategia extends SigepwebAppModel {
    
    public $useTable = 'sigepweb_contas_estrategias';
    
    public $belongsTo = [
        'Conta' => [
            'className' => 'Sigepweb.Conta',
            'foreignKey' => 'sigepweb_conta_id',
        ],
        'SigepConta' => [
            'className' => 'Sigepweb.SigepConta',
            'foreignKey' => 'sigepweb_conta_id',
        ],
        'Estrategia' => [
            'className' => 'Sigepweb.Estrategia',
            'foreignKey' => 'sigepweb_estrategia_id',
        ],
        'Estado' => [
            'className' => 'IbgeEstado',
            'foreignKey' => 'ibge_estado_id',
        ],
    ];
    
}