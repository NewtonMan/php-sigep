<?php
require_once ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';

use NFePHP\DA\NFe\Danfe;
use NFePHP\DA\Legacy\FilesFolders;

class AnaliticoController extends EmbarcadorAppController {
    
    public $uses = ['Embarcador.Encomenda', 'Embarcador.State', 'Embarcador.Zone', 'Embarcador.City', 'Cadastros.Destino', 'Embarcador.GerenciarSla', 'Embarcador.TmsRegion'];
    
    public function index(){
        $this->setRefer();
        $criterios = $this->crudModelSearch('Destino');
        $criterios['Destino.cliente'] = 1;
        $criterios['Destino.embarcador'] = 1;
        $this->request->data['lista'] = $this->paginate('Destino', $criterios);
    }
    
    public function calendario($cliente_id){
        $periodos = [];
        $total = 0;
        $finalizadas = 0;
        $pendentes = 0;
        $ocorrencias = 0;
        $fator = 0;
        for ($x=0; $x<12; $x++) {
            $data1 = date('Y-m', time() - 60*60*24*30*$x) . '-01';
            $data2 = date('Y-m-t', time() - 60*60*24*30*$x);
            $dados = [
                'total' => $this->Encomenda->find('count', ['conditions' => [
                    'Encomenda.embarcador_id' => $cliente_id,
                    'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                    'Encomenda.cancelado IS NULL',
                ]]),
                'finalizadas' => $this->Encomenda->find('count', ['conditions' => [
                    'Encomenda.embarcador_id' => $cliente_id,
                    'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                    'Encomenda.data_conclusao IS NOT NULL',
                    'Encomenda.cancelado IS NULL',
                ]]),
                'pendentes' => $this->Encomenda->find('count', ['conditions' => [
                    'Encomenda.embarcador_id' => $cliente_id,
                    'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                    'Encomenda.data_conclusao IS NULL',
                    'Encomenda.cancelado IS NULL',
                ]]),
                'ocorrencias' => $this->Encomenda->find('count', ['conditions' => [
                    'Encomenda.embarcador_id' => $cliente_id,
                    'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                    'Status.ocorrencia' => 1,
                    'Encomenda.cancelado IS NULL',
                ]]),
            ];
            $periodos["{$data1}|{$data2}"] = $dados;
            $total += $dados['total'];
            $finalizadas += $dados['finalizadas'];
            $pendentes += $dados['pendentes'];
            $ocorrencias += $dados['ocorrencias'];
            if ($dados['total']>0){
                $fator++;
            }
        }
        $media = [
            'total' => $total / $fator,
            'finalizadas' => $finalizadas / $fator,
            'pendentes' => $pendentes / $fator,
            'ocorrencias' => $ocorrencias / $fator,
        ];
        $this->set(compact('periodos', 'cliente_id', 'total', 'finalizados', 'pendentes', 'ocorrencias', 'media'));
    }
    
    public function relatorio($cliente_id, $data1, $data2) {
        $c = $this->Destino->read(null, $cliente_id);
        $this->crumbs[] = [
            'name' => $c['Destino']['fantasia'],
            'href' => "/embarcador/analitico/calendario/{$c['Destino']['id']}",
            'active' => false,
        ];
        $this->crumbs[] = [
            'name' => "Período de {$data1} até {$data2}",
            'href' => '#',
            'active' => true,
        ];
        $transportadoras = $this->Destino->find('all', ['conditions' => [
            'Destino.transportador' => 1,
            'Destino.embarcador' => 1,
        ]]);
        $charts[0][] = [
            'Transportadora',
            'Encomendas',
        ];
        foreach ($transportadoras as $x => $t){
            $charts[0][] = [
                "{$t['Destino']['fantasia']}",
                $this->Encomenda->find('count', ['conditions' => [
                    'Encomenda.embarcador_id' => $cliente_id,
                    'Encomenda.transportador_id' => $t['Destino']['id'],
                    'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                    'Encomenda.cancelado IS NULL',
                ]]),
            ];
            
            $total = new stdClass();
            $total->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $t['Destino']['id'],
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.cancelado IS NULL',
            ]]);
            $total->f = number_format($total->v, 0, ',', '.');
            
            $finalizadas = new stdClass();
            $finalizadas->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $t['Destino']['id'],
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
            ]]);
            $finalizadas->f = number_format($finalizadas->v, 0, ',', '.');
            
            $pendentes = new stdClass();
            $pendentes->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $t['Destino']['id'],
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NULL',
                'Encomenda.cancelado IS NULL',
            ]]);
            $pendentes->f = number_format($pendentes->v, 0, ',', '.');
            
            $ocorrencias = new stdClass();
            $ocorrencias->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $t['Destino']['id'],
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NULL',
                'Status.ocorrencia' => 1,
                'Encomenda.cancelado IS NULL',
            ]]);
            $ocorrencias->f = number_format($ocorrencias->v, 0, ',', '.');
            
            $charts[1][] = [
                "{$t['Destino']['fantasia']}",
                $total,
                $finalizadas,
                $pendentes,
                $ocorrencias,
                "<a href=\"/embarcador/analitico/performance_sla/{$cliente_id}/{$data1}/{$data2}/{$t['Destino']['id']}\" class=\"btn btn-default\">SLA</a>" . 
                "<a href=\"/embarcador/analitico/performance/{$cliente_id}/{$data1}/{$data2}/{$t['Destino']['id']}\" class=\"btn btn-default\">Performance</a>" . 
                "<a href=\"/embarcador/analitico/capilaridade/{$cliente_id}/{$data1}/{$data2}/{$t['Destino']['id']}\" class=\"btn btn-default\">Capilaridade</a>"
            ];
        }
        $this->set(compact('transportadoras', 'data1', 'data2', 'charts'));
    }
    
    public function performance($cliente_id, $data1, $data2, $transportador_id) {
        $c = $this->Destino->read(null, $cliente_id);
        $t = $this->Destino->read(null, $transportador_id);
        
        $this->crumbs[] = [
            'name' => $c['Destino']['fantasia'],
            'href' => "/embarcador/analitico/calendario/{$c['Destino']['id']}",
            'active' => false,
        ];
        $this->crumbs[] = [
            'name' => "Período de {$data1} até {$data2}",
            'href' => "/embarcador/analitico/relatorio/{$c['Destino']['id']}/{$data1}/{$data2}",
            'active' => false,
        ];
        $this->crumbs[] = [
            'name' => "PERFORMANCE {$t['Destino']['fantasia']}",
            'href' => '#',
            'active' => true,
        ];
        
        $estados = $this->State->find('all');
        $media = [];
        $tforaParzo = 0;
        $tentregas = 0;
        $tot = 0;
        foreach ($estados as $e) {
            // CAPITAIS
            $total = new stdClass();
            $total->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
            ]]);
            $total->f = number_format($total->v, 0, ',', '.');
            
            $antesPrazo = new stdClass();
            $antesPrazo->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
                'Encomenda.data_conclusao < Encomenda.data_previsao',
            ]]);
            $antesPrazo->f = number_format($antesPrazo->v, 0, ',', '.');
            
            $noPrazo = new stdClass();
            $noPrazo->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
                'Encomenda.data_conclusao = Encomenda.data_previsao',
            ]]);
            $noPrazo->f = number_format($noPrazo->v, 0, ',', '.');
            
            $foraPrazo = new stdClass();
            $foraPrazo->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
                'Encomenda.data_conclusao > Encomenda.data_previsao',
            ]]);
            $foraPrazo->f = number_format($foraPrazo->v, 0, ',', '.');
            
            $per = new stdClass();
            if ($total->v==0){
                $per->v = 0;
            } else {
                $per->v = (((100 / $total->v)) * (($antesPrazo->v) + ($noPrazo->v)));
            }
            $per->f = number_format($per->v, 1, ',', '.').'%';
            
            $tentregas += $total->v;
            $tforaParzo += $foraPrazo->v;
            
            if ($total->v>0){
                $media[] = $per->v;
                $tot += $per->v;
            }
            
            if ($total->v>0){
                $charts[0][] = [
                    utf8_encode($e['State']['name']) . " - CAPITAL",
                    $per,
                    $total,
                    $antesPrazo,
                    $noPrazo,
                    $foraPrazo,
                ];
            }
            
            // INTERIOR
            $total = new stdClass();
            $total->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun NOT IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
            ]]);
            $total->f = number_format($total->v, 0, ',', '.');
            
            $antesPrazo = new stdClass();
            $antesPrazo->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun NOT IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
                'Encomenda.data_conclusao < Encomenda.data_previsao',
            ]]);
            $antesPrazo->f = number_format($antesPrazo->v, 0, ',', '.');
            
            $noPrazo = new stdClass();
            $noPrazo->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun NOT IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
                'Encomenda.data_conclusao = Encomenda.data_previsao',
            ]]);
            $noPrazo->f = number_format($noPrazo->v, 0, ',', '.');
            
            $foraPrazo = new stdClass();
            $foraPrazo->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun NOT IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
                'Encomenda.data_conclusao > Encomenda.data_previsao',
            ]]);
            $foraPrazo->f = number_format($foraPrazo->v, 0, ',', '.');
            
            $per = new stdClass();
            if ($total->v==0){
                $per->v = 0;
            } else {
                $per->v = (((100 / $total->v)) * (($antesPrazo->v) + ($noPrazo->v)));
            }
            $per->f = number_format($per->v, 1, ',', '.').'%';
            
            $tentregas += $total->v;
            $tforaParzo += $foraPrazo->v;
            
            if ($total->v>0){
                $media[] = $per->v;
                $tot += $per->v;
            }
            
            if ($total->v>0){
                $charts[0][] = [
                    utf8_encode($e['State']['name']) . " - INTERIOR",
                    $per,
                    $total,
                    $antesPrazo,
                    $noPrazo,
                    $foraPrazo,
                ];
            }
        }
        //die(print_r($charts));
        $mitens = count($media);
        $performance = number_format($tot / $mitens, 1, '.', '');
        $performanceTotal = 100 - ((100 / $tentregas) * $tforaParzo);
        $performanceTotal = number_format($performanceTotal, 1, '.', '');
        
        $this->set(compact('transportadoras', 'data1', 'data2', 'charts', 'performance', 'performanceTotal'));
    }
    
    public function performance_sla($cliente_id, $data1, $data2, $transportador_id) {
        $c = $this->Destino->read(null, $cliente_id);
        $t = $this->Destino->read(null, $transportador_id);
        
        $this->crumbs[] = [
            'name' => $c['Destino']['fantasia'],
            'href' => "/embarcador/analitico/calendario/{$c['Destino']['id']}",
            'active' => false,
        ];
        $this->crumbs[] = [
            'name' => "Período de {$data1} até {$data2}",
            'href' => "/embarcador/analitico/relatorio/{$c['Destino']['id']}/{$data1}/{$data2}",
            'active' => false,
        ];
        $this->crumbs[] = [
            'name' => "SLA {$t['Destino']['fantasia']}",
            'href' => '#',
            'active' => true,
        ];
        
        $estados = $this->State->find('all');
        $media = [];
        $tforaParzo = 0;
        $tentregas = 0;
        $tot = 0;
        foreach ($estados as $e) {
            // CAPITAIS
            $total = new stdClass();
            $total->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
            ]]);
            $total->f = number_format($total->v, 0, ',', '.');
            
            $antesPrazo = new stdClass();
            $antesPrazo->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
                'Encomenda.data_conclusao < DATE_ADD(Encomenda.created, INTERVAL 11 DAY)',
            ]]);
            $antesPrazo->f = number_format($antesPrazo->v, 0, ',', '.');
            
            $noPrazo = new stdClass();
            $noPrazo->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
                'Encomenda.data_conclusao = DATE_ADD(Encomenda.created, INTERVAL 11 DAY)',
            ]]);
            $noPrazo->f = number_format($noPrazo->v, 0, ',', '.');
            
            $foraPrazo = new stdClass();
            $foraPrazo->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
                'Encomenda.data_conclusao > DATE_ADD(Encomenda.created, INTERVAL 11 DAY)',
            ]]);
            $foraPrazo->f = number_format($foraPrazo->v, 0, ',', '.');
            
            $per = new stdClass();
            if ($total->v==0){
                $per->v = 0;
            } else {
                $per->v = (((100 / $total->v)) * (($antesPrazo->v) + ($noPrazo->v)));
            }
            $per->f = number_format($per->v, 1, ',', '.').'%';
            
            $tentregas += $total->v;
            $tforaParzo += $foraPrazo->v;
            
            if ($total->v>0){
                $media[] = $per->v;
                $tot += $per->v;
            }
            
            if ($total->v>0){
                $charts[0][] = [
                    utf8_encode($e['State']['name']) . " - CAPITAL",
                    $per,
                    $total,
                    $antesPrazo,
                    $noPrazo,
                    $foraPrazo,
                ];
            }
            
            // INTERIOR
            $total = new stdClass();
            $total->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun NOT IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
            ]]);
            $total->f = number_format($total->v, 0, ',', '.');
            
            $antesPrazo = new stdClass();
            $antesPrazo->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun NOT IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
                'Encomenda.data_conclusao < DATE_ADD(Encomenda.created, INTERVAL 13 DAY)',
            ]]);
            $antesPrazo->f = number_format($antesPrazo->v, 0, ',', '.');
            
            $noPrazo = new stdClass();
            $noPrazo->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun NOT IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
                'Encomenda.data_conclusao = DATE_ADD(Encomenda.created, INTERVAL 13 DAY)',
            ]]);
            $noPrazo->f = number_format($noPrazo->v, 0, ',', '.');
            
            $foraPrazo = new stdClass();
            $foraPrazo->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun NOT IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
                'Encomenda.data_conclusao > DATE_ADD(Encomenda.created, INTERVAL 13 DAY)',
            ]]);
            $foraPrazo->f = number_format($foraPrazo->v, 0, ',', '.');
            
            $per = new stdClass();
            if ($total->v==0){
                $per->v = 0;
            } else {
                $per->v = (((100 / $total->v)) * (($antesPrazo->v) + ($noPrazo->v)));
            }
            $per->f = number_format($per->v, 1, ',', '.').'%';
            
            $tentregas += $total->v;
            $tforaParzo += $foraPrazo->v;
            
            if ($total->v>0){
                $media[] = $per->v;
                $tot += $per->v;
            }
            
            if ($total->v>0){
                $charts[0][] = [
                    utf8_encode($e['State']['name']) . " - INTERIOR",
                    $per,
                    $total,
                    $antesPrazo,
                    $noPrazo,
                    $foraPrazo,
                ];
            }
        }
        //die(print_r($charts));
        $mitens = count($media);
        $performance = number_format($tot / $mitens, 1, '.', '');
        $performanceTotal = 100 - ((100 / $tentregas) * $tforaParzo);
        $performanceTotal = number_format($performanceTotal, 1, '.', '');
        
        $this->set(compact('transportadoras', 'data1', 'data2', 'charts', 'performance', 'performanceTotal'));
    }
    
    public function capilaridade($cliente_id, $data1, $data2, $transportador_id) {
        $c = $this->Destino->read(null, $cliente_id);
        $t = $this->Destino->read(null, $transportador_id);
        
        $this->crumbs[] = [
            'name' => $c['Destino']['fantasia'],
            'href' => "/embarcador/analitico/calendario/{$c['Destino']['id']}",
            'active' => false,
        ];
        $this->crumbs[] = [
            'name' => "Período de {$data1} até {$data2}",
            'href' => "/embarcador/analitico/relatorio/{$c['Destino']['id']}/{$data1}/{$data2}",
            'active' => false,
        ];
        $this->crumbs[] = [
            'name' => "CAPILARIDADE {$t['Destino']['fantasia']}",
            'href' => '#',
            'active' => true,
        ];
        
        $estados = $this->State->find('all');
        foreach ($estados as $e) {
            // CAPITAIS
            $total = new stdClass();
            $total->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.cancelado IS NULL',
            ]]);
            $total->f = number_format($total->v, 0, ',', '.');
            
            $finalizadas = new stdClass();
            $finalizadas->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
            ]]);
            $finalizadas->f = number_format($finalizadas->v, 0, ',', '.');
            
            $pendentes = new stdClass();
            $pendentes->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NULL',
                'Encomenda.cancelado IS NULL',
            ]]);
            $pendentes->f = number_format($pendentes->v, 0, ',', '.');
            
            $ocorrencias = new stdClass();
            $ocorrencias->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NULL',
                'Status.ocorrencia' => 1,
                'Encomenda.cancelado IS NULL',
            ]]);
            $ocorrencias->f = number_format($ocorrencias->v, 0, ',', '.');
            
            if ($total->v>0){
                $charts[0][] = [
                    "{$e['State']['name']} - Capital",
                    $total,
                    $finalizadas,
                    $pendentes,
                    $ocorrencias,
                ];
            }
            
            // INTERIOR
            $itotal = new stdClass();
            $itotal->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun NOT IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.cancelado IS NULL',
            ]]);
            $itotal->f = number_format($itotal->v, 0, ',', '.');
            
            $ifinalizadas = new stdClass();
            $ifinalizadas->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun NOT IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NOT NULL',
                'Encomenda.cancelado IS NULL',
            ]]);
            $ifinalizadas->f = number_format($ifinalizadas->v, 0, ',', '.');
            
            $ipendentes = new stdClass();
            $ipendentes->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun NOT IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NULL',
                'Encomenda.cancelado IS NULL',
            ]]);
            $ipendentes->f = number_format($ipendentes->v, 0, ',', '.');
            
            $iocorrencias = new stdClass();
            $iocorrencias->v = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $cliente_id,
                'Encomenda.transportador_id' => $transportador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun NOT IN (SELECT id FROM embarcador_cities WHERE capital=1)',
                'Encomenda.created BETWEEN ? AND ?' => ["{$data1} 00:00:00", "{$data2} 23:59:59"],
                'Encomenda.data_conclusao IS NULL',
                'Status.ocorrencia' => 1,
                'Encomenda.cancelado IS NULL',
            ]]);
            $iocorrencias->f = number_format($iocorrencias->v, 0, ',', '.');
            
            if ($itotal->v>0){
                $charts[0][] = [
                    "{$e['State']['name']} - Interior",
                    $itotal,
                    $ifinalizadas,
                    $ipendentes,
                    $iocorrencias,
                ];
            }
        }
        $this->set(compact('transportadoras', 'data1', 'data2', 'charts'));
    }
    
    public function gerenciar_sla($embarcador_id){
        Configure::write('debug', 2);
        $transportador = $this->Destino->find('all', ['conditions' => [
            'Destino.embarcador' => 1,
            'Destino.transportador' => 1,
        ]]);
        $dados = $this->Encomenda->find('first', ['conditions' => [
            'Encomenda.embarcador_id' => $embarcador_id,
        ]]);
        /*$estados = $this->State->find('all');
        foreach($estados as $e){
            /*
            $regiao = new stdClass();
            $regiao->c = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $embarcador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun IN (SELECT id FROM embarcador_cities WHERE capital=1)',
            ]]);
            if($regiao->c > 0 ) $lista[] = $e['State']['name'] . ' - CAPITAL';
            
            $regiao = new stdClass();
            $regiao->i = $this->Encomenda->find('count', ['conditions' => [
                'Encomenda.embarcador_id' => $embarcador_id,
                'LocalEntrega.UF' => $e['State']['letter'],
                'LocalEntrega.cMun NOT IN (SELECT id FROM embarcador_cities WHERE capital=1)',
            ]]);
            if($regiao->i > 0 ) $lista[] = $e['State']['name']. ' - INTERIOR';
             
        }*/
        $this->set(compact('transportador', 'dados'));
    }
    
    public function tabela_sla($embarcador_id, $transportador_id){
        Configure::write('debug', 2);
        if($this->request->is('post') || $this->request->is('put')){
            $data = $this->request->data;
            print_r($data);
        }
        $transportador = $this->Destino->find('first', ['conditions' => [
            'Destino.id' => $transportador_id,
        ]]);
        $dados = $this->Encomenda->find('first', ['conditions' => [
            'Encomenda.embarcador_id' => $embarcador_id,
        ]]);
        $lista = $this->TmsRegion->find('all', ['order' => [
            'TmsRegion.nome' => 'ASC',
        ]]);
        $this->set(compact('transportador', 'dados', 'lista'));
    }
    
}