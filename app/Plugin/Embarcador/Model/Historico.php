<?php
class Historico extends EmbarcadorAppModel {
    
    public $useTable = 'embarcador_historicos';
    
    public $belongsTo = [
        'Usuario' => [
            'className' => 'Usuario',
            'foreignKey' => 'usuario_id',
        ],
    ];
    
}