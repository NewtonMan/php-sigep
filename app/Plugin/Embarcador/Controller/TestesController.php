<?php
class TestesController extends EmbarcadorAppController {
    
    public $uses = ['Embarcador.Encomenda'];
    
    public function index(){
        if ($this->request->is('post') || $this->request->is('put')){
            $encomenda = $this->Encomenda->importNFe($this->request->data['Encomenda']['arquivo']['tmp_name'], AuthComponent::User('empresa_id'));
            die(print_r($encomenda));
        }
    }
    
}