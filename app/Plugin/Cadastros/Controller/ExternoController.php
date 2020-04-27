<?php
class ExternoController extends CadastrosAppController {
    
    public $uses = array('Cadastros.Destino', 'IbgeEstado', 'IbgeCidade', 'IbgeOptions');
    
    public $components = array('RequestHandler');
    
    public function cadastrar(){
        $this->request->data['Destino']['cpf_cnpj'] = onlyNumbers($this->request->data['Destino']['cpf_cnpj']);
        $cad = $this->Destino->find('first', array('conditions' => array(
            'Destino.cpf_cnpj' => $this->request->data['Destino']['cpf_cnpj'],
        )));
        if (empty($cad['Destino']['id'])){
            $this->Destino->create();
            if ($this->Destino->save($this->request->data, false)){
                $msg = 'Novo Cadastro Realizado';
                $cad = $this->Destino->read(null, $this->Destino->id);
            } else {
                $msg = 'Falha Durante Cadastro: Preencha todos os campos.';
                $cad = null;
            }
        } else {
            $msg = 'Cadastro já existente.';
            $this->Destino->id = $cad['Destino']['id'];
            $this->Destino->save($this->request->data);
        }
        $cad = to_utf8($cad);
        $msg = to_utf8($msg);
        $this->set(array(
            'msg' => $msg,
            'cadastro' => $cad,
            '_serialize' => array('msg','cadastro')
        ));
    }
    
}