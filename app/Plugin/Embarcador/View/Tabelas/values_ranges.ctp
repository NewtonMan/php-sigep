<?=$this->Session->flash();?>
<?=$this->Form->create('TmsTableCostValue', ['type' => 'file']);?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title">Formulário para Faixas/Intervalos de Tabelas</h1>
    </div>
    <div class="panel-body">
        
    </div>
    <table class="table table-striped table-condensed table-ranges">
        <thead>
            <tr>
                <th class='col-xs-6 text-center'>Rota</th>
                <th class='col-xs-2 text-center'>Faixa</th>
                <th class='col-xs-2 text-center'>Valor</th>
                <th class='col-xs-2 text-center'>Percentual</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($faixas as $x => $f) {
            ?>
            <tr>
                <td class="text-center">
                    <?=$this->Form->hidden("$x.TmsTableCostValue.id");?>
                    <?=$this->Form->hidden("$x.TmsTableCostValue.embarcador_tms_cost_range_id", ['value' => $f['TmsTableCostRange']['id']]);?>
                    <?=(empty($route['State']['uf']) ? 'Todos os Estados':$route['State']['uf'])?> / 
                    <?=(empty($route['Zone']['name']) ? 'Todas as Regiões':$route['Zone']['name'])?> / 
                    <?=(empty($route['City']['name']) ? 'Todas as Cidades':$route['City']['name'])?> / 
                    CEP Start <?=$route['TmsTableCostRoute']['cep_start']?> / 
                    CEP End <?=$route['TmsTableCostRoute']['cep_end']?>
                </td>
                <td class="text-center">
                    De <?=$f['TmsTableCostRange']['range_from']?> até <?=$f['TmsTableCostRange']['range_to']?>
                </td>
                <td class="text-center"><?=$this->Form->input("$x.TmsTableCostValue.value", ['label' => false, 'class' => 'mask_money', 'type' => 'text']);?></td>
                <td class="text-center"><?=$this->Form->input("$x.TmsTableCostValue.percent", ['label' => false, 'class' => 'mask_weight', 'type' => 'text']);?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <div class="panel-footer text-center">
        <?=$this->Form->submit('Salvar Valores', ['class' => 'btn btn-success']);?>
    </div>
</div>
<?=$this->Form->end();?>