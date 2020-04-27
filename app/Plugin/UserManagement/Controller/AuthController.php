<?php
require ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';

use Firebase\JWT\JWT;

App::uses('CakeLog', 'Log');

class AuthController extends UserManagementAppController {
    
    public $layout = "AdminLTE.clean";

    public $uses = array('UserManagement.User');
    
    public $components = ['RequestHandler'];
    
    public function beforeFilter(){
        parent::beforeFilter();
        $this->Auth->allow('api_login');
        $this->Auth->allow('login');
        $this->Auth->allow('lost_password');
        $this->Auth->allow('reset_password');
        $this->Auth->allow('sombra');
        $this->layout = 'AdminLTE.clean';
    }

    public function index(){
        return $this->redirect('/login');
    }

    public function reset_password(){
        $msg = '';
        $label = 'info';
        $token = substr(sha1($_GET['e'] . '|' . date("Ymd") . '|' . Configure::read('Security.salt')), 0, 8);
        if ($token==$_GET['token']){
            if ($this->request->is('post') || $this->request->is('put')){
                $u = $this->User->find('first', array('conditions' => array(
                    'User.email' => $_GET['e'],
                )));
                $this->User->id = $u['User']['id'];
                if ($this->User->save($this->request->data)){
                    $this->Session->setFlash('Sua nova senha foi aceita!');
                    return $this->redirect('/login');
                } else {
                    $msg = 'ERRO: Verifique o Formulário.';
                    $label = 'warning';
                }
            }
        } else {
            $this->Session->setFlash('Token deve ter expirado!');
            return $this->redirect('/forgotPassword');
        }
        $this->set(compact('msg', 'label'));
    }

    public function lost_password(){
        $msg = '';
        $label = 'info';
        if ($this->request->is('post')) {
            $u = $this->User->find('first', array('conditions' => array(
                'User.email' => $this->request->data['User']['email'],
            )));
            if (!$u){
                $msg = _('E-mail não está cadastrado.');
                $label = 'warning';
            } elseif (empty($u['User']['active'])){
                $msg = _('Cadastro não está Ativo, aguarde ser ativado ou entre em contato conosco.');
                $label = 'warning';
            } else {
                $msg = _('E-mail com link para resetar senha enviado.');
                
                $token = substr(sha1($u['User']['email'] . '|' . date("Ymd") . '|' . Configure::read('Security.salt')), 0, 8);
                $url = "http://{$_SERVER['HTTP_HOST']}/resetPassword?e={$u['User']['email']}&token={$token}";
                
                $Email = new CakeEmail();
                $Email->template('UserManagement.lost_password', 'default')
                    ->viewVars(array('u' => $u, 'url' => $url))
                    ->emailFormat('text')
                    ->to($u['User']['email'])
                    ->from('reset-password@'.$_SERVER['HTTP_HOST'])
                    ->subject('Recuperar Acesso')
                    ->send();
            }
        }
        $this->set(compact('msg', 'label'));
    }

    public function sombra($id){
        $user = $this->User->read(null, $id);
        $this->Auth->login($user['User']);
        return $this->redirect('/');
    }

    public function login(){
        if ($this->request->is('post')) {
            if (isset($this->request['User']['CPF'])){
                $this->request['User']['CPF'] = onlyNumbers($this->request['User']['CPF']);
            }
            if ($this->Auth->login()){
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Session->setFlash(__('Invalid email/password.'), 'AdminLTE.mensagens/alerta');
            }
        }
    }

    public function api_login(){
        $token = '';
        $data = $this->request->input('json_decode');
        $CPF = (int)onlyNumbers($data->User->CPF);
        $senha = AuthComponent::password($data->User->senha);
        $user = $this->User->find('first', ['conditions' => [
            'User.CPF' => $CPF,
            'User.senha' => $senha,
        ]]);
        if (empty($user['User']['id'])){
            throw new UnauthorizedException();
        } else {
            $token = JWT::encode($user['User'], Configure::read('Security.salt'));
        }
        $this->set([
            'token' => $token,
            'user' => $user['User'],
            '_serialize' => ['token', 'user']
        ]);
    }

    public function logout(){
        $route_default = AuthComponent::User('route_logout');
        $route = $this->Auth->logout();
        return $this->redirect((empty($route_default) ? $route:$route_default));
    }

}
