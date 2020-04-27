<?php
class State extends EmbarcadorAppModel {
    
    public $useTable = 'embarcador_states';
    
    public $order = [
        'State.letter' => 'ASC',
    ];
    
}