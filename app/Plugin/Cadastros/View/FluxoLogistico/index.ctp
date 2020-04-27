<?php echo $this->Session->flash(); ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title"><?php echo __($titulo); ?></h1>
    </div>
    <table class="table table-striped table-condensed">
        <tr>
            <th>Árvore</th>
            <th>Cliente</th>
            <th>Status</th>
            <th><a href="/cadastros/fluxo_logistico/add" class="btn btn-primary btn-xs">Criar Novo</a></th>
        </tr>
        <?foreach ($lista as $id=>$nome){
            $f = $fluxo->read(null, $id);
        ?>
        <tr>
            <td><?=$id?> - <?=$nome?></td>
            <td><?=($f['Cliente']['fantasia'])?></td>
            <td><?=($f['FluxoLogistico']['ativo']=='sim' ? 'Ativo':'Inativo')?></td>
            <td>
                <a href="/cadastros/fluxo_logistico/edit/<?=$id?>" class="btn btn-warning btn-xs">Editar</a>
                <a href="/cadastros/fluxo_logistico/delete/<?=$id?>" class="btn btn-danger btn-xs">Excluir</a>
            </td>
        </tr>
        <?}?>
    </table>
</div>
