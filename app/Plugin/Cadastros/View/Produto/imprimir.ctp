    <div class="panel panel-default">
        <div class="panel-heading">
            <h1 class="panel-title">LISTAGEM DE PRODUTOS</h1>
        </div>
        <table class="table table-striped" width="100%">
            <tr>
                <th width="150">Foto</th>
                <th width="40%">Nome</th>
                <th></th>
            </tr>
            <?foreach ($lista as $item){?>
            <tr>
                <td>
                    <?
                    if (!empty($item['Produto']['foto'])){
                        echo "<img src=\"/files/produtos/p/{$item['Produto']['foto']}\" />";
                    }
                    ?>
                </td>
                <td><?=$item['Produto']['id']?> - <?=$item['Produto']['nome']?></td>
                <td></td>
            </tr>
            <?}?>
        </table>
    </div>
