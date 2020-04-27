<?php
App::uses('Controller', 'Controller');
class AppController extends Controller {
    
    public $layout = 'AdminLTE.default';
    
    public $components = array(
        'Session',
        'Auth' => array(
            'loginAction' => '/login',
            'loginRedirect' => '/',
            'authError' => 'Did you really think you are allowed to see that?',
            'authenticate' => array(
                'Basic' => array(
                    'userModel' => 'UserManagement.User',
                    'fields' => array(
                        'username' => 'email',
                        'password' => 'senha'
                    ),
                ),
                'JwtAuth.JwtToken' => array(
                    'fields' => array(
                        'username' => 'CPF',
                        'password' => 'senha',
                        //'token' => 'token',
                    ),
                    'parameter' => '_token',
                    'header' => 'Authorization',
                    'userModel' => 'UserManagement.User',
                ),
                'Form' => array(
                    'userModel' => 'UserManagement.User',
                    'fields' => array(
                        'username' => 'email',
                        'password' => 'senha'
                    ),
                ),
            ),
        ),
    );

    public $helpers = array(
        'Html' => array(
            'className' => 'Bootstrap3.BootstrapHtml'
        ),
        'Form' => array(
            'className' => 'Bootstrap3.BootstrapForm'
        ),
        'Modal' => array(
            'className' => 'Bootstrap3.BootstrapModal'
        ),
        'Paginator' => array(
            'className' => 'Bootstrap3.BootstrapPaginator'
        ),
    );

    public $paginate = array(
        'limit' => 10,
        'paramType' => 'querystring',
    );

    public $crumbs = [];
    
    public function beforeRender() {
        parent::beforeRender();
        $this->set('crumbs', $this->crumbs);
    }

    public function setRefer(){
        $this->Session->write('refer', $_SERVER['REQUEST_URI']);
    }

    public function getRefer(){
        return $this->redirect($this->Session->read('refer'));
    }

    public function crudModelSearch($model){
        $c = [];
        if (isset($_GET['crudSearch'])){
            $searchString = "%" . str_replace([' ', '*'], ['%', '%'], $_GET['crudSearch']) . "%";
            $searchInt = onlyNumbers($_GET['crudSearch']);
            foreach ($this->$model->schema() as $campo => $param){
                if ($param['type']=='string'){
                    $c["{$model}.{$campo} LIKE ?"] = $searchString;
                } elseif ($param['type']=='int' || $param['type']=='integer' || $param['type']=='biginteger'){
                    $c["{$model}.{$campo}"] = $searchInt;
                }
            }
            foreach ($this->$model->belongsTo as $modelb => $data){
                foreach ($this->$model->$modelb->schema() as $campo => $param){
                    if ($param['type']=='string'){
                        $c["{$modelb}.{$campo} LIKE ?"] = $searchString;
                    } elseif ($param['type']=='int' || $param['type']=='integer' || $param['type']=='biginteger'){
                        $c["{$modelb}.{$campo}"] = $searchInt;
                    }
                }
            }
            foreach ($this->$model->hasOne as $modelb => $data){
                foreach ($this->$model->$modelb->schema() as $campo => $param){
                    if ($param['type']=='string'){
                        $c["{$modelb}.{$campo} LIKE ?"] = $searchString;
                    } elseif ($param['type']=='int' || $param['type']=='integer' || $param['type']=='biginteger'){
                        $c["{$modelb}.{$campo}"] = $searchInt;
                    }
                }
            }
            $total = count($c);
            if ($total > 0){
                $c = ['OR' => $c];
            }
        }
        return $c;
    }

    public function crudFormAction($model, $id=null, $refer=true){
        if ($this->request->is('post') || $this->request->is('put')){
            if (!empty($id)){
                $this->$model->id = $id;
            } else {
                $this->$model->create();
            }
            if ($this->$model->save($this->request->data)){
                $this->Session->setFlash('Dados salvos!', 'AdminLTE.mensagens/sucesso');
                if ($refer) $this->getRefer();
            } else {
                $this->Session->setFlash('ERRO: Verifique o Formulário.', 'AdminLTE.mensagens/alerta');
            }
        } elseif (!empty($id)) {
            $this->request->data = $this->$model->read(null, $id);
        }
    }
    
}
