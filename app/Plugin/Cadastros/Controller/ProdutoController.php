<?php
require_once 'Image.php';
require_once 'Upload.php';

class ProdutoController extends CadastrosAppController {
    public $uses = array('Produto', 'ProdutoValorVenda', 'NotaFiscal', 'IbgeEstado', 'ProdutoCliente', 'ContadorBarra', 'ProdutoEstoqueMinimo', 'FluxoLogistico', 'ProdutoIcms', 'ProdutoIpi', 'Embalagem', 'Destinatario', 'NotaFiscalEletronica.ImpostoIcms', 'NotaFiscalEletronica.ImpostoPis', 'NotaFiscalEletronica.ImpostoCofins', 'NotaFiscalEletronica.ImpostoIpi');
    
    private function criteriosBusca(){
        $criterios = $this->restringir('Produto');
        $acesso_cliente_id = AuthComponent::User('acesso_cliente_id');
        if (!empty($acesso_cliente_id)){
            $criterios[] = "Produto.id IN (SELECT produto_id FROM produto_cliente WHERE cliente_id = $acesso_cliente_id)";
        }
        if (is_numeric(@$_GET['data']['cliente_id'])) $criterios['Produto.id IN (SELECT produto_id FROM produto_cliente WHERE cliente_id=?)'] = @$_GET['data']['cliente_id'];
        if (!empty($_GET['data']['nome'])){
            $palavra = trim($_GET['data']['nome']);
            $palavra = str_replace(' ', '%', $palavra);
            $criterios['OR'] = array(
                'Produto.id'=>$_GET['data']['nome'],
                'Produto.nome LIKE ?'=>"%{$palavra}%",
                'Produto.nome_abrev LIKE ?'=>"%{$palavra}%",
                'Produto.descricao LIKE ?'=>"%{$palavra}%",
                'Produto.nf_NCM LIKE ?'=>"%{$palavra}%",
                'Produto.codigo LIKE ?'=>"%{$palavra}%",
                'Produto.codigo_cliente LIKE ?'=>"%{$palavra}%",
                'Produto.codigo_barras LIKE ?'=>"%{$palavra}%",
            );
        }
        if (!empty($_GET['filtro'])) $criterios['Produto.id IN (SELECT produto_id FROM avisos WHERE aviso = ?)'] = 'produto_invalido';
        return $criterios;
    }
    
    public function index(){
        $this->setRefer();
        $criterios = $this->criteriosBusca();
        $clientes = $this->Destinatario->find('list',array('fields'=>array('Destinatario.id', 'Destinatario.fantasia'),'conditions'=>array('cliente'=>1),'order'=>'fantasia'));
        $clientes = array_unshift_assoc($clientes, '', '- qualquer -');
        $this->set('clientes', $clientes);
        $this->set('titulo', 'Produtos');
        $this->paginate = array(
            'limit' => 10,
        );
        $this->set('lista', $this->paginate('Produto', $criterios));
    }
    
    public function impostos_cliente(){
        if ($this->request->is('post') || $this->request->is('put')){
            $cliente_id = $this->request->data['Produto']['cliente_id'];
            unset($this->request->data['Produto']['cliente_id']);
            foreach ($this->request->data['Produto'] as $k => $v){
                $this->request->data['Produto'][$k] = (empty($v) ? null:$v);
            }
            $this->Produto->updateAll($this->request->data['Produto'], [
                'Produto.id IN (SELECT produto_id FROM produto_cliente WHERE cliente_id=?)'=>$cliente_id,
            ]);
            $this->Session->setFlash('Impostos Aplicados!', 'mensagens/informacao');
            $this->getRefer();
        }
        $clientes = $this->Destinatario->find('list', ['conditions' => [
            'Destinatario.cliente' => 1,
        ]]);
        $imposto_icms = $this->ImpostoIcms->find('list');
        $imposto_pis = $this->ImpostoPis->find('list');
        $imposto_cofins = $this->ImpostoCofins->find('list');
        $imposto_ipi = $this->ImpostoIpi->find('list');
        $this->set(compact('imposto_icms','imposto_pis','imposto_cofins','imposto_ipi','clientes'));
    }
    
    public function imprimir(){
        $this->layout = 'imprimir';
        $criterios = $this->criteriosBusca();
        $this->set('lista', $this->Produto->find('all', array('conditions'=>$criterios)));
    }
    
    public function add(){
        if ($this->request->is('post')) {
            $this->Produto->create();
            $this->request->data['Produto']['empresa_id'] = $this->Session->read('Auth.User.empresa_id');
            
            $error = false;
            $this->Produto->set($this->request->data['Produto']);
            if (!$this->Produto->validates()){
                $error = true;
                $problem['produto'] = true;
            }
            
            $this->Embalagem->set($this->request->data['Embalagem']);
            if (!$this->Embalagem->validates()){
                $error = true;
                $problem['embalagem'] = true;
            }
            
            if ($error==false){
                
                if ($this->Produto->save($this->request->data)) {

                    $this->request->data['ProdutoValorVenda']['produto_id'] = $this->Produto->id;
                    $this->ProdutoValorVenda->create();
                    $this->ProdutoValorVenda->save($this->request->data['ProdutoValorVenda']);

                    $this->request->data['Embalagem']['produto_id'] = $this->Produto->id;
                    $this->Embalagem->create();
                    $this->Embalagem->save($this->request->data['Embalagem']);

                    $this->request->data['ProdutoIcms']['produto_id'] = $this->Produto->id;
                    $this->ProdutoIcms->create();
                    $this->ProdutoIcms->save($this->request->data['ProdutoIcms']);

                    $this->request->data['ProdutoIpi']['produto_id'] = $this->Produto->id;
                    $this->ProdutoIpi->create();
                    $this->ProdutoIpi->save($this->request->data['ProdutoIpi']);

                    if (is_array($this->request->data['ProdutoCliente'])){
                        foreach($this->request->data['ProdutoCliente'] as $x => $cliente_id){
                            if ($this->request->data['ProdutoCliente'][$x]['cliente_id'] == 0) continue;
                            $this->ProdutoCliente->create();
                            $pc = array(
                                'produto_id' => $this->Produto->id,
                                'cliente_id' => $this->request->data['ProdutoCliente'][$x]['cliente_id'],
                            );
                            $this->ProdutoCliente->save($pc);
                        }
                    }
                    
                    if (is_array($this->request->data['ProdutoEstoqueMinimo'])){
                        foreach($this->request->data['ProdutoEstoqueMinimo'] as $fluxo_id => $em){
                            if (empty($this->request->data['ProdutoEstoqueMinimo'][$fluxo_id]['quantidade_minima'])) continue;
                            $this->request->data['ProdutoEstoqueMinimo'][$fluxo_id]['produto_id'] = $this->Produto->id;
                            $this->request->data['ProdutoEstoqueMinimo'][$fluxo_id]['fluxo_logistico_id'] = $fluxo_id;
                            $this->ProdutoEstoqueMinimo->create();
                            $this->ProdutoEstoqueMinimo->save($this->request->data['ProdutoEstoqueMinimo'][$fluxo_id]);
                        }
                    }
                    
                    $this->Session->setFlash(__('Produto Salvo!'), 'mensagens/sucesso');
                    $this->redirect($this->Session->read('refer'));
                } else {
                    $this->Session->setFlash('Ops, faltou alguma coisa, verifique o formulário.', 'mensagens/alerta');
                    $this->set('problem', $problem);
                }
            } else {
                $this->Session->setFlash('Ops, faltou alguma coisa, verifique o formulário.', 'mensagens/alerta');
                $this->set('problem', $problem);
            }
        } else {
            if (isset($_GET['data']['Produto']['codigo_barras'])){
                $produtos = $this->Produto->find('count', array('conditions'=>array('codigo_barras'=>$_GET['data']['Produto']['codigo_barras'])));
                if ($produtos>0){
                    $this->Session->setFlash('Este Código de Barras pertence a um produto já cadastrado.', 'mensagens/alerta');
                    $this->redirect('/produto');
                } else {
                    $this->request->data = getCosmosData($_GET['data']['Produto']['codigo_barras']);
                }
            }
            $this->request->data['Produto']['ativo'] = 'sim';
        }
        
        $estados = $this->IbgeEstado->find('list');
        $imposto_icms = $this->ImpostoIcms->find('list');
        $imposto_pis = $this->ImpostoPis->find('list');
        $imposto_cofins = $this->ImpostoCofins->find('list');
        $imposto_ipi = $this->ImpostoIpi->find('list');
        $this->set(compact('imposto_icms','estados','imposto_pis','imposto_cofins','imposto_ipi'));
        
        $this->set('fluxos', $this->FluxoLogistico->generateTreeList($this->restringir('FluxoLogistico'), NULL, NULL, '&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;'));
        $this->set('clientes', $this->Destinatario->find('list',array('fields'=>array('Destinatario.id', 'Destinatario.fantasia'),'conditions'=>array('cliente'=>1),'order'=>'fantasia')));
        $this->render('form_new');
    }
    
    public function edit($id = null) {
        $notas_pendente = $this->NotaFiscal->find('count', ['conditions' => [
            'NotaFiscal.movimento_id IN (SELECT DISTINCT movimento_id FROM movimento_material WHERE produto_id=?)' => $id,
            'NotaFiscal.nf_autorizada IS NULL AND nf_cancelada IS NULL AND nf_inutilizada IS NULL',
        ]]);
        if ($notas_pendente>0) {
            $this->Session->setFlash(__('Nota Fiscal Pendente, Não Pode Alterar Cadastro!'), 'mensagens/erro');
            return $this->getRefer();
        }
        $this->Produto->id = $id;
        if (!$this->Produto->exists()) {
            throw new NotFoundException(__('Produto inválido!'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['Produto']['empresa_id'] = $this->Session->read('Auth.User.empresa_id');
            
            $error = false;
            $this->Produto->set($this->request->data['Produto']);
            if (!$this->Produto->validates()){
                $error = true;
            }
            
            if ($error==false){
                if ($this->Produto->save($this->request->data)) {

                    $this->request->data['ProdutoValorVenda']['produto_id'] = $this->Produto->id;
                    $this->ProdutoValorVenda->create();
                    $this->ProdutoValorVenda->save($this->request->data['ProdutoValorVenda']);
                    
                    $this->Embalagem->deleteAll(array('Embalagem.produto_id'=>$id));
                    $this->request->data['Embalagem']['produto_id'] = $id;
                    $this->request->data['Embalagem']['empresa_id'] = AuthComponent::User('empresa_id');
                    $this->Embalagem->create();
                    $this->Embalagem->save($this->request->data['Embalagem']);
                    
                    $this->request->data['ProdutoIcms']['produto_id'] = $id;
                    $this->request->data['ProdutoIcms']['empresa_id'] = AuthComponent::User('empresa_id');
                    $this->ProdutoIcms->deleteAll(array('produto_id'=>$id));
                    $this->ProdutoIcms->create();
                    $this->ProdutoIcms->save($this->request->data['ProdutoIcms']);

                    $this->request->data['ProdutoIpi']['produto_id'] = $id;
                    $this->request->data['ProdutoIpi']['empresa_id'] = AuthComponent::User('empresa_id');
                    $this->ProdutoIpi->deleteAll(array('produto_id'=>$id));
                    $this->ProdutoIpi->create();
                    $this->ProdutoIpi->save($this->request->data['ProdutoIpi']);

                    $this->ProdutoCliente->deleteAll(array('ProdutoCliente.produto_id'=>$id));
                    if (is_array($this->request->data['ProdutoCliente'])){
                        foreach($this->request->data['ProdutoCliente'] as $x => $cliente_id){
                            if ($this->request->data['ProdutoCliente'][$x]['cliente_id'] == 0) continue;
                            $this->ProdutoCliente->create();
                            $pc = array(
                                'produto_id' => $id,
                                'cliente_id' => $this->request->data['ProdutoCliente'][$x]['cliente_id'],
                            );
                            $this->ProdutoCliente->save($pc);
                        }
                    }
                    
                    $this->ProdutoEstoqueMinimo->deleteAll(array('ProdutoEstoqueMinimo.produto_id'=>$id));
                    if (is_array($this->request->data['ProdutoEstoqueMinimo'])){
                        foreach($this->request->data['ProdutoEstoqueMinimo'] as $fluxo_id => $em){
                            if (empty($this->request->data['ProdutoEstoqueMinimo'][$fluxo_id]['quantidade_minima'])) continue;
                            $this->request->data['ProdutoEstoqueMinimo'][$fluxo_id]['produto_id'] = $id;
                            $this->request->data['ProdutoEstoqueMinimo'][$fluxo_id]['fluxo_logistico_id'] = $fluxo_id;
                            $this->ProdutoEstoqueMinimo->create();
                            $this->ProdutoEstoqueMinimo->save($this->request->data['ProdutoEstoqueMinimo'][$fluxo_id]);
                        }
                    }
                    
                    $this->Session->setFlash(__('Produto Salvo!'), 'mensagens/sucesso');
                    $this->redirect($this->Session->read('refer'));
                } else {
                    $this->Session->setFlash(__('Não foi possível processar sua solicitação, tente mais tarde.'), 'mensagens/alerta');
                }
            }
        } else {
            $this->request->data = $this->Produto->read(null, $id);
            $this->request->data['Embalagem']['largura_em_cm'] = FloatFromSQL($this->request->data['Embalagem']['largura_em_cm']);
            $this->request->data['Embalagem']['altura_em_cm'] = FloatFromSQL($this->request->data['Embalagem']['altura_em_cm']);
            $this->request->data['Embalagem']['profundidade_em_cm'] = FloatFromSQL($this->request->data['Embalagem']['profundidade_em_cm']);
            $this->request->data['ProdutoValorVenda']['valor_venda'] = FloatFromSQL($this->request->data['ProdutoValorVenda']['valor_venda']);
            $this->ProdutoIcms->recursive = 0;
            //$icms = $this->ProdutoIcms->read(null, $this->request->data['ProdutoIcms']['id']);
            //$this->request->data['ProdutoIcms'] = $icms['ProdutoIcms'];
            $clientes = $this->ProdutoCliente->find('list', array('fields'=>'cliente_id','conditions'=>array('produto_id'=>$id)));
            $this->request->data['ClientesChecked'] = $clientes;
            $this->set('foto', $this->request->data['Produto']['foto']);
            
            $estoque_minimo_db = $this->ProdutoEstoqueMinimo->find('all', array('conditions'=>array('produto_id'=>$id)));
            $estoque_minimo = array();
            foreach ($estoque_minimo_db as $emi){
                $estoque_minimo[$emi['ProdutoEstoqueMinimo']['fluxo_logistico_id']] = array(
                    'quantidade_minima' => $emi['ProdutoEstoqueMinimo']['quantidade_minima'],
                    'email' => $emi['ProdutoEstoqueMinimo']['email'],
                );
            }
            $this->request->data['ProdutoEstoqueMinimo'] = $estoque_minimo;
        }
        
        $estados = $this->IbgeEstado->find('list');
        $imposto_icms = $this->ImpostoIcms->find('list');
        $imposto_pis = $this->ImpostoPis->find('list');
        $imposto_cofins = $this->ImpostoCofins->find('list');
        $imposto_ipi = $this->ImpostoIpi->find('list');
        $this->set(compact('imposto_icms','estados','imposto_pis','imposto_cofins','imposto_ipi'));
        
        $this->set('fluxos', $this->FluxoLogistico->generateTreeList($this->restringir('FluxoLogistico'), NULL, NULL, '&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;'));
        $this->set('clientes', $this->Destinatario->find('list',array('fields'=>array('Destinatario.id', 'Destinatario.fantasia'),'conditions'=>array('cliente'=>1),'order'=>'fantasia')));
        $this->render('form_new');
    }
    
    public function delete($id = null){
        if ($this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Produto->id = $id;
        if (!$this->Produto->exists()) {
            throw new NotFoundException(__('Produto inválido!'));
        }
        try {
            $this->Produto->delete();
            $this->Session->setFlash(__('Produto Removido!'), 'mensagens/sucesso');
            $this->redirect($this->Session->read('refer'));
        } catch (PDOException $e) {
            $this->Session->setFlash(__('Não pode remover este item: ' . $e->getMessage()), 'mensagens/alerta');
            $this->redirect($this->Session->read('refer'));
        }
        $this->Session->setFlash(__('O Produto não foi removido!'), 'mensagens/alerta');
        $this->redirect($this->Session->read('refer'));
    }
    
    private function uploadFoto($foto){
        $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
	$filename = substr(hash('sha1', str_shuffle(microtime())), 0, 8).".{$ext}";
	$path = WWW_ROOT . DS . 'files' . DS . 'produtos';
        if (move_uploaded_file($foto['tmp_name'], $path.DS.$filename)) {
            $image = new PQ_Image($path . DS . $filename);
            $image->newWidth = 500;
            $image->newHeight = 500;
            $image->Resize();
            $image->SaveAs();
            $image->newWidth = 150;
            $image->newHeight = 150;
            $image->Resize();
            $image->SaveAs($path . DS . "p" . DS . $filename);
            return $filename;
        } else {
            die('Err 2');
            return null;
        }
    }
    
    public function lista_json($cliente = null){
        $criterios = $this->restringir('Produto');
        $this->layout = 'ajax';
        $produtos = $this->Produto->find(
            'all',
            array(
                'fields' => array(
                    'Produto.id',
                    'Produto.codigo_cliente',
                    'Produto.nome',
                    'Produto.descricao',
                ),
                'conditions'=>$criterios,
            )
        );
        $produtos = to_utf8($produtos);
        $this->set('array', $produtos);
        $this->render('to_json');
    }
    
    public function entrada_codigo_barras($barcode){
        $criterios = $this->restringir('Produto');
        $criterios['Produto.codigo_barras'] = $barcode;
        $this->layout = 'ajax';
        $produto = $this->Produto->find(
            'first',
            array(
                'fields' => array(
                    'Produto.id',
                    'Produto.codigo',
                    'Produto.nome',
                ),
                'conditions'=>$criterios,
            )
        );
        $produto = to_utf8($produto);
        $this->autoRender = false;
        if (!empty($produto['Produto']['id'])){
            echo "{$produto['Produto']['id']} - {$produto['Produto']['codigo']} - {$produto['Produto']['nome']}";
        } else {
            echo '';
        }
    }
    
    public function getJSONByBarCode($barcode){
        $criterios = $this->restringir('Produto');
        $criterios['OR'] = array(
            'Produto.codigo_barras' => $barcode,
            'Produto.id' => $barcode,
        );
        $this->layout = 'ajax';
        $this->response->type('application/json');
        $this->Produto->recursive = -1;
        $produto = $this->Produto->find('first', array('conditions'=>$criterios));
        if (empty($produto['Produto']['id'])){
            $barcode = substr($barcode, 0, (strlen($barcode) - 1));
            $contador = $this->ContadorBarra->find('first', array('conditions' => array(
                'ContadorBarra.id' => $barcode,
            )));
            $produto['Produto'] = $contador['Produto'];
        }
        $produto = to_utf8($produto);
        $this->set('array', $produto['Produto']);
        $this->render('to_json');
    }
    
    public function geraCodigoBarras($id){
        $p = $this->Produto->read(null, $id);
        $code = (empty($p['Produto']['codigo_barras']) ? (78000000000+(int)$id):$p['Produto']['codigo_barras']);
        $this->layout = 'codigo_barras';
        $this->set('filename', 'etiqueta-produto-'.$code.'.png');
        $this->set('code', $code);
    }
    
    public function detalhe($id){
        $produtos[] = to_utf8($this->Produto->read(null, $id));
        $produtos[0]['Produto']['foto'] = $this->requestAction('/np_photo_gallery/gallery/featured_path/'.$produtos[0]['Produto']['id'].'/100');
        $this->set('array', $produtos);
        $this->layout = 'ajax';
        $this->render('to_json');
    }
    
    public function importar_up(){
        $this->Produto->recursive = -1;
        $linhas = file(WWW_ROOT . 'files' . DS . 'upjob.csv');
        foreach ($linhas as $linha){
            $linha = trim($linha);
            $colunas = explode(';', $linha);
            $colunas[0] = strtoupper(str_replace('-', '', $colunas[0]));
            
            $produto = array(
                'empresa_id' => AuthComponent::User('empresa_id'),
                'nome' => $colunas[2] . ' SN ' . $colunas[0],
                'nome_abrev' => substr($colunas[2], 0, 20),
                'codigo_cliente' => $colunas[1],
                'codigo_barras' => $colunas[0],
                'descricao' => "{$colunas[2]} REGISTRADA COM CÓDIGO {$colunas[1]} E COM ETIQUETA SERIAL {$colunas[0]}",
                'unidade_comercial' => 'un',
                'nf_NCM' => '123123',
                'CFOP_interno' => '5949',
                'CFOP_externo' => '6949',
                'ativo' => 'sim',
            );
            
            $achou = $this->Produto->find('count', array('conditions'=>$produto));
            if ($achou>0){
                echo "JÁ CADASTRADO<br/>";
                $p = $this->Produto->find('first', array('conditions'=>$produto));
                $produto_cliente = array(
                    'produto_id' => $p['Produto']['id'],
                    'cliente_id' => 22629,
                );
                $this->ProdutoCliente->create();
                $this->ProdutoCliente->save($produto_cliente);
            } else {
                $this->Produto->create();
                if ($this->Produto->save($produto)){
                    echo "CADASTRADO COM SUCESSO<br/>";
                } else {
                    echo "CADASTRADO COM ERRO<br/>";
                }
            }
            
            //print_r($produto);
        }
    }
    
    public function duplicidade(){
        if (isset($_GET['corrigir'])){
            $produtos = $this->Produto->find('all', array(
                'fields' => array(
                    'Produto.id',
                    'Produto.codigo_cliente',
                    'Produto.nome',
                    'COUNT(*) as Produto__contagem',
                ),
                'conditions' => array(
                    'Produto.id IN (SELECT produto_id FROM produto_cliente WHERE cliente_id=?)' => $_GET['cliente_id'],
                ),
                'group' => array(
                    'Produto.codigo_cliente HAVING COUNT(Produto.codigo_cliente) > 1'
                ),
            ));
            foreach ($produtos as $p){
                $this->Produto->query("CALL SP_PRODUTO_UNIFICAR_POR_CODIGO('{$p['Produto']['codigo_cliente']}', '{$_GET['cliente_id']}')");
            }
            return $this->redirect('/cadastros/produto/duplicidade?cliente_id=' . $_GET['cliente_id']);
        } elseif (isset($_GET['cliente_id'])) {
            $cliente = $this->Destinatario->read(null, $_GET['cliente_id']);
            $produtos = $this->Produto->find('all', array(
                'fields' => array(
                    'Produto.id',
                    'Produto.codigo_cliente',
                    'Produto.nome',
                    'COUNT(*) as Produto__contagem',
                ),
                'conditions' => array(
                    'Produto.id IN (SELECT produto_id FROM produto_cliente WHERE cliente_id=?)' => $_GET['cliente_id'],
                ),
                'group' => array(
                    'Produto.codigo_cliente HAVING COUNT(Produto.codigo_cliente) > 1'
                ),
            ));
            $this->set(compact('produtos','cliente'));
            $this->render('duplicidade_produtos');
        } else {
            $clientes = $this->Destinatario->find('all', array('conditions' => array(
                'Destinatario.cliente' => 1,
            )));
            $this->set(compact('clientes'));
            $this->render('duplicidade_clientes');
        }
    }
    
    public function valores(){
        $cliente_id = 1226;
        $this->autoRender = false;
        $filename = WWW_ROOT . 'files' . DS . 'axt.csv';
        $linhas = explode("\r", file_get_contents($filename));
        foreach ($linhas as $x => $linha){
            if ($x==0) continue;
            $pos = $x+1;
            $linha = trim($linha);
            $colunas = explode(";", $linha);
            $produto = $this->Produto->find('first', array('conditions' => array(
                '(Produto.codigo_cliente LIKE ? OR Produto.codigo_cliente LIKE ?)' => array($colunas[0], addLeading($colunas[0], 6)),
                'Produto.id IN (SELECT produto_id FROM produto_cliente WHERE cliente_id = ?)' => $cliente_id,
            )));
            $produto_id = @$produto['Produto']['id'];
            if (empty($produto_id)){
                echo "Linha {$pos} = Produto não identificado pelo Código de Cliente<br/>";
            } else {
                $preco_unitario = FloatToSQL(trim(str_replace('R$', '', $colunas[1])));
                $volume_id = trim($colunas[2]);
                $this->Produto->query("CALL SP_ESTOQUE_ACERTA_VALOR_PRODUTO({$cliente_id}, {$produto_id}, {$preco_unitario}, {$volume_id})");
            }
        }
    }
    
}