<?php
class Status extends EmbarcadorAppModel {
    
    public $useTable = 'embarcador_status';
    
    public $order = [
        'Status.name' => 'ASC',
    ];
    
}