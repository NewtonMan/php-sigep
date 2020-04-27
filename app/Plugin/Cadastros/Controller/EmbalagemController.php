<?php
class EmbalagemController extends CadastrosAppController {
    public $uses = array('Embalagem', 'Produto');
    
    public function index() {
        $this->Session->write('refer', $this->here);
        $this->Embalagem->recursive = -1;
        $criterios['produto_id'] = null;
        $this->set('titulo', 'Embalagens');
        $this->set('lista', $this->paginate('Embalagem', $criterios));
    }

    public function select_options() {
        $this->autoRender = false;
        $criterios = $this->restringir('Embalagem');
        $criterios['produto_id'] = null;
        $this->Embalagem->recursive = 0;
        $options = $this->Embalagem->find('list', array('conditions'=>$criterios));
        $out = '';
        foreach ($options as $id => $option){
            $out .= "<option value=\"{$id}\">{$option}</option>";
        }
        echo $out;
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Embalagem->create();
            $this->request->data['Embalagem']['empresa_id'] = $this->Auth->User('empresa_id');
            if ($this->Embalagem->save($this->request->data)) {
                $this->Session->setFlash(__('Embalagem Salva!'));
                $this->redirect($this->Session->read('refer'));
            } else {
                $this->Session->setFlash(__('Não foi possível processar sua solicitação, tente mais tarde.'));
            }
        }
        $this->set('opcoes_produtos', $this->Produto->find('list', array('conditions'=>array('empresa_id'=>$this->Auth->User('empresa_id')))));
        $this->render('form');
    }

    public function edit($id = null) {
        $this->Embalagem->id = $id;
        if (!$this->Embalagem->exists()) {
            throw new NotFoundException(__('Embalagem inválida!'));
        }
        $embalagem = $this->Embalagem->read(null, $id);
        if ($embalagem['Embalagem']['empresa_id']!=AuthComponent::User('empresa_id')) return $this->redirect('/embalagem');
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['Embalagem']['empresa_id'] = $this->Auth->User('empresa_id');
            if ($this->Embalagem->save($this->request->data)) {
                $this->Session->setFlash(__('Embalagem Salva!'));
                $this->redirect($this->Session->read('refer'));
            } else {
                $this->Session->setFlash(__('Não foi possível processar sua solicitação, tente mais tarde.'));
            }
        } else {
            $this->request->data = $embalagem;
            unset($this->request->data['Embalagem']['senha']);
        }
        $this->set('opcoes_produtos', $this->Produto->find('list', array('conditions'=>array('empresa_id'=>$this->Auth->User('empresa_id')))));
        $this->render('form');
    }

    public function delete($id = null) {
        $embalagem = $this->Embalagem->read(null, $id);
        if ($embalagem['Embalagem']['empresa_id']!=AuthComponent::User('empresa_id')) return $this->redirect('/embalagem');
        if ($this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Embalagem->id = $id;
        if (!$this->Embalagem->exists()) {
            throw new NotFoundException(__('Embalagem inválida!'));
        }
        if ($this->Embalagem->delete()) {
            $this->Session->setFlash(__('Embalagem Removida!'));
            $this->redirect($this->Session->read('refer'));
        }
        $this->Session->setFlash(__('A Embalagem não foi removida!'));
        $this->redirect($this->Session->read('refer'));
    }
}