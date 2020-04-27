<?php
class CorreioStatus extends EmbarcadorAppModel {
    
    public $useTable = 'embarcador_correios_status';
    
    public $displayField = 'Descricao';
    
    public $belongsTo = [
        'Status' => [
            'className' => 'Embarcador.Status',
            'foreignKey' => 'status_id',
        ],
    ];
    
    public $order = [
        'CorreioStatus.Descricao' => 'ASC',
    ];
    
}