<?php
require_once ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';

class Api1Controller extends EmbarcadorAppController {
    
    public $uses = ['Sigepweb.SigepConta', 'TotalExpress.TotalConta', 'Embarcador.Encomenda', 'Cadastros.Destino'];
    
    public $components = array(
        'Session',
        'RequestHandler',
        'Auth' => array(
            'authenticate' => array(
                'CakephpBzUtils.JwtToken' => array(
                    'fields' => array(
                        'username' => 'email',
                        'password' => 'senha',
                    ),
                    'parameter' => 'token',
                    'header' => 'HTTP_X_AUTHORIZATION',
                    'userModel' => 'Usuario',
                    'scope' => array(
                        'Usuario.ativo' => 1
                    ),
                ),
                'Basic' => array(
                    'userModel' => 'Usuario',
                    'fields' => array('username' => 'email', 'password' => 'senha'),
                    'scope' => array('Usuario.ativo' => 1),
                ),
                'Form' => array(
                    'userModel' => 'Usuario',
                    'fields' => array('username' => 'email', 'password' => 'senha'),
                    'scope' => array('Usuario.ativo' => 1),
                ),
            ),
        ),
    );
    
    public function beforeFilter() {
        parent::beforeFilter();
        Configure::write('Cache.disable', false);
        $this->RequestHandler->prefers('json');
        $this->RequestHandler->renderAs($this, 'json');
        $this->RequestHandler->respondAs('json');
    }
    
    public function calcular_frete(){
        Cache::set(array('duration' => '+10 days'));
        if (!$this->request->is('get')){
            throw new MethodNotAllowedException();
        }
        $cepOrigem = @addLeading(onlyNumbers($_GET['cepOrigem']), 8);
        $cepDestino = @addLeading(onlyNumbers($_GET['cepDestino']), 8);
        if ($cepOrigem=='00000000'){
            throw new BadRequestException('cepOrigem deve ser informado.');
        }
        if ($cepDestino=='00000000'){
            throw new BadRequestException('cepDestino deve ser informado.');
        }
        $peso = ((int)@onlyNumbers($_GET['peso'])) / 1000;
        if ($peso<0.100) $peso = 0.100;
        $valor = (float)@$_GET['valor'];
        if ($valor==0) $valor = 0.01;
        
        $uid = AuthComponent::User('acesso_cliente_id');
        $cotacaoKey = hash('sha1', "cotacao-$cepOrigem-$cepDestino-$peso-$valor-$uid-".date('YmdHi'));
        $cotacao = Cache::read($cotacaoKey);
        $errosKey = hash('sha1', "cotacao-erros-$cepOrigem-$cepDestino-$peso-$valor-$uid-".date('YmdHi'));
        $erros = Cache::read($errosKey);
        if (!is_array($cotacao)){
            $cotacao = [];
            $erros = [];
            
            // TOTAL
            $contas = $this->TotalConta->find('all', ['conditions' => [
                'TotalConta.cliente_id' => AuthComponent::User('acesso_cliente_id'),
            ]]);
            foreach ($contas as $conta) {
                try {
                    $data = $this->TotalConta->TotalExpressCotacao($conta['TotalConta']['id'], $cepDestino, $peso, $valor);
                } catch (Exception $ex) {
                    $data = [];
                    $erros[] = $ex->getMessage();
                }
                foreach ($data as $i) {
                    $prazo = (int)$i->DadosFrete->Prazo;
                    $rPrevisao = $this->TotalConta->query("SELECT FN_EMBARCADOR_DATA_PREVISAO(({$prazo} + 1)) as data_prevista;");
                    $data_prevista = $rPrevisao[0][0]['data_prevista'];
                    $cotacao[] = [
                        'descricao' => "TOTAL",
                        'prazo' => $prazo,
                        'valor' => (float)FloatToSQL($i->DadosFrete->ValorServico),
                        'sabado' => 0,
                        'data_prevista' => $data_prevista,
                    ];
                }
            }
            
            // CORREIOS
            $contas = $this->SigepConta->find('all', ['conditions' => [
                'SigepConta.cliente_id' => AuthComponent::User('acesso_cliente_id'),
            ]]);
            foreach ($contas as $conta){
                try {
                    $data = $this->SigepConta->SigepwebCotacao($conta['SigepConta']['id'], $cepOrigem, $cepDestino, $peso);
                } catch (Exception $ex) {
                    $data = [];
                    $erros[] = $ex->getMessage();
                }
                foreach ($data as $i){
                    $prazo = (int)$i['prazoEntrega'];
                    $rPrevisao = $this->TotalConta->query("SELECT FN_EMBARCADOR_DATA_PREVISAO(({$prazo} + 1)) as data_prevista;");
                    $data_prevista = $rPrevisao[0][0]['data_prevista'];
                    $cotacao[] = [
                        'descricao' => "CORREIOS " . $i['servico']['nome'],
                        'prazo' => $prazo,
                        'valor' => $i['valor'],
                        'sabado' => (int)$i['entregaSabado'],
                        'data_prevista' => $data_prevista,
                    ];
                }
            }

            Cache::write($cotacaoKey, $cotacao);
            Cache::write($errosKey, $erros);
        }
        
        $acesso_cliente_id = AuthComponent::User('acesso_cliente_id');
        if (!empty($acesso_cliente_id)) $this->Encomenda->query("CALL SP_RECURSO_CONSUMIR(1, {$acesso_cliente_id}, NOW(), 1);");
        
        $this->set([
            'data' => $cotacao,
            'erros' => $erros,
            '_serialize' => ['data', 'erros'],
        ]);
    }
    
    public function rastrear(){
        $this->RequestHandler->respondAs('json');
        if (!$this->request->is('get')){
            throw new MethodNotAllowedException();
        }
        $acesso_cliente_id = AuthComponent::User('acesso_cliente_id');
        if (!empty($acesso_cliente_id)){
            $criterios = [
                'Encomenda.embarcador_id' => AuthComponent::User('acesso_cliente_id'),
            ];
        } else {
            $uid = AuthComponent::User('id');
            $criterios = [
                'Encomenda.embarcador_id' => $this->Destino->meusClientes($uid),
            ];
        }
        $nfe_serie = onlyNumbers(@$_GET['nfe_serie']);
        $nfe_numero = onlyNumbers(@$_GET['nfe_numero']);
        $nfe_chave = onlyNumbers(@$_GET['nfe_chave']);
        if (strlen($nfe_chave)==44){
            $criterios['Encomenda.nfe_chave'] = $nfe_chave;
        } elseif ($nfe_serie>0 && $nfe_numero>0){
            $criterios['Encomenda.nfe_serie'] = $nfe_serie;
            $criterios['Encomenda.nfe_numero'] = $nfe_numero;
        } else {
            throw new BadMethodCallException();
        }
        
        $e = $this->Encomenda->find('first', ['conditions' => $criterios]);
        if (!isset($e['Encomenda']['id'])){
            $e = null;
        }
        
        $tc = $this->TotalConta->find('first', ['conditions' => [
            'TotalConta.cliente_id' => $e['Embarcador']['id'],
        ]]);
        if (@$e['Transportador']['correios']==1 && !empty($e['Encomenda']['codigo_rastreamento'])) {
            $linkRastreio = "https://linkcorreios.com.br/{$e['Encomenda']['codigo_rastreamento']}";
        } elseif (!empty($tc['TotalConta']['id']) && !empty($tc['TotalConta']['eid']) && !empty($e['Encomenda']['codigo_rastreamento']) && $e['Transportador']['cpf_cnpj']==73939449000193) {
            $eid = $tc['TotalConta']['eid'];
            $linkRastreio = "https://tracking.totalexpress.com.br/poupup_track.php?reid={$eid}&pedido={$e['Encomenda']['codigo_rastreamento']}&nfiscal={$e['Encomenda']['nfe_numero']}";
        } else {
            $linkRastreio = null;
        }

        $this->set([
            'encomenda' => to_utf8($e),
            'linkRastreio' => $linkRastreio,
            '_serialize' => ['encomenda','linkRastreio'],
        ]);
    }
    
}