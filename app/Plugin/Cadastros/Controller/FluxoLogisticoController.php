<?php
class FluxoLogisticoController extends CadastrosAppController {
    public $uses = array('Movimento', 'FluxoLogistico', 'MovimentoHistorico', 'Destinatario', 'Integracao','PedidoTransporte','PedidoTransporteStatus','PedidoTransporteHistorico');
    
    public function index(){
        $this->Session->write('refer', $this->here);
        $criterios = array();
        $this->FluxoLogistico->recursive = 2;
        $this->set('titulo', 'Fluxo Logístico');
        $this->set('fluxo', $this->FluxoLogistico);
        $this->set('lista', $this->FluxoLogistico->generateTreeList($criterios, NULL, NULL, '&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;'));
    }
    
    public function add() {
        if ($this->request->is('post')) {
            $this->FluxoLogistico->create();
            $this->request->data['FluxoLogistico']['empresa_id'] = $this->Session->read('Auth.User.empresa_id');
            if ($this->FluxoLogistico->save($this->request->data)) {
                $this->Session->setFlash(__('Fluxo Logístico Salvo!'), 'mensagens/sucesso');
                $this->redirect($this->Session->read('refer'));
            } else {
                $this->Session->setFlash(__('Não foi possível processar sua solicitação, tente mais tarde.'), 'mensagens/alerta');
            }
        }
        $criterios = $this->restringir('FluxoLogistico');
        $this->set('fluxos', $this->FluxoLogistico->generateTreeList($criterios, NULL, NULL, '---> '));
        $criterios = $this->restringir('Destinatario');
        $criterios['cliente'] = 1;
        $clientes = $this->Destinatario->find('list', array('conditions'=>$criterios, 'fields'=>array('Destinatario.id', 'Destinatario.fantasia')));
        $clientes_keys = array_keys($clientes);
        $clientes_vals = array_values($clientes);
        array_unshift($clientes_keys, '');
        array_unshift($clientes_vals, '- selecione o cliente -');
        $clientes = array_combine($clientes_keys, $clientes_vals);
        $this->set('clientes', $clientes);
        $this->render('form');
    }

    public function edit($id = null) {
        $this->FluxoLogistico->id = $id;
        if (!$this->FluxoLogistico->exists()){
            throw new NotFoundException(__('Fluxo Logístico inválido!'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['FluxoLogistico']['empresa_id'] = $this->Session->read('Auth.User.empresa_id');
            if ($this->FluxoLogistico->save($this->request->data)) {
                $this->Session->setFlash(__('Fluxo Logístico Salvo!'), 'mensagens/sucesso');
                $this->redirect($this->Session->read('refer'));
            } else {
                $this->Session->setFlash(__('Não foi possível processar sua solicitação, tente mais tarde.'), 'mensagens/alerta');
            }
        } else {
            $this->request->data = $this->FluxoLogistico->read(null, $id);
        }
        $criterios = $this->restringir('FluxoLogistico');
        $this->set('fluxos', $this->FluxoLogistico->generateTreeList($criterios, NULL, NULL, '---> '));
        $criterios = $this->restringir('Destinatario');
        $criterios['cliente'] = 1;
        $clientes = $this->Destinatario->find('list', array('conditions'=>$criterios, 'fields'=>array('Destinatario.id', 'Destinatario.fantasia')));
        $clientes_keys = array_keys($clientes);
        $clientes_vals = array_values($clientes);
        array_unshift($clientes_keys, '');
        array_unshift($clientes_vals, '- selecione o cliente -');
        $clientes = array_combine($clientes_keys, $clientes_vals);
        $this->set('clientes', $clientes);
        $this->render('form');
    }

    public function delete($id = null) {
        if ($this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->FluxoLogistico->id = $id;
        if (!$this->FluxoLogistico->exists()) {
            throw new NotFoundException(__('Fluxo Logístico inválido!'));
        }
        try {
            $this->FluxoLogistico->delete();
            $this->Session->setFlash(__('Fluxo Logístico Removido!'), 'mensagens/sucesso');
            $this->redirect($this->Session->read('refer'));
        } catch (PDOException $e) {
            $this->Session->setFlash(__('Não pode remover este item pois ele já foi utilizado em um movimento!'), 'mensagens/alerta');
            $this->redirect($this->Session->read('refer'));
        }
        $this->Session->setFlash(__('O usuário não foi removido!'), 'mensagens/alerta');
        $this->redirect($this->Session->read('refer'));
    }
    
    public function pdas($id){
        $this->set('titulo', 'Destinos e PDAs Envolvidos');
        $user = $this->Session->read("Auth.User");
        $criterios = array('Destinatario.empresa_id'=>$this->Auth->User('empresa_id'));
        $criterios['Destinatario.id IN (SELECT destino_id FROM movimento WHERE fluxo_logistico_id=?)'] = array($id);
        if (isset($_GET['uf']) && !empty($_GET['uf'])) $criterios['uf'] = $_GET['uf'];
        if (isset($_GET['c']) && !empty($_GET['c'])) $criterios['Destinatario.municipio LIKE'] = "%{$_GET['c']}%";
        if (isset($_GET['w']) && !empty($_GET['w'])) {
            $criterios['AND'] = array(
                'OR' => array(
                    'Destinatario.fantasia LIKE' => "%{$_GET['w']}%",
                    'Destinatario.nome_razao LIKE' => "%{$_GET['w']}%",
                    'Destinatario.endereco LIKE' => "%{$_GET['w']}%",
                    'Destinatario.bairro LIKE' => "%{$_GET['w']}%",
                )
            );
        }
        $destinatarios = $this->paginate('Destinatario', $criterios);
        $this->set('fluxo_id', $id);
        $this->set('destinatarios', $destinatarios);
    }
    
    public function getJSONbyCustomer($cliente){
        $json = array(''=>'- qualquer -');
        $fluxos = $this->FluxoLogistico->generateTreeList(
            array(
                'FluxoLogistico.empresa_id' => $this->Session->read('Auth.User.empresa_id'),
                'FluxoLogistico.ativo' => 'sim',
                'FluxoLogistico.cliente_id' => $cliente,
            ),
            null,
            null,
            ' - ',
            null
        );
        
        $json = array();
        
        foreach ($fluxos as $id=>$nome){
            $json[] = array('FluxoLogistico'=>array(
                'id' => $id,
                'nome' => utf8_encode($nome),
            ));
        }
        
        $this->set('array', $json);
        $this->layout = 'ajax';
        $this->render('to_json');
    }
    
    public function getJSONbyCustomerToCustomer($cliente){
        $json = array(''=>'- nenhum fluxo -');
        $fluxos = $this->FluxoLogistico->generateTreeList(
            array(
                'FluxoLogistico.empresa_id' => $this->Session->read('Auth.User.empresa_id'),
                'FluxoLogistico.ativo' => 'sim',
                'FluxoLogistico.cliente_id' => $cliente,
                'FluxoLogistico.id IN (SELECT fluxo_logistico_id FROM acesso_cliente_usuario_fluxo_logistico WHERE acesso_cliente_usuario_id = ?)' => $this->Auth->User('id'),
            ),
            null,
            null,
            ' - ',
            null
        );
        
        $json = array();
        
        foreach ($fluxos as $id=>$nome){
            $json[] = array('FluxoLogistico'=>array(
                'id' => $id,
                'nome' => utf8_encode($nome),
            ));
        }
        
        $this->set('array', $json);
        $this->layout = 'ajax';
        $this->render('to_json');
    }
    
    public function getJSONbyFlux($id = null){
        $json = array(''=>'- escolha -');
        $criterios = array('empresa_id'=>$this->Auth->User('empresa_id'));
        $destinatarios = $this->Destinatario->find(
            'all',
            array(
                'fields' => array(
                    'Destinatario.id',
                    'Destinatario.fantasia',
                    'Destinatario.municipio',
                    'Destinatario.uf',
                ),
            )
        );
        $total = count($destinatarios);
        if ($total==0){
            $destinatarios = $this->Destinatario->find(
                'all',
                array(
                    'fields' => array(
                        'Destinatario.id',
                        'Destinatario.fantasia',
                        'Destinatario.municipio',
                        'Destinatario.uf',
                    ),
                )
            );
        }
        $this->set('array', to_utf8($destinatarios));
        $this->layout = 'ajax';
        $this->render('to_json');
    }
    
    /*
     * DAQUI PRA BAIXO, OS CONTROLLERS DA PARTE ANALITICA
     */
    public function analitico_index(){
        $this->set('menu_cronograma', 1);
        $this->layout = 'default_analitico';
        $criterios = $this->restringir('FluxoLogistico');
        $this->FluxoLogistico->recursive = 0;
        $this->set('titulo', 'Fluxos Logísticos');
        $this->set('fluxo', $this->FluxoLogistico);
        $this->set('lista', $this->FluxoLogistico->generateTreeList($criterios, NULL, NULL, '&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;'));
    }
    
    public function analitico_tarefas(){
        $this->PedidoTransporte->recursive = 2;
        $this->set('menu_tarefas', 1);
        $this->layout = 'default_analitico';
        $criterios = $this->restringir('PedidoTransporte');
        $criterios['PedidoTransporte.frete_id NOT'] = null;
        if (!empty($_GET['flid'])){
            $criterios['FluxoLogistico.id'] = (int)$_GET['flid'];
        }
        if (!empty($_GET['prazo_de']) && !empty($_GET['prazo_ate'])){
            $criterios['PedidoTransporte.prazo_coleta BETWEEN ? AND ?'] = array(DataToSQL($_GET['prazo_de'])." 00:00:00",DataToSQL($_GET['prazo_ate'])." 23:59:59");
            $criterios['PedidoTransporte.prazo_entrega BETWEEN ? AND ?'] = array(DataToSQL($_GET['prazo_de'])." 00:00:00",DataToSQL($_GET['prazo_ate'])." 23:59:59");
        }
        if (!empty($_GET['codigo'])) $criterios['PedidoTransporte.id'] = "{$_GET['codigo']}";
        if (!empty($_GET['direcao'])) $criterios['PedidoTransporte.direcao'] = "{$_GET['direcao']}";
        if (!empty($_GET['situacao'])) $criterios['PedidoTransporte.pedido_transporte_status_id'] = "{$_GET['situacao']}";
        if (!empty($_GET['fantasia'])){
            $criterios[]['OR'] = array(
                'Origem.fantasia LIKE' => "%{$_GET['fantasia']}%",
                'Destino.fantasia LIKE' => "%{$_GET['fantasia']}%",
            );
        }
        if (!empty($_GET['municipio'])){
            $criterios[]['OR'] = array(
                'Origem.municipio LIKE' => "%{$_GET['municipio']}%",
                'Destino.municipio LIKE' => "%{$_GET['municipio']}%",
            );
        }
        if (!empty($_GET['uf'])){
            $criterios[]['OR'] = array(
                'Origem.uf LIKE' => "%{$_GET['uf']}%",
                'Destino.uf LIKE' => "%{$_GET['uf']}%",
            );
        }
        //$criterios['AND']['OR'] = array('prazo_coleta IS NOT NULL','prazo_entrega IS NOT NULL',);
        $this->set('fluxo', $this->FluxoLogistico);
        $this->set('lDirecao', $this->PedidoTransporte->lDirecao);
        $this->set('opcoes_fluxos_logisticos', $this->getFluxoLogisticoFilter());
        $this->set('opcoes_analitico_situacao', $this->PedidoTransporteStatus->find('list'));
        $this->set('lista', $this->paginate('PedidoTransporte', $criterios));
        if (isset($_GET['exportar'])){
            $this->layout = 'exportar_excel';
            $this->render('analitico_tarefas_csv');
        }
    }
    
    public function analitico_tarefas_em_aberto(){
        $this->PedidoTransporte->recursive = 2;
        $this->set('menu_tarefas_em_aberto', 1);
        $this->layout = 'default_analitico';
        $criterios = $this->restringir('PedidoTransporte');
        $criterios['PedidoTransporte.frete_id NOT'] = null;
        $criterios['((PedidoTransporte.pedido_transporte_status_id IS ?) OR (PedidoTransporteStatus.conclui_atividade IS NULL) OR (PedidoTransporteStatus.id IS NULL))'] = null;
        if (!empty($_GET['flid'])){
            $criterios['FluxoLogistico.id'] = (int)$_GET['flid'];
        }
        if (!empty($_GET['prazo_de']) && !empty($_GET['prazo_ate'])){
            $criterios['PedidoTransporte.prazo_coleta BETWEEN ? AND ?'] = array(DataToSQL($_GET['prazo_de'])." 00:00:00",DataToSQL($_GET['prazo_ate'])." 23:59:59");
            $criterios['PedidoTransporte.prazo_entrega BETWEEN ? AND ?'] = array(DataToSQL($_GET['prazo_de'])." 00:00:00",DataToSQL($_GET['prazo_ate'])." 23:59:59");
        }
        if (!empty($_GET['codigo'])) $criterios['PedidoTransporte.id'] = "{$_GET['codigo']}";
        if (!empty($_GET['direcao'])) $criterios['PedidoTransporte.direcao'] = "{$_GET['direcao']}";
        if (!empty($_GET['situacao'])) $criterios['PedidoTransporte.pedido_transporte_status_id'] = "{$_GET['situacao']}";
        if (!empty($_GET['fantasia'])){
            $criterios[]['OR'] = array(
                'Origem.fantasia LIKE' => "%{$_GET['fantasia']}%",
                'Destino.fantasia LIKE' => "%{$_GET['fantasia']}%",
            );
        }
        if (!empty($_GET['municipio'])){
            $criterios[]['OR'] = array(
                'Origem.municipio LIKE' => "%{$_GET['municipio']}%",
                'Destino.municipio LIKE' => "%{$_GET['municipio']}%",
            );
        }
        if (!empty($_GET['uf'])){
            $criterios[]['OR'] = array(
                'Origem.uf LIKE' => "%{$_GET['uf']}%",
                'Destino.uf LIKE' => "%{$_GET['uf']}%",
            );
        }
        //$criterios['AND']['OR'] = array('prazo_coleta IS NOT NULL','prazo_entrega IS NOT NULL',);
        $this->set('fluxo', $this->FluxoLogistico);
        $this->set('lDirecao', $this->PedidoTransporte->lDirecao);
        $this->set('opcoes_fluxos_logisticos', $this->getFluxoLogisticoFilter());
        $this->set('opcoes_analitico_situacao', $this->PedidoTransporteStatus->find('list'));
        $this->set('lista', $this->paginate('PedidoTransporte', $criterios));
        if (isset($_GET['exportar'])){
            $this->layout = 'exportar_excel';
            $this->render('analitico_tarefas_csv');
        } else {
            $this->render('analitico_tarefas');
        }
    }
    
    public function analitico_controle($id) {
        $this->PedidoTransporte->recursive = 3;
        $controle_data = $this->PedidoTransporte->read(null, $id);
        if ($controle_data['PedidoTransporte']['direcao']=='coleta'){
            $situacoes = $this->PedidoTransporteStatus->find('list', array('conditions'=>array('coleta'=>1)));
        } elseif ($controle_data['PedidoTransporte']['direcao']=='entrega'){
            $situacoes = $this->PedidoTransporteStatus->find('list', array('conditions'=>array('entrega'=>1)));
        } elseif ($controle_data['PedidoTransporte']['direcao']=='coleta_entrega'){
            $situacoes = $this->PedidoTransporteStatus->find('list');
        }
        if ($this->request->is('post')){
            $this->PedidoTransporte->id = $id;
            $mensagem = '';
            
            if ($this->request->data['pedido_transporte_status_id']!=$controle_data['PedidoTransporte']['pedido_transporte_status_id']){
                $old = (empty($controle_data['PedidoTransporte']['pedido_transporte_status_id']) ? 'Aguardando':"{$situacoes[$controle_data['PedidoTransporte']['pedido_transporte_status_id']]}");
                $new = (empty($this->request->data['pedido_transporte_status_id']) ? 'Aguardando':"{$situacoes[$this->request->data['pedido_transporte_status_id']]}");
                $mensagem .= "Atualizou o Status de {$old} para {$new}\n";
                $this->PedidoTransporte->saveField('pedido_transporte_status_id', $this->request->data['pedido_transporte_status_id']);
            }
            if ((@$this->request->data['entregue_em'])!=$controle_data['PedidoTransporte']['entregue_em']){
                $mensagem .= "Definiu a Data de Entrega como: {$this->request->data['entregue_em']}\n";
                $this->PedidoTransporte->saveField('entregue_em', $this->request->data['entregue_em']);
            }
            if ((@$this->request->data['coletado_em'])!=$controle_data['PedidoTransporte']['coletado_em']){
                $mensagem .= "Definiu a Data de Coleta como: {$this->request->data['coletado_em']}\n";
                $this->PedidoTransporte->saveField('coletado_em', $this->request->data['coletado_em']);
            }
            if ($this->request->data['arquivo']['error'] === UPLOAD_ERR_OK){
                $this->request->data['arquivo'] = $this->uploadFile($this->request->data['arquivo']);
                $mensagem .= "\nAnexou o arquivo: {$this->request->data['arquivo']['original_name']} (<a href=\"/files/historicos/{$this->request->data['arquivo']['filename']}\" target=\"_blank\">download</a>)";
            } else {
                unset($this->request->data['arquivo']);
            }
            
            $mensagem = (empty($this->request->data['mensagem']) ? $mensagem:"{$mensagem}\n\n{$this->request->data['mensagem']}");
            $historico = array(
                'pedido_transporte_id' => $id,
                'usuario_id' => $this->Auth->User('id'),
                'mensagem' => $mensagem,
            );
            if (!empty($mensagem)){
                $this->PedidoTransporteHistorico->create();
                $this->PedidoTransporteHistorico->save($historico);
            }
            
            return $this->redirect($_SERVER['REQUEST_URI']);
        }
        $this->layout = 'default_analitico';
        $this->set('menu_tarefas', 1);
        $this->set('fluxo', $this->FluxoLogistico);
        $this->set('controle_data', $controle_data);
        $this->set('opcoes_analitico_situacao', $situacoes);
        $this->set('listaMov', $this->PedidoTransporte->find('all', array('conditions'=>array('PedidoTransporte.fluxo_logistico_id'=>$controle_data['FluxoLogistico']['id'], 'PedidoTransporte.destino_id'=>$controle_data['Destino']['id']))));
    }
    
    public function getFluxoLogisticoFilter(){
        $fluxos_integrados_db = $this->Integracao->find('all', array(
            'conditions'=>array(
                'empresa_id_to' => $this->Auth->User('empresa_id'),
                'not' => array('fluxo_logistico_id' => null),
                'FluxoLogistico.ativo' => 'sim',
            )
        ));
        
        $fluxos_integrados = array();
        
        $total = count($fluxos_integrados);
        if ($total > 0){
            foreach ($fluxos_integrados_db as $item){
                $fluxos_integrados[$item['FluxoLogistico']['id']] = $item['FluxoLogistico']['id'];
            }
            sort($fluxos_integrados);
        }
        
        $criterios_fluxos = array(
            'OR'=>array(
                'FluxoLogistico.empresa_id'=>$this->Auth->User('empresa_id'),
                'FluxoLogistico.id' => $fluxos_integrados,
            ),
        );
        
        $order = array(
            'FluxoLogistico.parent_id'=>'ASC',
            'FluxoLogistico.nome'=>'ASC',
        );
        
        return $this->FluxoLogistico->find('all', array('conditions'=>$criterios_fluxos, 'order'=>$order));
    }
    
    private function uploadFile($arquivo){
        $ext = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
	$filename = substr(hash('sha1', str_shuffle(microtime())), 0, 8).".{$ext}";
	$path = WWW_ROOT . 'files' . DS . 'historicos';
        if (move_uploaded_file($arquivo['tmp_name'], $path.DS.$filename)) {
            return array('original_name'=>$arquivo['name'], 'filename'=>$filename);
        } else {
            return null;
        }
    }
    
    /*
     * FLUXO FINANCEIRO - daqui pra baixo
     */
    
    public function financeiro_index(){
        $this->layout = 'default_financeiro';
        $this->Session->write('refer', $this->here);
        $criterios = $this->restringir('FluxoLogistico');
        $this->FluxoLogistico->recursive = 2;
        $this->set('titulo', 'Fluxo Financeiro');
        $this->set('fluxo', $this->FluxoLogistico);
        $this->set('lista', $this->FluxoLogistico->generateTreeList($criterios, NULL, NULL, '&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;'));
    }

    public function financeiro_edit($id = null) {
        $this->layout = 'default_financeiro';
        $criterios = $this->restringir('FluxoLogistico');
        $this->set('fluxos', $this->FluxoLogistico->generateTreeList($criterios, NULL, NULL, '---> '));
        $criterios = $this->restringir('Destinatario');
        $criterios['cliente'] = 1;
        $clientes = $this->Destinatario->find('list', array('conditions'=>$criterios, 'fields'=>array('Destinatario.id', 'Destinatario.fantasia')));
        $clientes_keys = array_keys($clientes);
        $clientes_vals = array_values($clientes);
        array_unshift($clientes_keys, '');
        array_unshift($clientes_vals, '- selecione o cliente -');
        $clientes = array_combine($clientes_keys, $clientes_vals);
        $this->set('clientes', $clientes);
        $this->FluxoLogistico->id = $id;
        if (!$this->FluxoLogistico->exists()) {
            throw new NotFoundException(__('Fluxo Logístico inválido!'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['FluxoLogistico']['empresa_id'] = $this->Session->read('Auth.User.empresa_id');
            if ($this->FluxoLogistico->save($this->request->data)) {
                $this->FluxoLogistico->recover('parent', 'delete');
                $this->FluxoLogistico->reorder(array(
                    'field' => 'nome',
                    'order' => 'ASC',
                ));
                $this->Session->setFlash(__('Fluxo Logístico Salvo!'), 'mensagens/sucesso');
                $this->redirect($this->Session->read('refer'));
            } else {
                $this->Session->setFlash(__('Não foi possível processar sua solicitação, tente mais tarde.'), 'mensagens/alerta');
            }
        } else {
            $this->request->data = $this->FluxoLogistico->read(null, $id);
            unset($this->request->data['FluxoLogistico']['senha']);
        }
        $this->render('financeiro_form');
    }

    public function financeiro_delete($id = null) {
        if ($this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->FluxoLogistico->id = $id;
        if (!$this->FluxoLogistico->exists()) {
            throw new NotFoundException(__('Fluxo Logístico inválido!'));
        }
        try {
            $this->FluxoLogistico->delete();
            $this->FluxoLogistico->recover('parent', 'delete');
            $this->FluxoLogistico->reorder(array(
                'field' => 'nome',
                'order' => 'ASC',
            ));
            $this->Session->setFlash(__('Fluxo Logístico Removido!'), 'mensagens/sucesso');
            $this->redirect($this->Session->read('refer'));
        } catch (PDOException $e) {
            $this->Session->setFlash(__('Não pode remover este item pois ele já foi utilizado em um movimento!'), 'mensagens/alerta');
            $this->redirect($this->Session->read('refer'));
        }
        $this->Session->setFlash(__('O usuário não foi removido!'), 'mensagens/alerta');
        $this->redirect($this->Session->read('refer'));
    }
    
    public function recover(){
        $this->autoRender = false;
        $this->FluxoLogistico->recover('parent', null);
        $this->FluxoLogistico->reorder(array('field'=>'nome', 'order'=>'ASC'));
    }
    
}