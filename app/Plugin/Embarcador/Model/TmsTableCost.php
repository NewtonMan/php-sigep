<?php
class TmsTableCost extends EmbarcadorAppModel {
    
    public $useTable = 'embarcador_tms_table_costs';
    
    public $belongsTo = [
        'Transportadora' => [
            'className' => 'Cadastros.Destino',
            'foreignKey' => 'transportadora_id',
        ],
        'Embarcadora' => [
            'className' => 'Cadastros.Destino',
            'foreignKey' => 'embarcadora_id',
        ],
        'Modal' => [
            'className' => 'Embarcador.Modal',
            'foreignKey' => 'embarcador_tms_model_id',
        ],
        'TmsTableCostMethod' => [
            'className' => 'Embarcador.TmsTableCostMethod',
            'foreignKey' => 'embarcador_tms_cost_method_id',
        ],
    ];
    
    private function importarTmsTableCostRangeID($embarcador_tms_cost_id, $from, $to, $create=true){
        $this->TmsTableCostRange = ClassRegistry::init('Embarcador.TmsTableCostRange');
        $data = $this->TmsTableCostRange->find('first', ['conditions' => [
            'TmsTableCostRange.embarcador_tms_cost_id' => $embarcador_tms_cost_id,
            'TmsTableCostRange.range_from' => FloatToSQL($from),
            'TmsTableCostRange.range_to' => FloatToSQL($to),
        ]]);
        if (!empty($data['TmsTableCostRange']['id'])){
            return $data['TmsTableCostRange']['id'];
        } elseif ($create) {
            $data = [
                'TmsTableCostRange' => [
                    'embarcador_tms_cost_id' => $embarcador_tms_cost_id,
                    'range_from' => $from,
                    'range_to' => $to,
                ],
            ];
            $this->TmsTableCostRange->create();
            $this->TmsTableCostRange->save($data);
            return $this->TmsTableCostRange->id;
        } else {
            return null;
        }
    }
    
    private function importarTmsTableCostRouteID($embarcador_tms_cost_id, $cep_start, $cep_end, $days_cost, $value_exced, $create=true){
        $this->TmsTableCostRoute = ClassRegistry::init('Embarcador.TmsTableCostRoute');
        $data = $this->TmsTableCostRoute->find('first', ['conditions' => [
            'TmsTableCostRoute.embarcador_tms_cost_id' => $embarcador_tms_cost_id,
            'TmsTableCostRoute.cep_start' => $cep_start,
            'TmsTableCostRoute.cep_end' => $cep_end,
        ]]);
        if (!empty($data['TmsTableCostRoute']['id'])){
            $this->TmsTableCostRoute->id = $data['TmsTableCostRoute']['id'];
            
            if ($days_cost != $data['TmsTableCostRoute']['days'])
                $this->TmsTableCostRoute->saveField('days', $days_cost);
            
            if (FloatFromSQL($value_exced) != $data['TmsTableCostRoute']['value_exced'])
                $this->TmsTableCostRoute->saveField('value_exced', FloatFromSQL($value_exced));
            
            return $data['TmsTableCostRoute']['id'];
        } elseif ($create) {
            $data = [
                'TmsTableCostRoute' => [
                    'embarcador_tms_cost_id' => $embarcador_tms_cost_id,
                    'range_from' => $cep_start,
                    'range_to' => $cep_end,
                    'days' => $days_cost,
                    'value_exced' => $value_exced,
                ],
            ];
            $this->TmsTableCostRoute->create();
            $this->TmsTableCostRoute->save($data);
            return $this->TmsTableCostRoute->id;
        } else {
            return null;
        }
    }
    
    public function importarPanilhaFaixaCep($embarcador_tms_cost_id, $arquivo){
        if(file_exists($arquivo)){
            $ext = strtoupper(pathinfo($arquivo, PATHINFO_EXTENSION));
            if ($ext==='CSV'){
                $this->TmsTableCostValue = ClassRegistry::init('Embarcador.TmsTableCostValue');
                $linhas = file($arquivo);
                foreach ($linhas as $x => $linha) {
                    if ($x==0) continue;
                    $colunas = explode(';', trim($linha));
                    $total = count($colunas);
                    if ($total==8){
                        $cep_start = (int)onlyNumbers($colunas[0]);
                        $cep_end = (int)onlyNumbers($colunas[1]);
                        $range_from = (float)FloatToSQL($colunas[2]);
                        $range_from = number_format($range_from, 3, ',', '');
                        $range_to = (float)FloatToSQL($colunas[3]);
                        $range_to = number_format($range_to, 3, ',', '');
                        $value = (float)FloatToSQL($colunas[4]);
                        $percent = (float)FloatToSQL(trim(str_replace('%', '', $colunas[5])));
                        $value_exced = (float)FloatToSQL(trim(str_replace('R$', '', $colunas[6])));
                        $days = (int)onlyNumbers($colunas[7]);
                        $range_id = $this->importarTmsTableCostRangeID($embarcador_tms_cost_id, $range_from, $range_to);
                        $route_id = $this->importarTmsTableCostRouteID($embarcador_tms_cost_id, $cep_start, $cep_end, $days, $value_exced);
                        $tcv = $this->TmsTableCostValue->find('first', ['conditions' => [
                            'TmsTableCostValue.embarcador_tms_cost_id' => $embarcador_tms_cost_id,
                            'TmsTableCostValue.embarcador_tms_cost_range_id' => $range_id,
                            'TmsTableCostValue.embarcador_tms_cost_route_id' => $route_id,
                        ]]);
                        if (!empty($tcv['TmsTableCostValue']['id'])) {
                            $this->TmsTableCostValue->id = $tcv['TmsTableCostValue']['id'];
                            $this->TmsTableCostValue->saveField('value', FloatFromSQL($value));
                            $this->TmsTableCostValue->saveField('percent', FloatFromSQL($percent));
                        } else {
                            $this->TmsTableCostValue->create();
                            $this->TmsTableCostValue->save([
                                'TmsTableCostValue' => [
                                    'embarcador_tms_cost_id' => $embarcador_tms_cost_id,
                                    'embarcador_tms_cost_range_id' => $range_id,
                                    'embarcador_tms_cost_route_id' => $route_id,
                                    'value' => FloatFromSQL($value),
                                    'percent' => FloatFromSQL($percent),
                                ],
                            ]);
                        }
                    }
                }
                return true;
            } else {
                return 'Só é permitido arquivo CSV.';
            }
        } else {
            return 'Arquivo não existe';
        }
    }
    
}