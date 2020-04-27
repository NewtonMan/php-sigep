<?php
class City extends UserManagementAppModel {

    public $useTable = 'city';

    public $belongsTo = array(
        'State' => array(
            'className' => 'UserManagement.State',
            'foreignKey' => 'id_state',
        ),
    );

}
