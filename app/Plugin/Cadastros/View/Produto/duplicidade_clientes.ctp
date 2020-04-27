<?php echo $this->Session->flash(); ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title">Cliente</h1>
    </div>
    <table class="table table-striped">
        <?foreach ($clientes as $i){?>
        <tr>
            <td><a href="/cadastros/produto/duplicidade?cliente_id=<?=$i['Destinatario']['id']?>"><?=$i['Destinatario']['fantasia']?></a></td>
        </tr>
        <?}?>
    </table>
    <div class="panel-footer">
    </div>
</div>
