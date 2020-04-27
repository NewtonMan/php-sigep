<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h1 class="panel-title">Lista de Transportadoras - <?= $dados['Embarcador']['fantasia'] ?></h1>
        </div>
        <table class="table table-striped table-condensed">
            <tr>
                <th>Transportadora</th>
                <th>SLA</th>
            </tr>
            <?php foreach ($transportador as $t) { ?>
                <tr>
                    <td><?= $t['Destino']['fantasia'] ?></td>
                    <td class="col-3"><a href="/embarcador/analitico/tabela_sla/<?= $dados['Embarcador']['id'] ?>/<?= $t['Destino']['id'] ?>" class="btn btn-info">SLA</a></td>
                </tr>
            <? } ?>
        </table>
    </div>
</div>
