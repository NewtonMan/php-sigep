<?php
class UsuarioPermissaoController extends CadastrosAppController {
    public $uses = array('UsuarioPermissao', 'Usuario');
    
    public function index($usuario_id){
        $u = $this->Usuario->read(null, $usuario_id);
        if ($this->request->is('post')){
            $this->UsuarioPermissao->deleteAll(array('usuario_id'=>$usuario_id));
            foreach ($this->request->data as $index=>$kind){
                if ($kind!=1) continue;
                $info = explode('|', $index);
                list($controller, $action) = $info;
                $perm['usuario_id'] = $usuario_id;
                $perm['controller'] = $controller;
                $perm['action'] = $action;
                $perm['deny'] = 1;
                $this->UsuarioPermissao->create();
                $this->UsuarioPermissao->save($perm);
            }
            $this->Session->setFlash('Restrições Aplicadas.', 'mensagens/sucesso');
            $this->redirect($this->Session->read('refer'));
        }
        $bloqueios = $this->UsuarioPermissao->find('all', array('conditions'=>array('usuario_id'=>$usuario_id)));
        $bloqueados = array();
        foreach ($bloqueios as $up){
            $bloqueados[] = "{$up['UsuarioPermissao']['controller']}|{$up['UsuarioPermissao']['action']}";
        }
        $this->set('u', $u);
        $this->set('bloqueados', $bloqueados);
        $this->set('skell', $this->UsuarioPermissao->skell);
        $this->set('lista', $this->paginate('UsuarioPermissao', array('usuario_id'=>$usuario_id)));
    }
    
}