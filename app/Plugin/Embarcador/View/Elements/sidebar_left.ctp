<?php
$this->Destino = ClassRegistry::init('Cadastros.Destino');
$this->City = ClassRegistry::init('Embarcador.City');
$this->Zone = ClassRegistry::init('Embarcador.Zone');
$this->State = ClassRegistry::init('Embarcador.State');
$this->Encomenda = ClassRegistry::init('Embarcador.Encomenda');

$embarcador_id = (isset($embarcador_id) ? $embarcador_id:null);
$transportador_id = (isset($_GET['transportador_id']) ? $_GET['transportador_id']:null);
$city_id = (isset($_GET['city_id']) ? $_GET['city_id']:null);
$zone_id = (isset($_GET['zone_id']) ? $_GET['zone_id']:null);
$state_id = (isset($_GET['state_id']) ? $_GET['state_id']:null);

$base_url = "/embarcador/encomendas/monitoramento/{$embarcador_id}";

$zones = [];
$cities = [];
if (!is_null($state_id)){
    $zones = $this->Zone->find('list', [
        'conditions' => [
            'Zone.state_id' => $state_id,
        ],
    ]);
    if (!is_null($zone_id)){
        $cities = $this->City->find('list', [
            'conditions' => [
                'City.zone_id' => $zone_id,
                'City.state_id' => $state_id,
            ],
        ]);
    }
}
$states = $this->State->find('list');
if (!empty($embarcador_id)){
?>
<form method="get" action="/embarcador/encomendas/<?= $this->request->params['action'] ?>/<?=$embarcador_id?>">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h1 class="panel-title">Filtrar Encomendas</h1>
        </div>
        <table class="table table-condensed table-striped">
            <tr>
                <td>
                    <?=$this->Form->select('state_id', $states, ['empty' => ' - Todos os Estados - ', 'name' => 'state_id', 'class' => 'form-control', 'size' => 3, 'value' => $state_id]);?>
                </td>
            </tr>
            <tr>
                <td>
                    <?=$this->Form->select('zone_id', $zones, ['empty' => ' - Todas as Praças - ', 'name' => 'zone_id', 'class' => 'form-control', 'size' => 3, 'value' => $zone_id]);?>
                </td>
            </tr>
            <tr>
                <td>
                    <?=$this->Form->select('city_id', $cities, ['empty' => ' - Todas as Cidades - ', 'name' => 'city_id', 'class' => 'form-control', 'size' => 3, 'value' => $city_id]);?>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" class="form-control" placeholder="Destinatário / CPF / CNPJ" name="dest" value="<?= @$_GET['dest'] ?>">
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" class="form-control" placeholder="Número da NF / Chave NF-e" name="nf" value="<?= @$_GET['nf'] ?>">
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" class="form-control" placeholder="Pesquisar por Palavra Chave" name="crudSearch" value="<?= @$_GET['crudSearch'] ?>">
                </td>
            </tr>
            <tr>
                <td>
                    <button class="btn btn-success btn-block" type="submit">Pesquisar</button>
                </td>
            </tr>
        </table>
    </div>
</form>
<script>
    var _base_url = '/embarcador/encomendas/<?= $this->request->params['action'] ?>/<?=$embarcador_id?>?';
    $('#state_id').change(function(){
        var _option = $('#state_id option:selected').val();
        $('button.btn-success').html('<i class="fa fa-spinner fa-spin"></i>').addClass('disabled').attr('disabled', true);
        window.location.href=_base_url+'state_id='+_option+'&stage_id=<?=@$_GET['stage_id']?>';
    });
    $('#zone_id').change(function(){
        var _option = $('#zone_id option:selected').val();
        $('button.btn-success').html('<i class="fa fa-spinner fa-spin"></i>').addClass('disabled').attr('disabled', true);
        window.location.href=_base_url+'state_id=<?=$state_id?>&zone_id='+_option+'&stage_id=<?=@$_GET['stage_id']?>';
    });
    $('#city_id').change(function(){
        var _option = $('#city_id option:selected').val();
        $('button.btn-success').html('<i class="fa fa-spinner fa-spin"></i>').addClass('disabled').attr('disabled', true);
        window.location.href=_base_url+'state_id=<?=$state_id?>&zone_id=<?=$zone_id?>&city_id='+_option+'&stage_id=<?=@$_GET['stage_id']?>';
    });
</script>
<a href="/embarcador/analitico/calendario/<?=$embarcador_id?>" class="btn btn-default btn-block">Relatórios</a>
<a href="/embarcador/encomendas/relatorio/<?=$embarcador_id?>" class="btn btn-default btn-block">Exportar Dados</a>
<a href="/embarcador/importar?embarcador_id=<?=$embarcador_id?>" class="btn btn-default btn-block">Importar Dados</a>
<a href="/embarcador/analitico/gerenciar_sla/<?=$embarcador_id?>" class="btn btn-default btn-block">Gerenciar SLAs</a>
<?php
}
?>