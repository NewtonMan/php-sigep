<?php
App::uses('Controller', 'Controller');
App::uses('AppController', 'Controller');
class ConfiguracoesController extends AppController {
    public $uses = array('EmpresaStyle', 'EmpresaUrl');
    
    public $cacheAction = array(
        //'css_style' => array('callbacks' => true, 'duration' => 14400),
    );
    
    public function css_style(){
        $this->response->type('css');
        $this->layout = 'css';
        $empresa_url = $this->EmpresaUrl->find('first',
            array(
                'conditions'=>array(
                    'url' => $_SERVER['HTTP_HOST'],
                )
            )
        );
        $this->request->data = $this->EmpresaStyle->find('first', array('conditions'=>array('empresa_id'=>$empresa_url['EmpresaUrl']['empresa_id'])));
    }
    
    public function index(){
        $empresa_style_id = null;
        $empresa_style = $this->EmpresaStyle->find('first', array('conditions'=>array('empresa_id'=>$this->Auth->User('empresa_id'))));
        if (isset($empresa_style['EmpresaStyle'])){
            $empresa_style_id = $empresa_style['EmpresaStyle']['id'];
        }
        if ($this->request->is('post') || $this->request->is('put')){
            $this->EmpresaUrl->deleteAll(array('empresa_id'=>$this->Auth->User('empresa_id')));
            
            $this->request->data['EmpresaStyle']['urls'] = trim($this->request->data['EmpresaStyle']['urls']);
            $urls = explode("\n", $this->request->data['EmpresaStyle']['urls']);
            foreach ($urls as $url){
                $url = trim($url);
                $this->EmpresaUrl->create();
                $this->EmpresaUrl->save(
                    array(
                        'empresa_id' => $this->Auth->User('empresa_id'),
                        'url' => $url,
                    )
                );
            }
            
            if (is_null($empresa_style_id)){
                $this->EmpresaStyle->create();
            } else {
                $this->EmpresaStyle->id = $empresa_style_id;
            }
            $this->request->data['EmpresaStyle']['empresa_id'] = $this->Auth->User('empresa_id');
            $this->request->data['EmpresaStyle']['logo_file'] = $this->uploadTopo($this->request->data['EmpresaStyle']['logo_file']);
            if (is_null($this->request->data['EmpresaStyle']['logo_file'])) unset($this->request->data['EmpresaStyle']['logo_file']);
            $this->EmpresaStyle->save($this->request->data);
            $this->Session->setFlash('Configurações alteradas!', 'mensagens/sucesso');
        }
        
        $str_urls = '';
        $urls = $this->EmpresaUrl->find('all', array('conditions'=>array('empresa_id'=>$this->Auth->User('empresa_id'))));
        foreach ($urls as $url){
            $str_urls .= "{$url['EmpresaUrl']['url']}\n";
        }
        $str_urls = trim($str_urls);
        $this->request->data = $this->EmpresaStyle->read(null, $empresa_style_id);
        $this->request->data['EmpresaStyle']['urls'] = $str_urls;
    }
    
    private function uploadTopo($foto){
        $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
	$filename = substr(hash('sha1', str_shuffle(microtime())), 0, 8).".{$ext}";
	$path = WWW_ROOT . DS . 'files';
        if (move_uploaded_file($foto['tmp_name'], $path.DS.$filename)) {
            return $filename;
        } else {
            return null;
        }
    }
}