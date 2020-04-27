<?php
class City extends EmbarcadorAppModel {
    
    public $useTable = 'embarcador_cities';
    
    public $order = [
        'City.name' => 'ASC',
    ];
    
    public $belongsTo = [
        'Zone' => [
            'className' => 'Embarcador.Zone',
            'foreignKey' => 'zone_id',
        ],
        'State' => [
            'className' => 'Embarcador.State',
            'foreignKey' => 'state_id',
        ],
    ];
    
}