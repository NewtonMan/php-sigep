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
                <th class='col-xs-10 text-center' colspan="2">Tabela de Rotas</th>
                <th class='col-xs-2 text-center'></th>
            </tr>
            <tr>
                <th class='col-xs-1 text-center'>Estado</th>
                <th class='col-xs-2 text-center'>Região</th>
                <th class='col-xs-3 text-center'>Cidade</th>
                <th class='col-xs-2 text-center'>CEP Start</th>
                <th class='col-xs-2 text-center'>CEP End</th>
                <th class='col-xs-2 text-center'></th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($routes as $x => $r) {
            ?>
            <tr>
                <td class="text-center">
                    <?=(empty($r['State']['uf']) ? 'Todos':$r['State']['uf'])?>
                </td>
                <td class="text-center">
                    <?=(empty($r['Zone']['name']) ? 'Todas':$r['Zone']['name'])?>
                </td>
                <td class="text-center">
                    <?=(empty($r['City']['name']) ? 'Todas':$r['City']['name'])?>
                </td>
                <td class="text-center">
                    <?=$r['TmsTableCostRoute']['cep_start']?>
                </td>
                <td class="text-center">
                    <?=$r['TmsTableCostRoute']['cep_end']?>
                </td>
                <td>
                    <a href="/embarcador/tabelas/values_ranges/<?=$embarcadora_id?>/<?=$embarcador_tms_cost_id?>/<?=$r['TmsTableCostRoute']['id']?>" class="btn btn-primary btn-block"><i class="fa fa-dollar"></i> Precificar Rotas</a>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</div>
<?=$this->Form->end();?>