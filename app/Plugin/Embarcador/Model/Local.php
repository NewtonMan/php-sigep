<?php
class Local extends EmbarcadorAppModel {
    
    public $useTable = 'embarcador_locais';
    
    public $belongsTo = [
        'City' => [
            'className' => 'Embarcador.City',
            'foreignKey' => 'cMun',
        ],
    ];
    
}