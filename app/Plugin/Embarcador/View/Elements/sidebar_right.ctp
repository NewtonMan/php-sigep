<?php
$this->Status = ClassRegistry::init('Embarcador.Status');
$this->City = ClassRegistry::init('Embarcador.City');
$this->Zone = ClassRegistry::init('Embarcador.Zone');
$this->State = ClassRegistry::init('Embarcador.State');
$this->Encomenda = ClassRegistry::init('Embarcador.Encomenda');

$embarcador_id = (isset($embarcador_id) ? $embarcador_id:null);
if (!empty($embarcador_id)){
    $transportador_id = (isset($_GET['transportador_id']) ? (empty($_GET['transportador_id']) ? null:$_GET['transportador_id']):null);
    $city_id = (isset($_GET['city_id']) ? $_GET['city_id']:null);
    $zone_id = (isset($_GET['zone_id']) ? $_GET['zone_id']:null);
    $state_id = (isset($_GET['state_id']) ? $_GET['state_id']:null);
    $stage_id = (isset($_GET['stage_id']) ? $_GET['stage_id']:null);

    $criterios = $this->request->data['criterios'];

    $stages = [
        1 => 'Não coletados',
        8 => 'Coletado, sem dados de rastreio',
        2 => 'Em transporte, sem previsão de entrega',
        3 => 'Em transporte, no prazo',
        4 => 'Em transporte, atrasado',
        5 => 'Tratar Ocorrências',
        6 => 'Entregues no prazo',
        7 => 'Entregues com atraso',
    ];

    $totais = [
        1 => $this->Encomenda->find('count', [
            'conditions' => [
                $criterios,
                $this->request->data['stage_criterios'][1],
            ],
        ]),
        2 => $this->Encomenda->find('count', [
            'conditions' => [
                $criterios,
                $this->request->data['stage_criterios'][2],
            ],
        ]),
        3 => $this->Encomenda->find('count', [
            'conditions' => [
                $criterios,
                $this->request->data['stage_criterios'][3],
            ],
        ]),
        4 => $this->Encomenda->find('count', [
            'conditions' => [
                $criterios,
                $this->request->data['stage_criterios'][4],
            ],
        ]),
        5 => $this->Encomenda->find('count', [
            'conditions' => [
                $criterios,
                $this->request->data['stage_criterios'][5],
            ],
        ]),
        6 => $this->Encomenda->find('count', [
            'conditions' => [
                $criterios,
                $this->request->data['stage_criterios'][6],
            ],
        ]),
        7 => $this->Encomenda->find('count', [
            'conditions' => [
                $criterios,
                $this->request->data['stage_criterios'][7],
            ],
        ]),
        8 => $this->Encomenda->find('count', [
            'conditions' => [
                $criterios,
                $this->request->data['stage_criterios'][8],
            ],
        ]),
    ];

    $this->Destino->displayField = 'fantasia';
    $transportadores = array_merge(
        [
            [
                'Destino' => [
                    'id' => 0,
                    'fantasia' => 'Sem Transportadora'
                ]
            ]
        ],
        $this->Destino->find('all', ['conditions' => [
            'Destino.transportador' => 1,
            'Destino.embarcador' => 1,
        ], 'order' => [
            'Destino.fantasia' => 'ASC',
        ]])
    );
    foreach ($transportadores as $x => $t){
        $criterios[] ='Encomenda.cancelado IS NULL';
        $criterios['Encomenda.transportador_id'] = (empty($t['Destino']['id']) ? null:$t['Destino']['id']);
        $transportadores[$x]['count'] = $this->Encomenda->find('count', [
            'conditions' => [
                $criterios,
                (!empty($stage_id) ? $this->request->data['stage_criterios'][$stage_id]:[]),
            ],
        ]);
    }

    $base_url = @"/embarcador/encomendas/monitoramento/{$embarcador_id}?state_id={$state_id}&zone_id={$zone_id}&city_id={$city_id}&transportador_id={$transportador_id}&dest={$_GET['dest']}&nf={$_GET['nfe']}&crudSearch={$_GET['crudSearch']}&stage_id=";
    ?>
    <form method="get" action="/embarcador/encomendas/<?= $this->request->params['action'] ?>/<?=$embarcador_id?>">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title">Situação Consolidada</h1>
            </div>
            <div class="list-group">
                <a href="<?=$base_url?>" class="list-group-item">
                    Qualquer situação
                </a>
                <?php
                foreach ($stages as $stage_id => $stage_name){
                    $base_url = @"/embarcador/encomendas/monitoramento/{$embarcador_id}?state_id={$state_id}&zone_id={$zone_id}&city_id={$city_id}&transportador_id={$transportador_id}&dest={$_GET['dest']}&nf={$_GET['nfe']}&crudSearch={$_GET['crudSearch']}&stage_id=";
                ?>
                <a href="<?=$base_url . $stage_id?>" class="list-group-item<?=(@$_GET['stage_id']==$stage_id ? ' active':'')?>">
                    <span class="badge badge-default"><?=$totais[$stage_id]?></span>
                    <?=$stage_name?>
                </a>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title">Transportadoras</h1>
            </div>
            <div class="list-group">
                <?php
                $transportador_id = null;
                $base_url = @"/embarcador/encomendas/monitoramento/{$embarcador_id}?state_id={$state_id}&zone_id={$zone_id}&city_id={$city_id}&transportador_id={$transportador_id}&dest={$_GET['dest']}&nf={$_GET['nfe']}&crudSearch={$_GET['crudSearch']}&stage_id={$_GET['stage_id']}";
                ?>
                <a href="<?=$base_url?>" class="list-group-item">
                    Todas as Transportadoras
                </a>
                <?php
                foreach ($transportadores as $t){
                    $transportador_id = $t['Destino']['id'];
                    $base_url = @"/embarcador/encomendas/monitoramento/{$embarcador_id}?state_id={$state_id}&zone_id={$zone_id}&city_id={$city_id}&transportador_id={$transportador_id}&dest={$_GET['dest']}&nf={$_GET['nfe']}&crudSearch={$_GET['crudSearch']}&stage_id={$_GET['stage_id']}";
                ?>
                <a href="<?=$base_url?>" class="list-group-item<?=(isset($_GET['transportador_id']) && "{$_GET['transportador_id']}"==="{$t['Destino']['id']}" ? ' active':'')?>">
                    <span class="badge badge-default"><?=$t['count']?></span>
                    <?=$t['Destino']['fantasia']?>
                </a>
                <?php
                }
                ?>
            </div>
        </div>
    </form>
<?php
}
?>