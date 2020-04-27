<?php
class Conta extends SigepwebAppModel {
    
    public $useTable = 'sigepweb_contas';
    
    public $displayField = 'cartao_postagem';
    
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
    
}