<?php
App::uses('CakeEmail', 'Network/Email');

class UsuarioController extends CadastrosAppController {
    public $uses = array('Usuario', 'UsuarioCliente', 'UsuarioLogin', 'Orcamento', 'IbgeEstado', 'Destino', 'Empresa');
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
        $this->Auth->allow('esqueci');
        $this->Auth->allow('login');
    }
    
    public function index() {
        $this->Session->write('refer', $this->here);
        $this->Usuario->recursive = 0;
        $this->set('usuarios', $this->paginate('Usuario'));
    }
    
    public function view($id = null) {
        $this->Usuario->id = $id;
        if (!$this->Usuario->exists()) {
            throw new NotFoundException(__('Usu�rio Inv�lido'), 'mensagens/alerta');
        }
        $this->set('usuario', $this->Usuario->read(null, $id));
    }
    
    public function add() {
        if ($this->request->is('post')) {
            $this->Usuario->create();
            $this->request->data['Usuario']['empresa_id'] = 1;
            if ($this->Usuario->save($this->request->data)) {
                $this->Session->setFlash(__('Usu�rio Salvo!'), 'mensagens/sucesso');
                $this->redirect($this->Session->read('refer'));
            } else {
                $this->Session->setFlash(__('N�o foi poss�vel processar sua solicita��o, tente mais tarde.'), 'mensagens/alerta');
            }
        }
        $clientes = $this->Destino->find('list', ['conditions' => [
            'Destino.cliente' => 1,
        ]]);
        $this->set(compact('clientes'));
        $this->render('form');
    }
    
    public function edit($id = null) {
        $this->Usuario->id = $id;
        if (!$this->Usuario->exists()) {
            throw new NotFoundException(__('Usu�rio inv�lido!'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if (empty($this->request->data['Usuario']['senha'])){
                unset($this->request->data['Usuario']['senha']);
            }
            $this->request->data['Usuario']['empresa_id'] = $this->Auth->User('empresa_id');
            if ($this->Usuario->save($this->request->data)) {
                $this->Session->setFlash(__('Usu�rio Salvo!'), 'mensagens/sucesso');
                $this->redirect($this->Session->read('refer'));
            } else {
                $this->Session->setFlash(__('N�o foi poss�vel processar sua solicita��o, tente mais tarde.'), 'mensagens/alerta');
            }
        } else {
            $this->request->data = $this->Usuario->read(null, $id);
            unset($this->request->data['Usuario']['senha']);
        }
        $clientes = $this->Destino->find('list', ['conditions' => [
            'Destino.cliente' => 1,
        ]]);
        $this->set(compact('clientes'));
        $this->render('form');
    }
    
    public function logar_como($id = null) {
        $usuario = $this->Usuario->read(null, $id);
        $data = $usuario['Usuario'];
        $this->Auth->login($data);
        return $this->redirect('/');
    }

    public function delete($id = null) {
        $this->Usuario->id = $id;
        if (!$this->Usuario->exists()) {
            throw new NotFoundException(__('Usu�rio inv�lido!'));
        }
        if ($this->Usuario->saveField('ativo', null)) {
            $this->Session->setFlash(__('Usu�rio Removido!'), 'mensagens/sucesso');
            $this->redirect($this->Session->read('refer'));
        }
        $this->Session->setFlash(__('O usu�rio n�o foi removido!'), 'mensagens/alerta');
        $this->redirect($this->Session->read('refer'));
    }
    
    public function login(){
        $this->layout = 'login';
        if ($this->request->is('post')){
            if ($this->Auth->login()){
                $login = array(
                    'usuario_id' => $this->Auth->User('id'),
                    'remote_addr' => $_SERVER['REMOTE_ADDR'],
                    'x_fwd_for' => $_SERVER['X-Forwarded-For'],
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                );
                $this->UsuarioLogin->create();
                $this->UsuarioLogin->save($login);
                return $this->redirect($this->Auth->redirect());
            } else {
                $this->Session->setFlash(__('Usu�rio/Senha inv�lido.'), 'mensagens/alerta');
            }
        }
        $this->set('ibgeEstados', $this->IbgeEstado->find('list'));
    }

    public function logout() {
        $this->Session->destroy();
        $this->redirect($this->Auth->logout());
    }
    
    public function analitico_login(){
        $this->layout = 'login';
        if ($this->request->is('post')){
            if ($this->Auth->login()){
                $this->redirect($this->Auth->redirect());
            } else {
                $this->Session->setFlash(__('Usu�rio/Senha inv�lido.'), 'mensagens/alerta');
            }
        }
        $this->render('login');
    }

    public function analitico_index(){
        $this->redirect(array('controller'=>'avisos', 'action'=>'index'));
    }

    public function analitico_logout() {
        $this->redirect($this->Auth->logout());
    }
    
    public function acerta_senhas(){
        $users = $this->Usuario->find('all');
        foreach ($users as $user) {
            $this->Usuario->id = $user['Usuario']['id'];
            $this->Usuario->saveField('senha', $user['Usuario']['senha']);
        }
    }
    
    public function esqueci($code=null, $key=null, $email=null){
        $this->layout = 'login';
        $salt = 234234123412341234123;
        if (is_null($code)){
            if ($this->request->is('post')){
                $user = $this->Usuario->find('first', array('conditions'=>array('email'=>$this->request->data['Usuario']['email'])));
                if (strnatcasecmp($user['Usuario']['email'], $this->request->data['Usuario']['email'])==0){
                    $code = rand(12312312312,4567456754756);
                    $key = substr(hash('sha256', $this->request->data['Usuario']['email'].$code.$salt), 0, 8);
                    
                    $message = "Ol�,\n\nTentativa de recuperar acesso, se foi voc� quem solicitou este acesso, clique no link abaixo:\nhttp://{$_SERVER['HTTP_HOST']}/usuario/esqueci/{$code}/{$key}/".urlencode($this->request->data['Usuario']['email'])."\n\nCaso n�o tenha sido voc�, ignore este e-mail.";

                    $Email = new CakeEmail();
                    $Email->from(array('nao-responda@'.$_SERVER['HTTP_HOST'] => $_SERVER['HTTP_HOST']));
                    $Email->to($this->request->data['Usuario']['email']);
                    $Email->subject('Solicita��o de Recuperar Acesso');
                    $Email->send($message);
                    $this->Session->setFlash('Ok, verifique seu e-mail.', 'mensagens/sucesso');
                } else {
                    $this->Session->setFlash('Erro, e-mail n�o encontrado.', 'mensagens/alerta');
                }
            }
        } else {
            $email = urldecode($email);
            $ckey = substr(hash('sha256', $email.$code.$salt), 0, 8);
            if ($key==$ckey){
                $user = $this->Usuario->find('first', array('conditions'=>array('email'=>$email)));
                $senha = substr(hash('sha1', $email.$code.$salt), 0, 6);
                $this->Usuario->id = $user['Usuario']['id'];
                $this->Usuario->saveField('senha', $senha);
                
                $message = "Ol�,\n\nSua Senha foi resetada para: {$senha}";
                
                $Email = new CakeEmail();
                $Email->from(array('nao-responda@'.$_SERVER['HTTP_HOST'] => $_SERVER['HTTP_HOST']));
                $Email->to($email);
                $Email->subject('Sua Nova Senha');
                $Email->send($message);
                
                $this->Session->setFlash('Ok, sua senha foi enviada para seu e-mail.', 'mensagens/informacao');
            } else {
                $this->Session->setFlash('Erro, Token inv�lido.', 'mensagens/alerta');
            }
        }
    }
    
    public function clientes($id = null){
        $this->UsuarioCliente->recursive = 0;
        if ($this->request->is('post') || $this->request->is('put')){
            $this->UsuarioCliente->deleteAll(array('usuario_id'=>$id));
            foreach ($this->request->data['UsuarioCliente'] as $x => $data){
                if (empty($data['cliente_id'])){
                    unset($this->request->data['UsuarioCliente'][$x]);
                }
            }
            $this->UsuarioCliente->create();
            if ($this->UsuarioCliente->saveAll($this->request->data['UsuarioCliente'])){
                $this->Session->setFlash('Clientes definidos', 'mensagens/sucesso');
            } else {
                $this->Session->setFlash(__('Erro Interno'), 'mensagens/alerta');
            }
            return $this->redirect('/usuario/clientes/'.$id);
        } else {
            if (!is_null($id)){
                $clientes = $this->UsuarioCliente->find('all', array('conditions'=>array('usuario_id'=>$id)));
                $arr_clientes = array();
                foreach ($clientes as $item){
                    $arr_clientes[$item['UsuarioCliente']['cliente_id']] = $item['UsuarioCliente']['cliente_id'];
                }
                sort($arr_clientes);
                $usuario = $this->Usuario->find('first', array('conditions'=>array('Usuario.id'=>$id)));
                $this->set('user_id', $usuario['Usuario']['id']);
                $this->set('usuario', $usuario['Usuario']['nome']);
                $this->set('clientes', $arr_clientes);
                $this->set('lista', $this->Destino->find('all', array('conditions'=>array('Destino.empresa_id'=>$this->Auth->User('empresa_id'), 'cliente'=>1))));
            } else {
                $this->Session->setFlash(__('Erro Interno'), 'mensagens/alerta');
            }
        }
    }
    
    public function marca_cliente($user_id, $cliente_id){
        $this->UsuarioCliente->recursive = 0;
        $marcado = $this->UsuarioCliente->find('all', array('conditions'=>array('usuario_id'=>$user_id, 'cliente_id'=>$cliente_id)));
        $total = count($marcado);
        if ($total == 0){
            $this->UsuarioCliente->create();
            $this->UsuarioCliente->save(array('usuario_id'=>$user_id, 'cliente_id'=>$cliente_id));
        } else {
            $this->UsuarioCliente->id = $marcado[0]['UsuarioCliente']['id'];
            $this->UsuarioCliente->delete();
        }
    }
    
}