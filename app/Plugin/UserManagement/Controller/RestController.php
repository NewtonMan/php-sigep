<?php
class RestController extends UserManagementAppController {

    public $uses = array('UserManagement.User', 'Eventos.Evento', 'Eventos.Equipe', 'Eventos.EventoEquipe', 'Eventos.EventoEquipeUser');
    
    public $components = array('RequestHandler');

    public function index(){
        $results = $this->paginate('User', $this->crudModelSearch('User'));
        $this->set([
            'crudSearch' => @$_GET['crudSearch'],
            'results' => $results,
            'paging' => $this->request->params['paging']['User'],
            '_serialize' => ['crudSearch', 'results', 'paging'],
        ]);
    }

    public function view($id){
        $this->set([
            'data' => $this->User->read(null, $id),
            '_serialize' => ['data'],
        ]);
    }

    public function add(){
        $errors = [];
        $id = 0;
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $id = $this->User->id;
            } else {
                $errors = $this->User->validationErrors;
            }
        } else {
            throw new MethodNotAllowedException();
        }
        $this->set([
            'id' => $id,
            'errors' => $errors,
            '_serialize' => ['id', 'errors'],
        ]);
    }

    public function edit($id) {
        $errors = [];
        if ($this->request->is('post') || $this->request->is('put') || $this->request->is('patch')) {
            $this->User->id = $id;
            if ($this->User->save($this->request->data)) {
                $id = $this->User->id;
            } else {
                $errors = $this->User->validationErrors;
            }
        } else {
            throw new MethodNotAllowedException();
        }
        $this->set([
            'id' => $id,
            'errors' => $errors,
            '_serialize' => ['id', 'errors'],
        ]);
    }

    public function delete($id){
        if ($this->request->is('delete')) {
            $this->User->id = $id;
            $this->User->delete($id);
        } else {
            throw new MethodNotAllowedException();
        }
        $this->set([
            'id' => $id,
            'errors' => [],
            '_serialize' => ['id', 'errors'],
        ]);
    }

}
