<?php
class IbgeCep extends AppModel {
    public $recursive = 2;
    public $useTable = 'ibge_cep';
    public $name = 'IbgeCep';
    public $displayField = 'cep';
    public $order = array(
        'IbgeCep.cep' => 'ASC',
    );
    public $belongsTo = array(
        'IbgeCidade',
    );
}