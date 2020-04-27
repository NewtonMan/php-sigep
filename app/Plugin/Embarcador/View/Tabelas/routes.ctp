<?=$this->Session->flash();?>
<?=$this->Form->create('TmsTableCostRoute', ['type' => 'file']);?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title">Formulário para Faixas/Intervalos de Tabelas</h1>
    </div>
    <div class="panel-body">
        
    </div>
    <table class="table table-striped table-condensed table-ranges">
        <thead>
            <tr>
                <th class='col-xs-1 text-center'>Estado</th>
                <th class='col-xs-2 text-center'>Região</th>
                <th class='col-xs-2 text-center'>Cidade</th>
                <th class='col-xs-2 text-center'>CEP Start</th>
                <th class='col-xs-2 text-center'>CEP End</th>
                <th class='col-xs-1 text-center'>Prazo</th>
                <th class='col-xs-2 text-center'></th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($this->request->data as $x => $r) {
            ?>
            <tr id="id-range-<?=$x?>">
                <td>
                    <?=$this->Form->hidden("$x.TmsTableCostRoute.id");?>
                    <?=$this->Form->hidden("$x.TmsTableCostRoute.excluir", ['class' => 'route-excluir']);?>
                    <?=$this->Form->input("$x.TmsTableCostRoute.state_id", ['label' => false, 'class' => 'route-state', 'onchange' => 'RouteStateChange(\'id-route-'.$x.'\');', 'empty' => ' - Todos os Estados - ', 'options' => $states]);?>
                </td>
                <td>
                    <?=$this->Form->input("$x.TmsTableCostRoute.zone_id", ['label' => false, 'class' => 'route-zone', 'onchange' => 'RouteZoneChange(\'id-route-'.$x.'\');', 'empty' => ' - Todas - ', 'options' => $stateZones[$r['TmsTableCostRoute']['state_id']]]);?>
                </td>
                <td>
                    <?=$this->Form->input("$x.TmsTableCostRoute.city_id", ['label' => false, 'class' => 'route-city', 'empty' => ' - Todas - ', 'options' => (empty($r['TmsTableCostRoute']['zone_id']) ? $stateCities[$r['TmsTableCostRoute']['state_id']]:$zoneCities[$r['TmsTableCostRoute']['state_id']][$r['TmsTableCostRoute']['zone_id']])]);?>
                </td>
                <td>
                    <?=$this->Form->input("$x.TmsTableCostRoute.cep_start", ['label' => false, 'class' => 'mask_int', 'type' => 'text']);?>
                </td>
                <td>
                    <?=$this->Form->input("$x.TmsTableCostRoute.cep_end", ['label' => false, 'class' => 'mask_int', 'type' => 'text']);?>
                </td>
                <td>
                    <?=$this->Form->input("$x.TmsTableCostRoute.days", ['label' => false, 'class' => 'mask_int', 'type' => 'text']);?>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-block" onclick="RangeExcluir('id-range-<?=$x?>');"><i class="fa fa-trash-alt"></i> Excluir Rota</button>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <div class="panel-footer text-center">
        <button type='button' class='btn btn-primary' id="btn-route-add">Incluir Rota</button>
        <?=$this->Form->submit('Salvar Rotas', ['class' => 'btn btn-success']);?>
    </div>
</div>
<?=$this->Form->end();?>
<script>
    var _routesData = <?=json_encode(to_utf8($data));?>;

    function RouteStateChange(_select){
        var _state_id = $('#' + _select + ' .route-state option:selected').val();
        var _zoptions = '<option value=""> - Todas as Zonas - </option>';
        for (var i=0; i<_routesData.states.length; i++){
            if (_routesData.states[i].State.id==_state_id){
                for (var ii=0; ii<_routesData.states[i].zones.length; ii++){
                    var Zone = _routesData.states[i].zones[ii].Zone;
                    _zoptions = _zoptions + '<option value="'+Zone.id+'">'+Zone.nome+'</option>';
                }
            }
        }
        $('#' + _select + ' .route-zone').html(_zoptions);
    }
    function RouteZoneChange(_select){
        var _state_id = $('#' + _select + ' .route-state option:selected').val();
        var _zone_id = $('#' + _select + ' .route-zone option:selected').val();
        var _coptions = '<option value=""> - Todas as Cidades - </option>';
        for (var i=0; i<_routesData.states.length; i++){
            if (_routesData.states[i].State.id===_state_id){
                if (_zone_id===''){
                    for (var ii=0; ii<_routesData.states[i].cities.length; ii++){
                        var City = _routesData.states[i].cities[ii].City;
                        _coptions = _coptions + '<option value="'+City.id+'">'+City.nome+'</option>';
                    }
                } else {
                    for (var ii=0; ii<_routesData.states[i].zones.length; ii++){
                        var Zone = _routesData.states[i].zones[ii].Zone;
                        if (Zone.id===_zone_id){
                            for (var iii=0; iii<_routesData.states[i].zones[ii].cities.length; iii++){
                                var City = _routesData.states[i].zones[ii].cities[iii].City;
                                _coptions = _coptions + '<option value="'+City.id+'">'+City.name+'</option>';
                            }
                        }
                    }
                }
            }
        }
        $('#' + _select + ' .route-city').html(_coptions);
    }
    function RangeExcluir(_ref){
        if (confirm('Deseja realmente excluir esta faixa?')){
            $('#' + _ref).hide();
            $('#' + _ref + ' .route-excluir').val(1);
        }
    }
    var _pos = <?=count($this->request->data)?>;
    $('#btn-route-add').click(function(){
        _pos = parseInt(_pos) + 1;
        var _html = '<tr id="id-route-">\n\
            <td>\n\
                <?=$this->Form->hidden("$x.TmsTableCostRoute.id");?>\n\
                <?=$this->Form->hidden("$x.TmsTableCostRoute.excluir", ['class' => 'route-excluir']);?>\n\
                <?=str_replace("\n", '', $this->Form->input("$x.TmsTableCostRoute.state_id", ['label' => false, 'class' => 'route-state', 'onchange' => 'RouteStateChange(\'id-route-\');', 'empty' => false, 'options' => $states]));?>\n\
            </td>\n\
            <td>\n\
                <?=str_replace("\n", '', $this->Form->input("$x.TmsTableCostRoute.zone_id", ['label' => false, 'class' => 'route-zone', 'onchange' => 'RouteZoneChange(\'id-route-\');', 'empty' => ' - Todas - ', 'options' => []]));?>\n\
            </td>\n\
            <td>\n\
                <?=str_replace("\n", '', $this->Form->input("$x.TmsTableCostRoute.city_id", ['label' => false, 'class' => 'route-city', 'empty' => ' - Todas - ', 'options' => []]));?>\n\
            </td>\n\
            <td>\n\
                <?=$this->Form->input("$x.TmsTableCostRoute.cep_start", ['label' => false, 'class' => 'mask_int', 'type' => 'text']);?>\n\
            </td>\n\
            <td>\n\
                <?=$this->Form->input("$x.TmsTableCostRoute.cep_end", ['label' => false, 'class' => 'mask_int', 'type' => 'text']);?>\n\
            </td>\n\
            <td>\n\
                <?=$this->Form->input("$x.TmsTableCostRoute.days", ['label' => false, 'class' => 'mask_int', 'type' => 'text']);?>\n\
            </td>\n\
            <td>\n\
                <button type="button" class="btn btn-danger btn-block" onclick="RangeExcluir(\'id-range-\');"><i class="fa fa-trash-alt"></i> Excluir Rota</button>\n\
            </td>\n\
        </tr>';
        _html = _html.replace(/data\[\]/g, 'data[' + _pos + ']');
        _html = _html.replace(/id-route-/g, 'id-route-' + _pos);
        $('.table-ranges tbody').append(_html);
    });
</script>