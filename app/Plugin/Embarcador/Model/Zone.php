<?php
class Zone extends EmbarcadorAppModel {
    
    public $useTable = 'embarcador_zones';
    
    public $displayField = 'nome';
    
    public $belongsTo = [
        'State' => [
            'className' => 'Embarcador.State',
            'foreignKey' => 'state_id',
        ],
    ];
    
    public $order = [
        'Zone.DDD' => 'ASC',
    ];
    
}