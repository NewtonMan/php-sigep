<?php echo $this->Session->flash(); ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title"><?=$cliente['Destinatario']['fantasia']?> - Produtos</h1>
    </div>
    <table class="table table-striped">
        <tr>
            <td>Produto</td>
            <td>Ocorrências</td>
        </tr>
        <?foreach ($produtos as $i){?>
        <tr>
            <td><?=$i['Produto']['id']?> - <?=$i['Produto']['codigo_cliente']?> - <?=$i['Produto']['nome']?></td>
            <td><?=$i[0]['Produto__contagem']?></td>
        </tr>
        <?}?>
    </table>
    <div class="panel-footer">
        <a class="btn btn-danger" href="/cadastros/produto/duplicidade?cliente_id=<?=$_GET['cliente_id']?>&corrigir=1">Executar Rotina para Excluir Duplicidades (IRREVERSÍVEL)</a>
    </div>
</div>
