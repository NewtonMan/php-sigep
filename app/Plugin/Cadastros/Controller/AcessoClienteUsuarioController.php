<?php
class AcessoClienteUsuarioController extends CadastrosAppController {
    public $uses = array('AcessoClienteUsuario', 'AcessoClienteUsuarioCliente', 'AcessoClienteUsuarioFluxoLogistico', 'Destinatario', 'FluxoLogistico');
    
    public function index() {
        $this->Session->write('refer', $this->here);
        $criterios = $this->restringir('AcessoClienteUsuario');
        $this->AcessoClienteUsuario->recursive = 0;
        $this->set('usuarios', $this->paginate('AcessoClienteUsuario', $criterios));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->AcessoClienteUsuario->create();
            $this->request->data['AcessoClienteUsuario']['empresa_id'] = $this->Auth->User('empresa_id');
            if ($this->AcessoClienteUsuario->save($this->request->data)) {
                $this->Session->setFlash(__('Usuário de Cliente Criado!'), 'mensagens/sucesso');
                $this->redirect($this->Session->read('refer'));
            } else {
                $this->Session->setFlash(__('Não foi possível processar sua solicitação, tente mais tarde.'), 'mensagens/alerta');
            }
        }
        $this->render('form');
    }

    public function edit($id = null) {
        $this->AcessoClienteUsuario->id = $id;
        if (!$this->AcessoClienteUsuario->exists()) {
            throw new NotFoundException(__('Usuário de Cliente inválido!'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->AcessoClienteUsuario->save($this->request->data)) {
                $this->Session->setFlash(__('Usuário de Cliente Salvo!'), 'mensagens/sucesso');
                $this->redirect($this->Session->read('refer'));
            } else {
                $this->Session->setFlash(__('Não foi possível processar sua solicitação, tente mais tarde.'), 'mensagens/alerta');
            }
        } else {
            $this->request->data = $this->AcessoClienteUsuario->read(null, $id);
            unset($this->request->data['AcessoClienteUsuario']['senha']);
        }
        $this->render('form');
    }

    public function delete($id = null) {
        if ($this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->AcessoClienteUsuario->id = $id;
        if (!$this->AcessoClienteUsuario->exists()) {
            throw new NotFoundException(__('Usuário de Cliente inválido!'));
        }
        if ($this->AcessoClienteUsuario->delete()) {
            $this->Session->setFlash(__('Usuário de Cliente Removido!'), 'mensagens/sucesso');
            $this->redirect($this->referer());
        }
        $this->Session->setFlash(__('O usuário não foi removido!'), 'mensagens/alerta');
        $this->redirect($this->referer());
    }
    
    public function clientes($id = null){
        $this->AcessoClienteUsuarioCliente->recursive = 0;
        if (!is_null($id)){
            $clientes = $this->AcessoClienteUsuarioCliente->find('all', array('conditions'=>array('acesso_cliente_usuario_id'=>$id)));
            $arr_clientes = array();
            foreach ($clientes as $item){
                $arr_clientes[$item['AcessoClienteUsuarioCliente']['cliente_id']] = $item['AcessoClienteUsuarioCliente']['cliente_id'];
            }
            sort($arr_clientes);
            $usuario = $this->AcessoClienteUsuario->find('first', array('conditions'=>array('AcessoClienteUsuario.id'=>$id)));
            $this->set('user_id', $usuario['AcessoClienteUsuario']['id']);
            $this->set('usuario', $usuario['AcessoClienteUsuario']['nome']);
            $this->set('clientes', $arr_clientes);
            $this->set('lista', $this->Destinatario->find('all', array('conditions'=>array('Destinatario.empresa_id'=>$this->Auth->User('empresa_id'), 'cliente'=>1))));
        } else {
            $this->Session->setFlash(__('Erro Interno'), 'mensagens/alerta');
        }
    }
    
    public function fluxos($id = null){
        $this->AcessoClienteUsuarioFluxoLogistico->recursive = 0;
        if (!is_null($id)){
            $fluxos = $this->AcessoClienteUsuarioFluxoLogistico->find('all', array('conditions'=>array('acesso_cliente_usuario_id'=>$id)));
            $arr_fluxos = array();
            foreach ($fluxos as $item){
                $arr_fluxos[$item['AcessoClienteUsuarioFluxoLogistico']['fluxo_logistico_id']] = $item['AcessoClienteUsuarioFluxoLogistico']['fluxo_logistico_id'];
            }
            $usuario = $this->AcessoClienteUsuario->find('first', array('conditions'=>array('AcessoClienteUsuario.id'=>$id)));
            $this->set('user_id', $usuario['AcessoClienteUsuario']['id']);
            $this->set('usuario', $usuario['AcessoClienteUsuario']['nome']);
            $this->set('fluxos', $arr_fluxos);
            $criterios = $this->restringir('FluxoLogistico');
            $lista = $this->FluxoLogistico->generateTreeList($criterios, NULL, NULL, '&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;');
            $this->set('lista', $lista);
        } else {
            $this->Session->setFlash(__('Erro Interno'), 'mensagens/alerta');
        }
    }
    
    public function marca_cliente($user_id, $cliente_id){
        $this->AcessoClienteUsuarioCliente->recursive = 0;
        $marcado = $this->AcessoClienteUsuarioCliente->find('all', array('conditions'=>array('acesso_cliente_usuario_id'=>$user_id, 'cliente_id'=>$cliente_id)));
        $total = count($marcado);
        if ($total == 0){
            $this->AcessoClienteUsuarioCliente->create();
            $this->AcessoClienteUsuarioCliente->save(array('acesso_cliente_usuario_id'=>$user_id, 'cliente_id'=>$cliente_id));
        } else {
            $this->AcessoClienteUsuarioCliente->id = $marcado[0]['AcessoClienteUsuarioCliente']['id'];
            $this->AcessoClienteUsuarioCliente->delete();
        }
    }
    
    public function marca_fluxo($user_id, $fluxo_logistico_id){
        $this->AcessoClienteUsuarioFluxoLogistico->recursive = 0;
        $marcado = $this->AcessoClienteUsuarioFluxoLogistico->find('all', array('conditions'=>array('acesso_cliente_usuario_id'=>$user_id, 'fluxo_logistico_id'=>$fluxo_logistico_id)));
        $total = count($marcado);
        if ($total == 0){
            $this->AcessoClienteUsuarioFluxoLogistico->create();
            $this->AcessoClienteUsuarioFluxoLogistico->save(array('acesso_cliente_usuario_id'=>$user_id, 'fluxo_logistico_id'=>$fluxo_logistico_id));
        } else {
            $this->AcessoClienteUsuarioFluxoLogistico->id = $marcado[0]['AcessoClienteUsuarioFluxoLogistico']['id'];
            $this->AcessoClienteUsuarioFluxoLogistico->delete();
        }
    }
    
}