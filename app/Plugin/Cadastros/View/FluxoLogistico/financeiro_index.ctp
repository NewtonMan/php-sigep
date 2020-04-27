<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title"><?php echo __($titulo); ?></h4>
    </div>
    <?php echo $this->Session->flash(); ?>
    <table class="table table-striped">
        <tr>
            <th width="50%">Árvore</th>
            <th>Cliente</th>
            <th>Status</th>
            <th></th>
        </tr>
        <?
        foreach ($lista as $id=>$nome){
            $f = $fluxo->read(null, $id);
        ?>
        <tr>
            <td><?=$nome?></td>
            <td><?=($f['Cliente']['fantasia'])?></td>
            <td><?=($f['FluxoLogistico']['ativo']=='sim' ? 'Ativo':'Inativo')?></td>
            <td>
                <a href="/financeiro/lancamento_financeiro/balanco/<?=$id?>" class="btn btn-default">Despesa x Receita</a>
            </td>
        </tr>
        <?}?>
        <tr>
            <td colspan="4">
                <?if ($this->Paginator->hasPage('Destinatario', 2)):?>
                <ul class="pagination">
                    <?if ($this->Paginator->hasPrev()){?><li><?=$this->Paginator->prev('Anterior');?></li><?}?>
                    <?=$this->Paginator->numbers(array('separator'=>'', 'tag'=>'li', 'currentClass'=>'active', 'currentTag'=>'li'));?>
                    <?if ($this->Paginator->hasNext()){?><li><?=$this->Paginator->next('Próxima');?></li><?}?>
                </ul>
                <?endif;?>
            </td>
        </tr>
    </table>
</div>
