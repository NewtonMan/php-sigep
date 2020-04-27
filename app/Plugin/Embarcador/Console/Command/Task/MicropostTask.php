<?php
App::uses('HttpSocket', 'Network/Http');
class MicropostTask extends Shell {
    
    public $uses = ['Embarcador.Encomenda', 'Embarcador.CorreioStatus', 'Legado.RemessaArquivo', 'Cadastros.Destino', 'Movimento', 'Micropost.Conta'];
    
    public function captura_micropost_situacao(){
        $this->out("Captura Situação Micropost...");
        $clientes_micropost = $this->Conta->find('all', [
            'conditions' => [
                'Cliente.embarcador' => 1,
            ],
        ]);
        foreach ($clientes_micropost as $cm){
            $this->out("Cliente MICROPOST: {$cm['Cliente']['fantasia']}");

            // PREVISÃO
            $page = 1;
            captura_micropost_previsao:
            $this->out("Capturando Status...Bloco {$page}");
            $encomendas = $this->Encomenda->find('all', ['conditions' => [
                'Encomenda.embarcador_id' => $cm['Cliente']['id'],
                'Encomenda.tipo_encomenda_id' => 1,
                'Transportador.correios' => 1,
                'Encomenda.cancelado IS NULL',
                'Encomenda.codigo_rastreamento IS NOT NULL',
                'Encomenda.servico_transportador IS NOT NULL',
                'Status.conclui IS NULL',
                'Encomenda.data_conclusao IS NULL',
            ], 'order' => ['Encomenda.data_emissao' => 'DESC'], 'limit' => 100, 'page' => $page]);
            $objetos = [];
            foreach ($encomendas as $i){
                $reply = $this->captura_micropost_situacao_item($cm, substr($i['Encomenda']['codigo_rastreamento'], 0, 11));
                if (@$reply['EventoObjeto']['Descricao']=='Nenhuma Ocorrencia Localizada para este Objeto') {
                    continue;
                }
                foreach ($reply['EventoObjeto'] as $rx => $ri) {
                    if (!isset($ri['Tipo']) || !isset($ri['Status'])) continue;
                    correio_status_consulta:
                    $cs = $this->CorreioStatus->find('first', ['conditions' => [
                        'CorreioStatus.Tipo' => $ri['Tipo'],
                        'CorreioStatus.Status' => $ri['Status'],
                    ]]);
                    if (empty($cs['CorreioStatus']['id'])){
                        try {
                            $this->CorreioStatus->create();
                            $this->CorreioStatus->save($ri);
                        } catch (Exception $ex) {
                            $this->out("Falha ao gravar CorreioStatus... tentar novamente!");
                            goto correio_status_consulta;
                        }
                        $correio_status_id = $this->CorreioStatus->id;
                        $cs = $this->CorreioStatus->read(null, $correio_status_id);
                    } else {
                        $correio_status_id = $cs['CorreioStatus']['id'];
                    }
                    if ($rx==0){
                        $this->Encomenda->id = $i['Encomenda']['id'];
                        $this->Encomenda->saveField('correio_status_id', $correio_status_id);
                        if ($cs['CorreioStatus']['status_id']!='' && $cs['CorreioStatus']['status_id']!=$i['Encomenda']['status_id']){
                            $this->Encomenda->setStatusId($i['Encomenda']['id'], $cs['CorreioStatus']['status_id']);
                        }
                        if ($cs['CorreioStatus']['entrega']==1){
                            $this->Encomenda->saveField('data_conclusao', DataFromSQL(substr($ri['DataEvento'], 0, 10)));
                        } elseif ($cs['CorreioStatus']['coleta']==1){
                            $data_coleta = DataToSQL(empty($i['Encomenda']['data_coleta']) ? date('d/m/Y'):$i['Encomenda']['data_coleta']);
                            $time_coleta = strtotime("{$data_coleta} 00:00:00");
                            $data_coleta_correio = substr($ri['DataEvento'], 0, 10);
                            $time_coleta_correio = strtotime("{$data_coleta_correio} 00:00:00");
                            if ($time_coleta<$time_coleta_correio){
                                $time_previsao = strtotime(DataToSQL($i['Encomenda']['data_previsao']) . ' 00:00:00') - $time_coleta;
                                $time_previsao_correio = $time_coleta_correio + $time_previsao;
                                $data_previsao = date('d/m/Y', $time_previsao_correio);
                                $this->Encomenda->saveField('data_coleta', $data_coleta_correio);
                                $this->Encomenda->saveField('data_previsao', $data_previsao);
                            }
                        }
                    }
                }
            }
            $total = count($encomendas);
            if ($total>=100){
                $page++;
                goto captura_micropost_previsao;
            }
        }
    }
    
    public function captura_micropost_situacao_item($conta, $codigo){
        $params = [
            'cnpj' => substr(exibirCpfCnpj($conta['Conta']['cnpj']), 5),
            'numeroCartao' => $conta['Conta']['cartao_postagem'],
            'numeroRegistro' => $codigo,
            'usuario' => $conta['Conta']['usuario'],
            'senha' => $conta['Conta']['senha'],
        ];
        $socket = new HttpSocket();
        $result = $socket->post('http://webservice.prepostagem.com.br/MpWebService.asmx/RetornaStatusDoObjeto', $params);
        $std = simplexml_load_string($result->body);
        $json = json_encode($std);
        $array = json_decode($json, true);
        return $array;
    }
    
    public function captura_micropost_codigo($conta, $nf){
        $params = [
            'numeroNF' => $nf,
            'usuario' => $conta['Conta']['usuario'],
            'senha' => $conta['Conta']['senha'],
        ];
        $socket = new HttpSocket();
        $result = $socket->post('http://webservice.prepostagem.com.br/MpWebService.asmx/BuscaEtiquetaPorNF', $params);
        $std = simplexml_load_string($result->body);
        return $std->string;
    }
    
    public function captura_micropost_postagem($conta, $codigo){
        $params = [
            'numeroDeRegistro' => $codigo,
            'usuario' => $conta['Conta']['usuario'],
            'senha' => $conta['Conta']['senha'],
        ];
        $socket = new HttpSocket();
        $result = $socket->post('http://webservice.prepostagem.com.br/MpWebService.asmx/BuscaDadosPLP', $params);
        $std = simplexml_load_string($result->body);
        $json = json_encode($std);
        $array = json_decode($json, true);
        return $array;
    }
    
    public function captura_micropost_orcamento($conta, $encomenda_id){
        $e = $this->Encomenda->read(null, $encomenda_id);
        $params = [
            'cnpj' => substr(exibirCpfCnpj($conta['Conta']['cnpj']), 5),
            'numeroCartao' => $conta['Conta']['cartao_postagem'],
            'cepDestino' => onlyNumbers($e['LocalEntrega']['CEP']),
            'pesoReal' => number_format($e['Encomenda']['peso'], 3, '', ''),
            'valorDeclarado' => 0.00,
            'altura' => 2,
            'largura' => 11,
            'comprimento' => 16,
            'avisoDeRecebimento' => 'False',
            'usuario' => $conta['Conta']['usuario'],
            'senha' => $conta['Conta']['senha'],
        ];
        $socket = new HttpSocket();
        $result = $socket->post('http://webservice.prepostagem.com.br/MpWebService.asmx/RetornaOrcamento', $params);
        $std = simplexml_load_string($result->body);
        $json = json_encode($std);
        $array = json_decode($json, true);
        return $array;
    }
    
    public function captura_micropost_previsao(){
        $this->out("Captura Rastreios Micropost...");
        $clientes_micropost = $this->Conta->find('all', [
            'conditions' => [
                'Cliente.embarcador' => 1,
            ],
        ]);
        foreach ($clientes_micropost as $cm){
            $this->out("Cliente MICROPOST: {$cm['Cliente']['fantasia']}");

            // PREVISÃO
            $page = 1;
            captura_micropost_previsao:
            $this->out("Capturando Previsão...Bloco {$page}");
            $encomendas = $this->Encomenda->find('all', ['conditions' => [
                'Encomenda.embarcador_id' => $cm['Cliente']['id'],
                'Encomenda.tipo_encomenda_id' => 1,
                'Transportador.correios' => 1,
                'Encomenda.cancelado IS NULL',
                'Encomenda.codigo_rastreamento IS NOT NULL',
                'Encomenda.servico_transportador IS NOT NULL',
                'Encomenda.data_coleta IS NOT NULL',
                'Encomenda.data_previsao IS NULL',
            ], 'limit' => 100, 'page' => $page]);
            foreach ($encomendas as $i){
                $this->out("Consultando Orcamento {$i['Encomenda']['codigo_rastreamento']}: ", false);
                $reply = $this->captura_micropost_orcamento($cm, $i['Encomenda']['id']);
                $data_previsao = null;
                foreach ($reply['dadosTarifaRetorno'] as $dadosTarifaRetorno){
                    if ($dadosTarifaRetorno['prazoEntrega']<=0 || !is_null($data_previsao)) continue;
                    if ($dadosTarifaRetorno['codigo']==$i['Encomenda']['servico_transportador']){
                        $time_coleta = strtotime(DataToSQL($i['Encomenda']['data_coleta']) . ' 00:00:00');
                        $time_previsao = $time_coleta + 60*60*24*($dadosTarifaRetorno['prazoEntrega']+1);
                        $data_previsao = date('d/m/Y', $time_previsao);
                        $this->Encomenda->id = $i['Encomenda']['id'];
                        $this->Encomenda->saveField('data_previsao', $data_previsao);
                    }
                }
                if (!is_null($data_previsao)){
                    $this->out("OK");
                } else {
                    $this->out("FALHA");
                }
            }
            $total = count($encomendas);
            if ($total>=100){
                $page++;
                goto captura_micropost_previsao;
            }
        }
    }
    
    public function captura_micropost_orcamentos(){
        $this->out("Captura Rastreios Micropost...");
        $clientes_micropost = $this->Conta->find('all', [
            'conditions' => [
                'Cliente.embarcador' => 1,
            ],
        ]);
        foreach ($clientes_micropost as $cm){
            $this->out("Cliente MICROPOST: {$cm['Cliente']['fantasia']}");
            
            // POSTAGEM
            $page = 1;
            captura_micropost_postagem:
            $this->out("Capturando Previsão...Bloco {$page}");
            $encomendas = $this->Encomenda->find('all', ['conditions' => [
                'Encomenda.embarcador_id' => $cm['Cliente']['id'],
                'Encomenda.tipo_encomenda_id' => 1,
                'Transportador.correios' => 1,
                'Encomenda.cancelado IS NULL',
                'Encomenda.codigo_rastreamento IS NOT NULL',
                'Encomenda.servico_transportador IS NULL',
            ], 'limit' => 100, 'page' => $page]);
            foreach ($encomendas as $i){
                $this->out("Consultando Postagem MICROPOST: {$i['Encomenda']['codigo_rastreamento']}");
                $reply = $this->captura_micropost_postagem($cm, $i['Encomenda']['codigo_rastreamento']);
                if (@$reply['ObjetoPLP']['numeroPLP']=='0') continue;
                if (isset($reply['ObjetoPLP']['numeroPLP'])){
                    $reply['ObjetoPLP'] = [$reply['ObjetoPLP']];
                }
                foreach ($reply['ObjetoPLP'] as $PLP){
                    $e = $this->Encomenda->find('first', ['conditions' => [
                        'Encomenda.embarcador_id' => $cm['Cliente']['id'],
                        'Encomenda.tipo_encomenda_id' => 1,
                        'Transportador.correios' => 1,
                        'Encomenda.cancelado IS NULL',
                        'Encomenda.codigo_rastreamento' => $PLP['registro'],
                        'Encomenda.servico_transportador IS NULL',
                    ]]);
                    if (!empty($e['Encomenda']['id'])){
                        $this->Encomenda->id = $e['Encomenda']['id'];
                        $this->Encomenda->saveField('servico_transportador', $PLP['codServico']);
                        $this->Encomenda->saveField('valor_frete', FloatFromSQL($PLP['valor']));
                    }
                }
            }
            $total = count($encomendas);
            if ($total>=100){
                $page++;
                goto captura_micropost_postagem;
            }
        }
    }
    
    public function captura_micropost_rastreios(){
        $this->out("Captura Rastreios Micropost...");
        $clientes_micropost = $this->Conta->find('all', [
            'conditions' => [
                'Cliente.embarcador' => 1,
            ],
        ]);
        foreach ($clientes_micropost as $cm){
            $this->out("Cliente MICROPOST: {$cm['Cliente']['fantasia']}");
            
            //CÓDIGO RASTREIO
            $page = 1;
            captura_micropost_codigo:
            $this->out("Capturando Rastreios...Bloco {$page}");
            $encomendas = $this->Encomenda->find('all', ['conditions' => [
                'Encomenda.embarcador_id' => $cm['Cliente']['id'],
                'Encomenda.tipo_encomenda_id' => 1,
                'Transportador.correios' => 1,
                'Encomenda.cancelado IS NULL',
                'Encomenda.codigo_rastreamento IS NULL',
            ], 'limit' => 100, 'page' => $page]);
            foreach ($encomendas as $i){
                $this->out("Consultando rastreio para NF {$i['Encomenda']['nfe_numero']}: ", false);
                $reply = (array)$this->captura_micropost_codigo($cm, addLeading($i['Encomenda']['nfe_numero'], 6));
                $codigo = $reply[0];
                $prefixo = substr($codigo, 0, 2);
                $sufixo = substr($codigo, 2, 9);
                if (is_numeric($sufixo) && !is_numeric($prefixo) && strlen($codigo)==11){
                    $codigo .= "BR";
                    $this->Encomenda->setCodigoRastreamento($i['Encomenda']['id'], $codigo);
                    if (empty($i['Encomenda']['data_coleta'])) $this->Encomenda->setDataColeta($i['Encomenda']['id'], date('d/m/Y'));
                    $this->Encomenda->setStatusId($i['Encomenda']['id'], 1);
                    $this->out($codigo);
                } else {
                    $reply = (array)$this->captura_micropost_codigo($cm, $i['Encomenda']['nfe_numero']);
                    $codigo = $reply[0];
                    $prefixo = substr($codigo, 0, 2);
                    $sufixo = substr($codigo, 2, 9);
                    if (is_numeric($sufixo) && !is_numeric($prefixo) && strlen($codigo)==11){
                        $codigo .= "BR";
                        $this->Encomenda->setCodigoRastreamento($i['Encomenda']['id'], $codigo);
                        if (empty($i['Encomenda']['data_coleta'])) $this->Encomenda->setDataColeta($i['Encomenda']['id'], date('d/m/Y'));
                        $this->Encomenda->setStatusId($i['Encomenda']['id'], 1);
                        $this->out($codigo);
                    } else {
                        $this->out("FALHA: {$codigo}");
                    }
                }
            }
            $total = count($encomendas);
            if ($total>=100){
                $page++;
                goto captura_micropost_codigo;
            }
        }
    }
    
}