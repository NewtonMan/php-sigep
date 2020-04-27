<?php
class ModalidadeFrete extends EmbarcadorAppModel {
    
    public $useTable = 'embarcador_modalidade_frete';
    
    public $displayField = 'description';
    
    public $order = [
        'ModalidadeFrete.description' => 'ASC',
    ];
    
}