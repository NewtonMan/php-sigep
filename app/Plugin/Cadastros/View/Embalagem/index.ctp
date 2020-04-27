    <fieldset>
        <legend>Tipos de Embalagem <a href="#back" onclick="window.history.back();" title="Voltar"><img src="/img/btn-voltar.png"></a></legend>
        <?php echo $this->Session->flash(); ?>
        <table class="table table-striped" width="100%">
            <tr>
                <th width="80%">Nome (Larg x Alt x Prof em centimetros)</th>
                <th><a href="/embalagem/add" class="btn btn-primary">Criar Nova</a></th>
            </tr>
            <?foreach ($lista as $item){?>
            <tr>
                <td><?=$item['Embalagem']['nome']?> (<?=$item['Embalagem']['largura_em_cm']?>cm x <?=$item['Embalagem']['altura_em_cm']?>cm x <?=$item['Embalagem']['profundidade_em_cm']?>cm)</td>
                <td><a href="/embalagem/edit/<?=$item['Embalagem']['id']?>" class="btn btn-warning">Editar</a> <a href="/embalagem/delete/<?=$item['Embalagem']['id']?>" class="btn btn-danger">Excluir</a></td>
            </tr>
            <?}?>
        </table>
    </fieldset>
