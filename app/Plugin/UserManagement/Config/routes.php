<?php
Router::connect('/login', array('plugin'=>'UserManagement', 'controller' => 'auth', 'action' => 'login'));
Router::connect('/logout', array('plugin'=>'UserManagement', 'controller' => 'auth', 'action' => 'logout'));
Router::connect('/forgotEmail', array('plugin'=>'UserManagement', 'controller' => 'auth', 'action' => 'lost_email'));
Router::connect('/forgotPassword', array('plugin'=>'UserManagement', 'controller' => 'auth', 'action' => 'lost_password'));
Router::connect('/resetPassword', array('plugin'=>'UserManagement', 'controller' => 'auth', 'action' => 'reset_password'));
Router::connect('/userRegister', array('plugin'=>'UserManagement', 'controller' => 'users', 'action' => 'register'));
Router::connect('/registro', array('plugin'=>'UserManagement', 'controller' => 'users', 'action' => 'register'));
Router::connect('/register', array('plugin'=>'UserManagement', 'controller' => 'users', 'action' => 'register'));
