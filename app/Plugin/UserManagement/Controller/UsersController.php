<?php
class UsersController extends UserManagementAppController {

    public $uses = array('UserManagement.User');
    
    public $components = array('RequestHandler');

    public function beforeFilter(){
        parent::beforeFilter();
        $this->Auth->allow();
    }
    
    public function index(){
        $this->Session->write('user_menagement_list_refer_page', $_SERVER['REQUEST_URI']);
        $this->setRefer();
        $criterios = [];
        if (!empty($_GET['crudSearch'])){
            $sString = "%" . str_replace(['*', ' '], ['%', '%'], $_GET['crudSearch']) . "%";
            $criterios['OR'] = [
                'User.nome_completo LIKE ?' => $sString,
                'User.cargo LIKE ?' => $sString,
                'User.empregador LIKE ?' => $sString,
                'User.RG LIKE ?' => $sString,
                'User.email LIKE ?' => $sString,
                'User.telefone1 LIKE ?' => $sString,
                'User.telefone2 LIKE ?' => $sString,
            ];
        }
        if (!empty($_GET['equipe_id'])){
            $criterios['User.equipe_id'] = $_GET['equipe_id'];
        }
        $list = $this->paginate('User', $criterios);
        $this->set(compact('list'));
    }

    public function del($id){
        $this->User->id = $id;
        $this->User->delete($id);
        $this->getRefer();
    }

    public function exportar(){
        header('Expires: 0');
        header('Cache-control: private');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-disposition: attachment; filename="arquivo-exportado-'.date("d-m-Y_H-i-s").'.csv"');
        ob_implicit_flush(true);
        ob_end_flush();
        $list = $this->User->find('all');
        $this->set(compact('list'));
        $this->layout = 'ajax';
    }
    
    public function importar(){
        $this->layout = 'AdminLTE.clean';
        if ($this->request->is('post') || $this->request->is('put')){
            $importados = 0;
            $ext = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));
            if ($ext=='csv'){
                $filename = $_FILES['arquivo']['tmp_name'];
                $linhas = file($filename);
                $total = count($linhas);
                if ($total <= 1000) {
                    $evento_equipe_users = [];
                    foreach ($linhas as $linhas_pos => $linha_data){
                        if ($linhas_pos==0) continue;
                        $user_id = null;
                        $linha_numero = $linhas_pos+1;
                        $cols = explode(';', $linha_data);
                        foreach ($cols as $cx => $cv){
                            $cols[$cx] = utf8_encode(trim(str_replace(['"', '\'', "\t", "\r", "\n"], ['', '', '', '', ''], $cv)));
                        }
                        $user = [
                            'User' => [
                                'equipe_id' => $this->request->data['equipe_id'],
                                'nome_completo' => $cols[0],
                                'sexo' => $cols[1],
                                'CPF' => onlyNumbers($cols[2]),
                                'RG' => $cols[3],
                                'cargo' => $cols[4],
                                'empregador' => $cols[5],
                                'email' => $cols[6],
                                'telefone1' => onlyNumbers($cols[7]),
                                'telefone2' => onlyNumbers($cols[8]),
                                'active' =>  1,
                            ],
                        ];
                        foreach ($user['User'] as $uc => $uv){
                            if (empty($uv)){
                                unset($user['User'][$uc]);
                            }
                        }
                        $existe = $this->User->find('count', ['conditions' => [
                            'User.nome_completo' => $user['User']['nome_completo'],
                            'User.empregador' => $user['User']['empregador'],
                        ]]);
                        if ($existe==0){
                            $this->User->create();
                            if (!$this->User->save($user)){
                                $this->Session->setFlash("Linha {$linha_numero} não pode criar o Staff, preencha todos os dados.", 'mensagens/alerta');
                                continue;
                            } else {
                                $user_id = $this->User->id;
                                $importados++;
                            }
                        } else {
                            $staff = $this->User->find('first', ['conditions' => [
                                'User.nome_completo' => $user['User']['nome_completo'],
                                'User.empregador' => $user['User']['empregador'],
                            ]]);
                            $user['User']['id'] = $staff['User']['id'];
                            $this->User->id = $staff['User']['id'];
                            $this->User->save($user);
                            $user_id = $this->User->id;
                            $importados++;
                        }
                    }
                    $this->Session->setFlash('Usuários importados: ' . $importados, 'mensagens/sucesso');
                } else {
                    $this->Session->setFlash('ERRO: O sistema só processa arquivos com até 1000 linhas.', 'mensagens/alerta');
                }
            } else {
                $this->Session->setFlash('ERRO: Formato do arquivo incorreto, este sistema faz upload apenas em CSV.', 'mensagens/alerta');
            }
            $this->getRefer();
        } else {
            $this->setRefer();
        }
    }

    public function register(){
        //unset($this->User->validate);
        $errors = [];
        $uuid = 0;
        $this->layout = 'AdminLTE.clean';
        try {
            if ($this->request->is('post') || $this->request->is('put')) {
                $this->User->create();
                $this->request->data['User']['CPF'] = (int)onlyNumbers($this->request->data['User']['CPF']);
                $this->request->data['User']['telefone1'] = (int)onlyNumbers($this->request->data['User']['telefone1']);
                $this->request->data['User']['telefone2'] = (int)onlyNumbers($this->request->data['User']['telefone2']);
                if ($this->request->data['User']['CPF']==0) unset($this->request->data['User']['CPF']);
                if ($this->request->data['User']['telefone1']==0) unset($this->request->data['User']['telefone1']);
                if ($this->request->data['User']['telefone2']==0) unset($this->request->data['User']['telefone2']);
                $this->request->data['User']['active'] = 0;
                if ($this->User->save($this->request->data)) {
                    $msg = 'ok';
                    $uuid = $this->User->id;
                } else {
                    $msg = 'fail';
                    $verrors = $this->User->validationErrors;
                    $total = count($verrors);
                    if ($total>0) {
                        foreach ($verrors as $ec => $ems) {
                            foreach ($ems as $emm) {
                                $errors[] = $emm;
                            }
                        }
                        //$errors = "<li>" . implode('</li><li>', $errors) . "</li>";
                    }
                }
            } else {
                $msg = 'get';
            }
        } catch (Exception $ex) {
            $msg = 'exception';
        }
        $this->set([
            'msg' => $msg,
            'uuid' => $uuid,
            'errors' => $errors,
            '_serialize' => ['msg', 'errors', 'uuid'],
        ]);
        
    }

    public function form($mod, $id=null){
        $this->layout = 'AdminLTE.clean';
        if ($this->request->is('post') || $this->request->is('put')){
            if (empty($id)){
                $this->User->create();
            } else {
                $this->User->id = $id;
            }
            $this->request->data['User']['telefone1'] = onlyNumbers($this->request->data['User']['telefone1']);
            $this->request->data['User']['telefone2'] = onlyNumbers($this->request->data['User']['telefone2']);
            if (empty($this->request->data['User']['telefone1'])) unset($this->request->data['User']['telefone1']);
            if (empty($this->request->data['User']['telefone2'])) unset($this->request->data['User']['telefone2']);
            if (empty($this->request->data['User']['senha'])){
                unset($this->request->data['User']['senha']);
            }
            if ($this->User->save($this->request->data)){
                $this->Session->setFlash(__('User Data Saved.'));
                $refer = $this->Session->read('user_menagement_list_refer_page');
                return $this->redirect('/cracha/etiqueta/'.$this->User->id);
            } else {
                $this->Session->setFlash(__('ERROR: Check the User Form.'));
            }
        } elseif (!empty ($id)){
            $this->request->data = $this->User->read(null, $id);
            $this->request->data['User']['senha'] = '';
        }
        $this->render('form');
    }

    public function add(){
        $this->form('Add');
    }

    public function edit($id){
        $this->form('Edit', $id);
    }
    
    public function update_avatar(){
        $uid = $this->Auth->User('id');
        $m = onlyNumbers(microtime(true));
        $filename = "avatar-{$uid}-{$m}.jpeg";
        $tmp = WWW_ROOT . 'files' . DS . $filename;
        file_put_contents($tmp, base64_decode($this->request->data['avatar']));
        $img = new PQ_Image($tmp);
        $img->Open();
        if ($img->widthOriginal > $img->heightOriginal){
            $side = ($img->widthOriginal - $img->heightOriginal) / 2;
            $img->Crop($side, 0, ($side + $img->heightOriginal), $img->heightOriginal);
        } else {
            $side = ($img->heightOriginal - $img->widthOriginal) / 2;
            $img->Crop(0, $side, $img->widthOriginal, ($side + $img->widthOriginal));
        }
        $img->SaveAs();
        if ($img->widthOriginal>400 || $img->heightOriginal>400){
            $img->newWidth = 400;
            $img->newHeight = 400;
            $img->Resize();
        }
        $img->SaveAs(WWW_ROOT . 'files' . DS . $filename);
        
        $this->User->id = $uid;
        $this->User->saveField('avatar', $filename);
        $user = $this->User->read(null, $uid);
        $this->set([
            'user' => $user['User'],
            'msg' => 'ok',
            '_serialize' => ['user', 'msg'],
        ]);
    }
    
    public function api_update(){
        $msg = 'none';
        $uid = $this->Auth->User('id');
        $this->User->id = $uid;
        if ($this->User->save($this->request->data)){
            $msg = 'ok';
        } else {
            $msg = 'fail';
        }
        $user = $this->User->read(null, $uid);
        $this->set([
            'user' => $user['User'],
            'msg' => $msg,
            '_serialize' => ['user', 'msg'],
        ]);
    }
    
    public function sombrear($id){
        $u = $this->User->read(null, $id);
        $this->Auth->login($u);
        return $this->redirect('/');
    }
    
}
