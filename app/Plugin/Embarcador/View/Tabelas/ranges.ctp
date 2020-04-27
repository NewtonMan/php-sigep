<?=$this->Session->flash();?>
<?=$this->Form->create('TmsTableCostRange', ['type' => 'file']);?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title">Formulário para Faixas/Intervalos de Tabelas</h1>
    </div>
    <div class="panel-body">
        
    </div>
    <table class="table table-striped table-condensed table-ranges">
        <thead>
            <tr>
                <th class='text-center'>De</th>
                <th class='text-center'>Até</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($this->request->data as $x => $r) {
            ?>
            <tr id="route-<?=$x?>">
                <td>
                    <?=$this->Form->hidden("$x.TmsTableCostRange.id");?>
                    <?=$this->Form->hidden("$x.TmsTableCostRange.excluir", ['class' => 'route-excluir']);?>
                    <?=$this->Form->input("$x.TmsTableCostRange.range_from", ['label' => false, 'placeholder' => 'A partir de', 'class' => 'form-control-sm mask_weight', 'type' => 'text']);?>
                </td>
                <td>
                    <?=$this->Form->input("$x.TmsTableCostRange.range_to", ['label' => false, 'placeholder' => 'Até', 'class' => 'form-control-sm  mask_weight', 'type' => 'text']);?>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-block" onclick="RangeExcluir('<?=$x?>');"><i class="fa fa-trash-alt"></i> Excluir Faixa</button>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <div class="panel-footer text-center">
        <button type='button' class='btn btn-primary' id="btn-route-add">Incluir Faixa</button>
        <?=$this->Form->submit('Salvar Tabela', ['class' => 'btn btn-success']);?>
    </div>
</div>
<?=$this->Form->end();?>
<script>
    function RangeExcluir(_ref){
        if (confirm('Deseja realmente excluir esta faixa?')){
            $('#route-' + _ref).hide();
            $('#route-' + _ref + ' .route-excluir').val(1);
        }
    }
    var _pos = <?=count($this->request->data)?>;
    $('#btn-route-add').click(function(){
        _pos = parseInt(_pos) + 1;
        var _html = '<tr id="route-' + _pos + '">\n\
            <td>\n\
                <input type="hidden" name="data[' + _pos + '][TmsTableCostRange][id]" id="' + _pos + 'TmsTableCostRangeId">\n\
                <input type="hidden" class="route-excluir" name="data[' + _pos + '][TmsTableCostRange][excluir]" id="' + _pos + 'TmsTableCostRangeExcluir">\n\
                <div class="form-group"><input name="data[' + _pos + '][TmsTableCostRange][range_from]" placeholder="A partir de" class="form-control-sm form-control mask_weight" type="text" id="' + _pos + 'TmsTableCostRangeRangeFrom"></div>\n\
            </td>\n\
            <td>\n\
                <div class="form-group"><input name="data[' + _pos + '][TmsTableCostRange][range_to]" placeholder="Até" class="form-control-sm form-control mask_weight" type="text" id="' + _pos + 'TmsTableCostRangeRangeTo"></div>\n\
            </td>\n\
            <td>\n\
                <button type="button" class="btn btn-danger btn-block" onclick="RangeExcluir(' + _pos + ');"><i class="fa fa-trash-alt"></i> Excluir Faixa</button>\n\
            </td>\n\
        </tr>';
        $('.table-ranges tbody').append(_html);
    });
</script>