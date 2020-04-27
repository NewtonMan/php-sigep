<?php
class TmsTableCostRoute extends EmbarcadorAppModel {
    
    public $useTable = 'embarcador_tms_table_costs_routes';
    
    public $belongsTo = [
        'State' => [
            'className' => 'Embarcador.State',
            'foreignKey' => 'state_id',
        ],
        'Zone' => [
            'className' => 'Embarcador.Zone',
            'foreignKey' => 'zone_id',
        ],
        'City' => [
            'className' => 'Embarcador.City',
            'foreignKey' => 'city_id',
        ],
    ];
    
}