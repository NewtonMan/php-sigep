<?php
class Zone extends UserManagementAppModel {

    public $useTable = 'zone';

    public $belongsTo = array(
        'State' => array(
            'className' => 'UserManagement.State',
            'foreignKey' => 'id_state',
        ),
    );

    public $virtualFields = array(
        'display' => "CONCAT(Zone.iso_name, ' ', Zone.iso, ' - ', Zone.title)",
    );

}
