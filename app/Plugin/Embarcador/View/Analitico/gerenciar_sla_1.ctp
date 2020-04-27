<div class="panel panel-default">
    <div class="panel-heading">
        <h1 class="panel-title">Gerencimando de SLA - <?= $dados['Embarcador']['fantasia']?></h1>
    </div>
    <table class="table table-striped table-condensed">
        <tr>
            <th>Região</th>
            <th>Prazo</th>
            <th>Ações</th>
            <th>Criado em</th>
        </tr>
        <?php
            foreach($lista as $i){
                ?>
            <tr>
                <td><?=$i?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?}?>
    </table>
</div>
