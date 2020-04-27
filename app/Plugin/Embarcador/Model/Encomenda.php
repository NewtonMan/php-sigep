<?php
require_once ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';

use NFePHP\NFe\Common\Standardize;
use NFePHP\DA\NFe\Danfe;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;

App::uses('HttpSocket', 'Network/Http');
App::uses('CakeEmail', 'Network/Email');
class Encomenda extends EmbarcadorAppModel {
    
    public $useTable = 'embarcador_encomendas';
    
    public $belongsTo = [
        'Tipo' => [
            'className' => 'Embarcador.Tipo',
            'foreignKey' => 'tipo_encomenda_id',
        ],
        'Embarcador' => [
            'className' => 'Cadastros.Destino',
            'foreignKey' => 'embarcador_id',
        ],
        'Destinatario' => [
            'className' => 'Cadastros.Destino',
            'foreignKey' => 'destinatario_id',
        ],
        'Transportador' => [
            'className' => 'Cadastros.Destino',
            'foreignKey' => 'transportador_id',
        ],
        'LocalEntrega' => [
            'className' => 'Embarcador.Local',
            'foreignKey' => 'local_entrega_id',
        ],
        'ModalidadeFrete' => [
            'className' => 'Embarcador.ModalidadeFrete',
            'foreignKey' => 'modalidade_frete_id',
        ],
        'Modal' => [
            'className' => 'Embarcador.Modal',
            'foreignKey' => 'modal_id',
        ],
        'Status' => [
            'className' => 'Embarcador.Status',
            'foreignKey' => 'status_id',
        ],
        'City' => [
            'className' => 'Embarcador.City',
            'foreignKey' => 'city_id',
        ],
        'Zone' => [
            'className' => 'Embarcador.Zone',
            'foreignKey' => false,
            'conditions' => [
                'Zone.id' => 'City.zone_id',
            ],
        ],
        'State' => [
            'className' => 'Embarcador.State',
            'foreignKey' => false,
            'conditions' => [
                'State.id' => 'City.state_id',
            ],
        ],
        'OcorrenciaResponsavel' => [
            'className' => 'Usuario',
            'foreignKey' => 'ocorrencia_responsavel_id',
        ],
    ];
    
    public $hasMany = [
        'Volume' => [
            'className' => 'Embarcador.Volume',
            'foreignKey' => 'encomenda_id',
            'order' => [
                'Volume.id' => 'ASC',
            ],
        ],
        'Historico' => [
            'className' => 'Embarcador.Historico',
            'foreignKey' => 'encomenda_id',
            'order' => [
                'Historico.id' => 'DESC',
            ],
        ],
    ];
    
    public $order = [
        'Encomenda.id' => 'DESC',
    ];
    
    public $virtualFields = [
        'peso' => 'SELECT SUM((IFNULL(pesoB, 0) * IFNULL(qVol, 1))) FROM embarcador_volumes EV WHERE EV.encomenda_id=Encomenda.id',
    ];
    
    public function setHistorico($encomenda_id, $historico){
        if (class_exists('AuthComponent')){
            $uid = AuthComponent::User('id');
        } else {
            $uid = null;
        }
        $this->Historico->create();
        $this->Historico->save([
            'usuario_id' => $uid,
            'encomenda_id' => $encomenda_id,
            'mensagem' => $historico,
        ]);
    }
    
    public function setOcorrenciaLida($encomenda_id){
        $historico = "Abriu a Ocorrência para Tratamento.";
        $this->id = $encomenda_id;
        $this->saveField('ocorrencia_lida', 1);
        $this->setHistorico($encomenda_id, $historico);
    }
    
    public function setOcorrenciaResponsavelId($encomenda_id, $user_id){
        $encomenda = $this->read(null, $encomenda_id);
        $this->id = $encomenda_id;
        $user = $this->OcorrenciaResponsavel->read(null, $user_id);
        if (empty($encomenda['Encomenda']['ocorrencia_responsavel_id'])){
            $historico = "{$user['Usuario']['nome_completo']} foi definido como responsável pela Ocorrência nesta Encomenda.";
        } else {
            $historico = "Ocorrência transferida para {$user['Usuario']['nome_completo']}.";
            $this->saveField('ocorrencia_lida', null);
        }
        $this->saveField('ocorrencia_responsavel_id', $user_id);
        $this->setHistorico($encomenda_id, $historico);
    }
    
    public function setTransportadorId($encomenda_id, $transportador_id){
        $encomenda = $this->read(null, $encomenda_id);
        $this->id = $encomenda_id;
        $transportador = $this->Transportador->read(null, $transportador_id);
        if ($encomenda['Encomenda']['transportador_id']!=$transportador_id){
            $historico = "Transferiu a Transportadora de \"{$encomenda['Transportador']['fantasia']}\" para \"{$transportador['Transportador']['fantasia']}\".";
            $this->saveField('transportador_id', $transportador_id);
            $this->setHistorico($encomenda_id, $historico);
        }
    }
    
    public function setStatusId($encomenda_id, $status_id){
        $encomenda = $this->read(null, $encomenda_id);
        $status = $this->Status->read(null, $status_id);
        if (empty($encomenda['Status']['id'])){
            $historico = "Definiu o Status";
        } else {
            $historico = "Mudou o Status de \"{$encomenda['Status']['name']}\"";
        }
        $historico .= " para \"{$status['Status']['name']}\"";
        $this->id = $encomenda_id;
        $this->saveField('status_id', $status_id);
        if (($status_id==2 || $status_id==3) && $encomenda['Embarcador']['cpf_cnpj']==27403527000202) {
            $HttpSocket = new HttpSocket();
            $HttpSocket->post('https://caffeinearmy.free.beeceptor.com/nfe_entegue', ['nfe_chave' => $encomenda['Encomenda']['nfe_chave']]);
        }
        $this->setHistorico($encomenda_id, $historico);
    }
    
    public function setModalId($encomenda_id, $modal_id){
        $encomenda = $this->read(null, $encomenda_id);
        $modal = $this->Modal->read(null, $modal_id);
        if (empty($encomenda['Modal']['id'])){
            $historico = "Definiu o Modal";
        } else {
            $historico = "Mudou o Modal de \"{$encomenda['Modal']['name']}\"";
        }
        $historico .= " para \"{$modal['Modal']['name']}\"";
        $this->id = $encomenda_id;
        $this->saveField('modal_id', $modal_id);
        $this->setHistorico($encomenda_id, $historico);
    }
    
    public function setDataColeta($encomenda_id, $data_coleta){
        $encomenda = $this->read(null, $encomenda_id);
        if (empty($encomenda['Encomenda']['codigo_rastreio'])){
            $historico = "Informou a Data da Coleta como: ".$data_coleta;
        } elseif ($encomenda['Encomenda']['data_coleta']!=$data_coleta) {
            $historico = "Mudou a Data da Coleta de \"{$encomenda['Encomenda']['data_coleta']}\" para \"{$data_coleta}\"";
        }
        $this->id = $encomenda_id;
        $this->saveField('data_coleta', $data_coleta);
        $this->setHistorico($encomenda_id, $historico);
    }
    
    public function setDataPrevisao($encomenda_id, $data_previsao){
        $encomenda = $this->read(null, $encomenda_id);
        if (empty($encomenda['Encomenda']['codigo_rastreio'])){
            $historico = "Informou a Data da Previsao como: ".$data_previsao;
        } elseif ($encomenda['Encomenda']['data_previsao']!=$data_previsao) {
            $historico = "Mudou a Data da Previsao de \"{$encomenda['Encomenda']['data_previsao']}\" para \"{$data_previsao}\"";
        }
        $this->id = $encomenda_id;
        $this->saveField('data_previsao', $data_previsao);
        $this->setHistorico($encomenda_id, $historico);
    }
    
    public function setDataConclusao($encomenda_id, $data_conclusao){
        $encomenda = $this->read(null, $encomenda_id);
        if (empty($encomenda['Encomenda']['codigo_rastreio'])){
            $historico = "Informou a Data da Conclusao como: ".$data_conclusao;
        } elseif ($encomenda['Encomenda']['data_conclusao']!=$data_conclusao) {
            $historico = "Mudou a Data da Conclusao de \"{$encomenda['Encomenda']['data_conclusao']}\" para \"{$data_conclusao}\"";
        }
        $this->id = $encomenda_id;
        $this->saveField('data_conclusao', $data_conclusao);
        $this->setHistorico($encomenda_id, $historico);
    }
    
    public function setDataRomaneio($encomenda_id, $data_romaneio){
        $encomenda = $this->read(null, $encomenda_id);
        if (empty($encomenda['Encomenda']['codigo_rastreio'])){
            $historico = "Informou a Data do Romaneio como: ".$data_romaneio;
        } elseif ($encomenda['Encomenda']['data_romaneio']!=$data_romaneio) {
            $historico = "Mudou a Data do Romaneio de \"{$encomenda['Encomenda']['data_romaneio']}\" para \"{$data_romaneio}\"";
        }
        $this->id = $encomenda_id;
        $this->saveField('data_romaneio', $data_romaneio);
        $this->setHistorico($encomenda_id, $historico);
    }
    
    public function setCodigoRastreamento($encomenda_id, $codigo_rastreamento){
        $encomenda = $this->read(null, $encomenda_id);
        if (empty($encomenda['Encomenda']['codigo_rastreio'])){
            $historico = "Informou o Código de Rastreio como: ".$codigo_rastreamento;
        } elseif ($encomenda['Encomenda']['codigo_rastreamento']!=$codigo_rastreamento) {
            $historico = "Mudou o Código de Rastreio de \"{$encomenda['Encomenda']['codigo_rastreamento']}\" para \"{$codigo_rastreamento}\"";
        }
        $this->id = $encomenda_id;
        $this->saveField('codigo_rastreamento', $codigo_rastreamento);
        $this->setHistorico($encomenda_id, $historico);
    }
    
    public function importNFe($filename, $empresa_id){
        if (file_exists($filename)){
            $encomenda = false;
            $std = new Standardize();
            try {
                $data = $std->toArray(file_get_contents($filename));
            } catch (Exception $ex) {
                return false;
            }
            //die(print_r($data));
            switch ($data['attributes']['versao']) {
                case '4.00':
                case '3.10':
                    $nfe_chave = onlyNumbers(substr($data['NFe']['infNFe']['attributes']['Id'], 3));
                    if (strlen($nfe_chave)==44){
                        $encomenda = $this->find('first', ['conditions' => [
                            'Encomenda.nfe_chave' => $nfe_chave,
                        ]]);
                        if (empty($encomenda[$this->alias]['id'])){
                            $nfe_serie = substr($nfe_chave, 22, 3);
                            $nfe_numero = substr($nfe_chave, 25, 9);
                            $data_emissao = DataFromSQL(substr($data['NFe']['infNFe']['ide']['dhEmi'], 0, 10));
                            $embarcador = $this->nfe400GetEmbarcador($empresa_id, $data);
                            $destinatario = $this->nfe400GetDestinatario($empresa_id, $data);
                            $transportador = $this->nfe400GetTransportador($empresa_id, $data);
                            $local_entrega = $this->nfe400GetLocalEntrega($empresa_id, $data);
                            $new = [
                                'Encomenda' => [
                                    'tipo_encomenda_id' => 1,
                                    'nfe_chave' => $nfe_chave,
                                    'nfe_serie' => $nfe_serie,
                                    'nfe_numero' => $nfe_numero,
                                    'data_emissao' => $data_emissao,
                                    'embarcador_id' => $embarcador['id'],
                                    'destinatario_id' => $destinatario['id'],
                                    'transportador_id' => $transportador['id'],
                                    'local_entrega_id' => $local_entrega['id'],
                                    'city_id' => $local_entrega['cMun'],
                                    'modalidade_frete_id' => $data['NFe']['infNFe']['transp']['modFrete'],
                                    'valor_declarado' => FloatFromSQL($data['NFe']['infNFe']['total']['ICMSTot']['vNF']),
                                    'observacoes' => $data['NFe']['infNFe']['infAdic']['infCpl'],
                                    'ultima_noticia' => date('d/m/Y H:i'),
                                ],
                                'Volume' => $this->nfe400GetVolumes($empresa_id, $data),
                            ];
                            $this->create();
                            $this->saveAll($new, ['deep' => true]);
                            $encomenda = $this->read(null, $this->id);
                        }
                    }
                    break;
            }
            return $encomenda;
        } else {
            return false;
        }
    }
    
    private function nfe400GetEmbarcador($empresa_id, $nfe_array){
        $cpf_cnpj = (isset($nfe_array['NFe']['infNFe']['emit']['CNPJ']) ? $nfe_array['NFe']['infNFe']['emit']['CNPJ']:$nfe_array['NFe']['infNFe']['emit']['CPF']);
        $destino = $this->Embarcador->find('first', ['conditions' => [
            'Embarcador.cpf_cnpj' => onlyNumbers($cpf_cnpj),
        ]]);
        if (empty($destino['Embarcador']['id'])){
            $data = [
                'empresa_id' => $empresa_id,
                'cpf_cnpj' => onlyNumbers($cpf_cnpj),
                'rg_insc_estadual' => (isset($nfe_array['NFe']['infNFe']['emit']['IE']) ? $nfe_array['NFe']['infNFe']['emit']['IE']:$nfe_array['NFe']['infNFe']['emit']['RG']),
                'fantasia' => $nfe_array['NFe']['infNFe']['emit']['xFant'],
                'nome_razao' => $nfe_array['NFe']['infNFe']['emit']['xNome'],
                'endereco' => $nfe_array['NFe']['infNFe']['emit']['enderEmit']['xLgr'],
                'numero' => (int) onlyNumbers($nfe_array['NFe']['infNFe']['emit']['enderEmit']['nro']),
                'complemento' => @$nfe_array['NFe']['infNFe']['emit']['enderEmit']['xCpl'],
                'bairro' => $nfe_array['NFe']['infNFe']['emit']['enderEmit']['xBairro'],
                'ibge_cidade_id' => $nfe_array['NFe']['infNFe']['emit']['enderEmit']['cMun'],
                'ibge_estado_id' => substr($nfe_array['NFe']['infNFe']['emit']['enderEmit']['cMun'], 0, 2),
                'municipio' => $nfe_array['NFe']['infNFe']['emit']['enderEmit']['xMun'],
                'uf' => $nfe_array['NFe']['infNFe']['emit']['enderEmit']['UF'],
                'cep' => @$nfe_array['NFe']['infNFe']['emit']['enderEmit']['CEP'],
                'telefones' => @$nfe_array['NFe']['infNFe']['emit']['enderEmit']['fone'],
            ];
            $this->Embarcador->create();
            $this->Embarcador->save($data);
            $destino = $this->Embarcador->read(null, $this->Embarcador->id);
        }
        return $destino['Embarcador'];
    }
    
    private function nfe400GetTransportador($empresa_id, $nfe_array){
        $nfe_chave = onlyNumbers(substr($nfe_array['NFe']['infNFe']['attributes']['Id'], 3));
        if (isset($nfe_array['NFe']['infNFe']['transp']['transporta'])) {
            $cpf_cnpj = @(isset($nfe_array['NFe']['infNFe']['transp']['transporta']['CNPJ']) ? $nfe_array['NFe']['infNFe']['transp']['transporta']['CNPJ']:$nfe_array['NFe']['infNFe']['transp']['transporta']['CPF']);
            $destino = $this->Transportador->find('first', ['conditions' => [
                'Transportador.cpf_cnpj' => onlyNumbers($cpf_cnpj),
            ]]);
            if (empty($destino['Transportador']['id'])){
                $data = [
                    'empresa_id' => $empresa_id,
                    'cpf_cnpj' => onlyNumbers($cpf_cnpj),
                    'rg_insc_estadual' => @(isset($nfe_array['NFe']['infNFe']['transp']['transporta']['IE']) ? $nfe_array['NFe']['infNFe']['transp']['transporta']['IE']:(isset($nfe_array['NFe']['infNFe']['transp']['transporta']['RG']) ? $nfe_array['NFe']['infNFe']['transp']['transporta']['RG']:'ISENTO')),
                    'fantasia' => @(isset($nfe_array['NFe']['infNFe']['transp']['transporta']['xFant']) ? $nfe_array['NFe']['infNFe']['transp']['transporta']['xFant']:$nfe_array['NFe']['infNFe']['transp']['transporta']['xNome']),
                    'nome_razao' => @$nfe_array['NFe']['infNFe']['transp']['transporta']['xNome'],
                    'endereco' => @$nfe_array['NFe']['infNFe']['transp']['transporta']['xEnder'],
                    'municipio' => @$nfe_array['NFe']['infNFe']['transp']['transporta']['xMun'],
                    'uf' => @$nfe_array['NFe']['infNFe']['transp']['transporta']['UF'],
                ];
                $this->Transportador->create();
                $this->Transportador->save($data);
                $destino = $this->Transportador->read(null, $this->Transportador->id);
            }
            return $destino['Transportador'];
        } else {
            return [
                'id' => null
            ];
        }
    }
    
    private function nfe400GetDestinatario($empresa_id, $nfe_array){
        $cpf_cnpj = (isset($nfe_array['NFe']['infNFe']['dest']['CNPJ']) ? $nfe_array['NFe']['infNFe']['dest']['CNPJ']:$nfe_array['NFe']['infNFe']['dest']['CPF']);
        $destino = $this->Destinatario->find('first', ['conditions' => [
            'Destinatario.cpf_cnpj' => onlyNumbers($cpf_cnpj),
        ]]);
        if (empty($destino['Destinatario']['id'])){
            $data = [
                'empresa_id' => $empresa_id,
                'cpf_cnpj' => onlyNumbers($cpf_cnpj),
                'rg_insc_estadual' => (isset($nfe_array['NFe']['infNFe']['dest']['IE']) ? $nfe_array['NFe']['infNFe']['dest']['IE']:(isset($nfe_array['NFe']['infNFe']['dest']['RG']) ? $nfe_array['NFe']['infNFe']['dest']['RG']:'ISENTO')),
                'fantasia' => (isset($nfe_array['NFe']['infNFe']['dest']['xFant']) ? $nfe_array['NFe']['infNFe']['dest']['xFant']:$nfe_array['NFe']['infNFe']['dest']['xNome']),
                'nome_razao' => $nfe_array['NFe']['infNFe']['dest']['xNome'],
                'endereco' => $nfe_array['NFe']['infNFe']['dest']['enderDest']['xLgr'],
                'numero' => (int) onlyNumbers($nfe_array['NFe']['infNFe']['dest']['enderDest']['nro']),
                'complemento' => @$nfe_array['NFe']['infNFe']['dest']['enderDest']['xCpl'],
                'bairro' => $nfe_array['NFe']['infNFe']['dest']['enderDest']['xBairro'],
                'ibge_cidade_id' => $nfe_array['NFe']['infNFe']['dest']['enderDest']['cMun'],
                'ibge_estado_id' => substr($nfe_array['NFe']['infNFe']['dest']['enderDest']['cMun'], 0, 2),
                'municipio' => $nfe_array['NFe']['infNFe']['dest']['enderDest']['xMun'],
                'uf' => $nfe_array['NFe']['infNFe']['dest']['enderDest']['UF'],
                'cep' => @$nfe_array['NFe']['infNFe']['dest']['enderDest']['CEP'],
                'telefones' => @$nfe_array['NFe']['infNFe']['dest']['enderDest']['fone'],
            ];
            $this->Destinatario->create();
            $this->Destinatario->save($data);
            $destino = $this->Destinatario->read(null, $this->Destinatario->id);
        }
        return $destino['Destinatario'];
    }
    
    private function nfe400GetLocalEntrega($empresa_id, $nfe_array){
        if (isset($nfe_array['NFe']['infNFe']['entrega'])){
            $data = $nfe_array['NFe']['infNFe']['entrega'];
        } else {
            $cpf_cnpj = (isset($nfe_array['NFe']['infNFe']['dest']['CNPJ']) ? $nfe_array['NFe']['infNFe']['dest']['CNPJ']:$nfe_array['NFe']['infNFe']['dest']['CPF']);
            $data = [
                'CPF' => (isset($nfe_array['NFe']['infNFe']['dest']['CPF']) ? $nfe_array['NFe']['infNFe']['dest']['CPF']:null),
                'CNPJ' => (isset($nfe_array['NFe']['infNFe']['dest']['CNPJ']) ? $nfe_array['NFe']['infNFe']['dest']['CNPJ']:null),
                'xLgr' => $nfe_array['NFe']['infNFe']['dest']['enderDest']['xLgr'],
                'nro' => (int) onlyNumbers($nfe_array['NFe']['infNFe']['dest']['enderDest']['nro']),
                'xCpl' => @$nfe_array['NFe']['infNFe']['dest']['enderDest']['xCpl'],
                'xBairro' => $nfe_array['NFe']['infNFe']['dest']['enderDest']['xBairro'],
                'cMun' => $nfe_array['NFe']['infNFe']['dest']['enderDest']['cMun'],
                'xMun' => $nfe_array['NFe']['infNFe']['dest']['enderDest']['xMun'],
                'UF' => $nfe_array['NFe']['infNFe']['dest']['enderDest']['UF'],
                'CEP' => @$nfe_array['NFe']['infNFe']['dest']['enderDest']['CEP'],
            ];
        }
        $this->LocalEntrega->create();
        $this->LocalEntrega->save($data);
        $destino = $this->LocalEntrega->read(null, $this->LocalEntrega->id);
        return $destino['LocalEntrega'];
    }
    
    private function nfe400GetVolumes($empresa_id, $nfe_array){
        $vol = [];
        if (isset($nfe_array['NFe']['infNFe']['transp']['vol']['qVol'])){
            $vol[] = [
                'Volume' => $nfe_array['NFe']['infNFe']['transp']['vol'],
            ];
        } elseif (isset($nfe_array['NFe']['infNFe']['transp']['vol'])){
            foreach ($nfe_array['NFe']['infNFe']['transp']['vol'] as $v){
                $vol[] = [
                    'Volume' => $v,
                ];
            }
        }
        foreach ($vol as $x => $v){
            $vol[$x]['Volume']['pesoL'] = FloatFromSQL(@$vol[$x]['Volume']['pesoL']);
            $vol[$x]['Volume']['pesoB'] = FloatFromSQL(@$vol[$x]['Volume']['pesoB']);
        }
        return $vol;
    }
    
}