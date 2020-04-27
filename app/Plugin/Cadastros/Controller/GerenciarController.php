<?php
class GerenciarController extends CadastrosAppController {
    
    public $uses = array('Cadastros.Destino', 'IbgeEstado', 'IbgeCidade', 'IbgeOptions');
    
    public $components = array('RequestHandler');
    
    public $campos = array(
        'oficina',
        'transportador',
        'transportador_agente',
        'cliente',
        'terceirizado',
        'armazem',
        'cia_aerea',
        'fornecedor',
    );
    
    public function logo($id){
        $this->layout = 'popup';
        if ($this->request->is('post') || $this->request->is('put')){
            if ($this->request->data['Destino']['logo']['error']==UPLOAD_ERR_OK){
                $ext = strtolower(pathinfo($this->request->data['Destino']['logo']['name'], PATHINFO_EXTENSION));
                if ($ext == 'jpg' || $ext == 'jpeg'){
                    $filename = WWW_ROOT . 'files' . DS . 'logo-' . $id . '.jpg';
                    @unlink($filename);
                    move_uploaded_file($this->request->data['Destino']['logo']['tmp_name'], $filename);
                    $this->Session->setFlash('OK: Logo atualizado.', 'mensagens/sucesso');
                } else {
                    $this->Session->setFlash('ERRO: Logo deve estar em JPG.', 'mensagens/alerta');
                }
            } else {
                $this->Session->setFlash('ERRO: Logo deve estar em JPG.', 'mensagens/alerta');
            }
        }
        $this->set(compact('id'));
    }
    
    private function lista($titulo, $marcar = null){
        $this->setRefer();
        $criterios = array();
        if (!empty($marcar)){
            $ms = explode(',', $marcar);
            foreach ($ms as $m){
                $criterios["Destino.{$m}"] = 1;
            }
        }
        $lista = $this->paginate('Destino',$criterios);
        $title_for_layout = $titulo;
        
        $this->set(compact('lista','titulo','title_for_layout','marcar'));
        $this->render('index');
    }
    
    public function consulta(){
        $destinos = $this->Destino->find('all');
        $this->set(array(
            'data' => to_utf8($destinos),
            '_serialize' => ['data'],
        ));
    }
    
    public function index(){
        $titulo = 'Cadastro Geral de Empresas / Pessoas Fisicas';
        $this->lista($titulo);
    }
    
    public function emissor_nfe(){
        $titulo = 'Cadastro de Emissores de NF-e';
        $this->lista($titulo, 'emitente_nfe');
    }
    
    public function emissor_cte(){
        $titulo = 'Cadastro de Emissores de CT-e';
        $this->lista($titulo, 'emissor_cte');
    }
    
    public function filiais_transporte(){
        $titulo = 'Cadastro de Filiais de Transporte';
        $this->lista($titulo, 'transportador');
    }
    
    public function agentes(){
        $titulo = 'Cadastro de Recebedores / Agentes';
        $this->lista($titulo, 'transportador_agente');
    }
    
    public function transportador(){
        $titulo = 'Cadastro Geral de Transportadoras / Agentes';
        $this->lista($titulo, 'transportador');
    }
    
    public function cia_aerea(){
        $titulo = 'Cadastro de Cias A�reas';
        $this->lista($titulo, 'cia_aerea');
    }
    
    public function armazem(){
        $titulo = 'Cadastro de Armaz�ns';
        $this->lista($titulo, 'armazem');
    }
    
    public function oficina(){
        $titulo = 'Cadastro de Oficinas e Autorizadas';
        $this->lista($titulo, 'oficina');
    }
    
    public function fornecedor(){
        $titulo = 'Cadastro de Fornecedores';
        $this->lista($titulo, 'fornecedor');
    }
    
    public function cliente(){
        $titulo = 'Cadastro de Clientes / Embarcadores';
        $this->lista($titulo, 'cliente');
    }
    
    public function icms($operacao, $id){
        if ($operacao=='ativar'){
            $this->Destino->ICMS_Nao_Cobrar($id);
        } else {
            $this->Destino->ICMS_Cobrar($id);
        }
        return $this->redirect('/cadastros/gerenciar/edit/'.$id);
    }
    
    public function add($marcar=null){
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['Destino']['cpf_cnpj'] = (int)onlyNumbers($this->request->data['Destino']['cpf_cnpj']);
            if (isCPF("{$this->request->data['Destino']['cpf_cnpj']}") || isCNPJ("{$this->request->data['Destino']['cpf_cnpj']}")){
                //$this->request->data['Destino']['cpf_cnpj'] = onlyNumbers($this->request->data['Destino']['cpf_cnpj']);
                $this->request->data['Destino']['fantasia'] = '';
                $this->request->data['Destino']['nome_razao'] = '';
                $this->request->data['Destino']['terceirizado'] = 0;
                $cri = [
                    'Destino.cpf_cnpj' => $this->request->data['Destino']['cpf_cnpj'],
                ];
                $destino = $this->Destino->find('first', array('conditions' => $cri));
                if (!$destino){
                    $this->Destino->create();
                    $this->Destino->save($this->request->data, false);
                } else {
                    $this->Destino->id = $destino['Destino']['id'];
                }
                if (@$_GET['reply']=='js'){
                    if (!empty($marcar)){
                        $destino = $this->Destino->read(null, $this->Destino->id);
                        $igual = true;
                        foreach ($ms as $m){
                            if ($destino['Destino'][$m]!=1) $igual = false;
                        }
                        if (!$igual) $this->Destino->id = 0;
                    }
                    return $this->redirect('/cadastros/gerenciar/view/'.$this->Destino->id.'.json');
                } else {
                    return $this->redirect('/cadastros/gerenciar/edit/'.$this->Destino->id.'/'.$marcar);
                }
            } else {
                if (@$_GET['reply']=='js'){
                    return $this->redirect('/cadastros/gerenciar/view/0.json');
                } else {
                    $this->Session->setFlash('O n�mero do documento n�o � v�lido.', 'mensagens/alerta');
                    $this->getRefer();
                }
            }
        }
    }
    
    public function view($id){
        $this->Destino->recursive = -1;
        $d = $this->Destino->read(null, $id);
        $this->set(array(
            'data' => to_utf8($d),
            '_serialize' => array('data')
        ));
    }
    
    public function source(){
        $this->Destino->recursive = -1;
        $expanded = "%" . str_replace(' ', '%', $_GET['term']) . "%";
        $criterios = array(
            "Destino.fantasia LIKE '{$expanded}'",
            "Destino.nome_razao LIKE '{$expanded}'",
            "Destino.endereco LIKE '{$expanded}'",
            "Destino.bairro LIKE '{$expanded}'",
            "Destino.municipio LIKE '{$expanded}'",
            "Destino.uf LIKE '{$expanded}'",
            "Destino.cep LIKE '{$expanded}'",
        );
        $ns = onlyNumbers($_GET["term"]);
        if (!empty($ns)){
            $criterios[] = "ABS(ONLY_NUMBERS(Destino.cpf_cnpj)) = ABS(".onlyNumbers($_GET["term"]).")";
            $criterios[] = "ABS(ONLY_NUMBERS(Destino.rg_insc_estadual)) = ABS(".onlyNumbers($_GET["term"]).")";
            $criterios[] = "ONLY_NUMBERS(Destino.cpf_cnpj) LIKE '%" . onlyNumbers($_GET["term"]) . "%'";
            $criterios[] = "ONLY_NUMBERS(Destino.rg_insc_estadual) LIKE '%" . onlyNumbers($_GET["term"]) . "%'";
        }
        $criterios_ok = "(".implode(') OR (', $criterios).")";
        $lista = $this->Destino->find('all', array('conditions' => array($criterios_ok)));
        $this->autoRender = false;
        $lista = to_utf8($lista);
        echo json_encode($lista, JSON_FORCE_OBJECT);
    }
    
    public function edit($id, $marcar=null){
        $this->Destino->id = $id;
        $cidades = array();
        foreach ($this->IbgeEstado->find('all') as $x => $estado){
            $uf = $estado['IbgeEstado']['sigla'];
            $cidades[$x] = array(
                'uf' => $uf,
                'cidades' => array(),
            );
            foreach ($this->IbgeCidade->find('all', array('conditions' => array('IbgeCidade.id_uf' => $estado['IbgeEstado']['id']))) as $cidade){
                $cidades[$x]['cidades'][] = array(
                    'id' => $cidade['IbgeCidade']['id'],
                    'nome' => $cidade['IbgeCidade']['nome'],
                );
            }
        }
        $this->set('cidades', $cidades);
        $this->set('estados', $this->IbgeEstado->ufList());
        
        $emitentes = $this->Destino->find('list', array('conditions' => array(
            'Destino.emitente_nfe' => 1,
        )));
        $this->set('emitentes', $emitentes);
        
        if ($this->request->is('post') || $this->request->is('put')){
            unset($this->request->data['Destino']['cpf_cnpj']);
            if ($this->Destino->save($this->request->data)){
                if (@$_GET['mode']=='modal'){
                    if (empty($_GET['callback'])){
                        $_GET['callback'] = 'window.close();';
                    }
                    die('<script>'.@$_GET['callback'].'</script>');
                } else {
                    $this->getRefer();
                }
            } else {
                $this->Session->setFlash('Preencha todos os campos corretamente.', 'mensagens/alerta');
                $destino = $this->Destino->read(null, $id);
                $this->request->data['Destino']['cpf_cnpj'] = $destino['Destino']['cpf_cnpj'];
            }
        } else {
            $this->request->data = $this->Destino->read(null, $id);
            if (!empty($marcar)){
                $ms = explode(',', $marcar);
                foreach ($ms as $m){
                    if (in_array($m, $this->campos)){
                        $this->Destino->saveField($m, 1);
                    }
                }
            }
        }
    }
    
    public function importar_emissor_cliente($arquivo, $marcar = ''){
        $cadastrados = 0;
        $valido = false;
        $linhas = file($arquivo);
        if (substr($linhas[0], 0, 8)=='CLIENTE|') $valido = true;
        if ($valido){
            foreach ($linhas as $l){
                $col = explode('|', $l);
                if ($col[0]=='E'){
                    $destino = array(
                        'empresa_id'        => AuthComponent::User('empresa_id'),
                        'fantasia'          => $col[3],
                        'nome_razao'        => $col[3],
                        'cpf_cnpj'          => onlyNumbers($col[2]),
                        'rg_insc_estadual'  => onlyNumbers($col[4]),
                        'endereco'          => $col[6],
                        'numero'            => $col[7],
                        'complemento'       => $col[8],
                        'bairro'            => $col[9],
                        'ibge_cidade_id'    => $col[10],
                        'ibge_estado_id'    => substr($col[10], 0, 2),
                        'municipio'         => $col[11],
                        'uf'                => $col[12],
                        'cep'               => $col[13],
                        'telefones'         => $col[16],
                        'email'             => $col[17],
                    );
                    $dest = array(
                        'Destino.cpf_cnpj' => (int)onlyNumbers($destino['cpf_cnpj']),
                    );
                    $total = $this->Destino->find('count', array(
                        'conditions' => $dest
                    ));
                    $destino_id = 0;
                    if ($total==0){
                        $this->Destino->create();
                        $this->Destino->save($destino, false);
                        $destino_id = $this->Destino->id;
                        $cadastrados++;
                    } else {
                        $dest = $this->Destino->find('first', array(
                            'conditions' => $dest
                        ));
                        $this->Destino->id = $dest['Destinatario']['id'];
                        unset($destino['cpf_cnpj']);
                        $this->Destino->save($destino);
                        $destino_id = $dest['Destinatario']['id'];
                        $cadastrados++;
                    }
                    if (!empty($marcar)){
                        $ms = explode(',', $marcar);
                        $this->Destino->id = $destino_id;
                        foreach ($ms as $m){
                            if (in_array($m, $this->campos)){
                                $this->Destino->saveField($m, 1);
                            }
                        }
                    }
                }
            }
        }
        return $cadastrados;
    }
    
    public function importar($marcar = ''){
        if ($this->request->is('post')){
            $filename = $this->upload($this->request->data['Destino']['arquivo']);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $name = $this->request->data['Destino']['arquivo']['name'];
            if ($name=='CLIENTE.txt'){
                $cadastrados = $this->importar_emissor_cliente($filename, $marcar);
                $this->Session->setFlash("Arquivo Processado, {$cadastrados} cadastrados novos.", 'mensagens/sucesso');
            } elseif ($extension!='xlsx'){
                $this->Session->setFlash('Erro: S�o aceitos apenas arquivos XLSX.', 'mensagens/alerta');
            } else {
                $cadastrados = 0;
                $existentes = 0;
                
                $reader = PHPExcel_IOFactory::createReader('Excel2007');
                $reader->setReadDataOnly(true);
                $obj = $reader->load($filename);
                $writer = PHPExcel_IOFactory::createWriter($obj, 'CSV');
                $writer->setDelimiter(';');
                $writer->save(str_replace('.xlsx', '.csv', $filename));
                //die(str_replace('.xlsx', '.csv', $filename));
                
                $csv = file(str_replace('.xlsx', '.csv', $filename));
                foreach ($csv as $linha_pos => $linha_data){
                    if ($linha_pos==0) continue;
                    $linha_data = utf8_decode($linha_data);
                    $linha_data = substr($linha_data, 1, (strlen($linha_data) - 1));
                    $colunas = explode('";"', $linha_data);
                    if (empty($colunas[0]) && empty($colunas[1]) && empty($colunas[2])) continue;
                    if (!isset($colunas[11])) continue;
                    $destino = array(
                        'empresa_id'        => AuthComponent::User('empresa_id'),
                        'fantasia'          => $colunas[0],
                        'nome_razao'        => $colunas[1],
                        'cpf_cnpj'          => (int)onlyNumbers($colunas[2]),
                        'rg_insc_estadual'  => $colunas[3],
                        'cep'               => $colunas[4],
                        'endereco'          => $colunas[5],
                        'numero'            => $colunas[6],
                        'complemento'       => $colunas[7],
                        'bairro'            => $colunas[8],
                        'municipio'         => $colunas[10],
                        'uf'                => $colunas[9],
                        'telefones'         => $colunas[11],
                        'codigo_cliente'    => str_replace('"', '', $colunas[13]),
                        'dados_bancarios'   => str_replace('"', '', $colunas[14]),
                    );
                    if (empty($destino['cpf_cnpj'])) continue;
                    $destino['codigo_cliente']  = trim($destino['codigo_cliente']);
                    $destino['dados_bancarios'] = trim($destino['dados_bancarios']);
                    
                    $dest = array(
                        'Destino.cpf_cnpj' => $destino['cpf_cnpj'],
                    );
                    $total = $this->Destino->find('count', array(
                        'conditions' => $dest
                    ));
                    $destino_id = 0;
                    if ($total==0){
                        $this->Destino->create();
                        $this->Destino->save($destino, false);
                        $destino_id = $this->Destino->id;
                        $cadastrados++;
                    } else {
                        $dest = $this->Destino->find('first', array(
                            'conditions' => $dest
                        ));
                        $this->Destino->id = $dest['Destino']['id'];
                        unset($destino['cpf_cnpj']);
                        $this->Destino->save($destino);
                        $destino_id = $dest['Destino']['id'];
                        $existentes++;
                    }
                    if (!empty($marcar)){
                        $ms = explode(',', $marcar);
                        $this->Destino->id = $destino_id;
                        foreach ($ms as $m){
                            if (in_array($m, $this->campos)){
                                $this->Destino->saveField($m, 1);
                            }
                        }
                    }
                }
                $this->Session->setFlash("Planilha processada, {$cadastrados} cadastrados e {$existentes} j� existiam.", 'mensagens/sucesso');
            }
        }
    }
    
    private function upload($file){
        $name = onlyNumbers(microtime()) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        if (move_uploaded_file($file['tmp_name'], WWW_ROOT . 'planilhas' . DS . $name)){
            $file['name'] = WWW_ROOT . 'planilhas' . DS . $name;
        } else {
            $file['name'] = 'error';
        }
        return $file['name'];
    }
    
}