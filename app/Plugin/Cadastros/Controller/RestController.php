<?php
class RestController extends CadastrosAppController {
    
    public $uses = ['Cadastros.Destino'];
    
    public $components = ['RequestHandler'];
    
    public $paginate = [
        'paramType' => 'querystring',
    ];
    
    public function read(){
        $id = $_GET['value'];
        $data = $this->Destino->read(null, $id);
        $this->set([
            'data' => to_utf8($data),
            '_serialize' => ['data'],
        ]);
    }
    
    public function list(){
        $criterios = [];
        if (isset($_GET['term'])){
            $str = '%' . str_replace(' ', '%', $_GET['term']) . '%';
            $int = (int)onlyNumbers($_GET['term']);
            if ($int>0){
                $criterios[]['OR'] = [
                    'Destino.cpf_cnpj' => $int,
                ];
            } else {
                $criterios[]['OR'] = [
                    'Destino.fantasia LIKE ?' => $str,
                    'Destino.nome_razao LIKE ?' => $str,
                    'Destino.rg_insc_estadual LIKE ?' => $str,
                    'Destino.endereco LIKE ?' => $str,
                    'Destino.municipio LIKE ?' => $str,
                    'Destino.uf LIKE ?' => $str,
                    'Destino.cep LIKE ?' => $str,
                ];
            }
        }
        $data = to_utf8($this->paginate('Destino', $criterios));
        $this->set([
            'data' => $data,
            'paginator' => $this->request->params['paging']['Destino'],
            '_serialize' => ['data', 'paginator'],
        ]);
    }
    
}