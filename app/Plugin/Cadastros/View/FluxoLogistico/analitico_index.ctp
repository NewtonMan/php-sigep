    <fieldset>
        <legend><?php echo __($titulo); ?> <a href="#back" onclick="window.history.back();" title="Voltar"><img src="/img/btn-voltar.png"></a></legend>
        <?php echo $this->Session->flash(); ?>
        <table class="table table-striped" width="100%">
            <tr>
                <th width="50%">Árvore</th>
                <th><a href="/analitico/fluxo_logistico/add" class="btn btn-primary">Criar Novo</a></th>
            </tr>
            <?foreach ($lista as $id=>$nome){?>
            <tr>
                <td><?=$nome?></td>
                <td>
                    <a href="<?if ($fluxo->childCount($id)==0){?>/analitico/fluxo_logistico/cronograma/<?=$id?><?} else {?>#<?}?>" class="btn<?if ($fluxo->childCount($id)>0){?> disabled<?}?>">Cronograma</a>
                    <a href="<?if ($fluxo->childCount($id)==0){?>/analitico/fluxo_logistico/pdas/<?=$id?><?} else {?>#<?}?>" class="btn<?if ($fluxo->childCount($id)>0){?> disabled<?}?>">Relacionar Destinos / PDAs</a>
                    <a href="/analitico/fluxo_logistico/edit/<?=$id?>" class="btn btn-warning">Editar</a>
                    <a href="/analitico/fluxo_logistico/delete/<?=$id?>" class="btn btn-danger">Excluir</a>
                </td>
            </tr>
            <?}?>
            <tr>
                <td colspan="2">
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
    </fieldset>
