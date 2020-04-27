<?php
class ContaServico extends SigepwebAppModel {
    
    public $useTable = 'sigepweb_contas_servicos';
    
    public $belongsTo = [
        'Conta' => [
            'className' => 'Sigepweb.Conta',
            'foreignKey' => 'sigepweb_conta_id',
        ],
        'SigepConta' => [
            'className' => 'Sigepweb.SigepConta',
            'foreignKey' => 'sigepweb_conta_id',
        ],
        'Servico' => [
            'className' => 'Sigepweb.Servico',
            'foreignKey' => 'sigepweb_servico_id',
        ],
        'Estado' => [
            'className' => 'IbgeEstado',
            'foreignKey' => 'ibge_estado_id',
        ],
    ];
    
}