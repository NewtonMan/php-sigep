    <fieldset>
        <legend><?php echo __($titulo); ?> <a href="#back" onclick="window.history.back();" title="Voltar"><img src="/img/btn-voltar.png"></a></legend>
        <form method="get" class="search-form">
            <div class="well">
                <strong>Formulário de Pesquisa</strong><br/>
                <br/>
                <label>
                    Palavra chave:<br/>
                    <input type="text" name="w" value="<?=@$_GET['w']?>" /> ex. "Carrefour" ou "Marginal Tiête"
                </label><br/>
                <br/>
                <label>
                    Cidade:<br/>
                    <input type="text" name="c" value="<?=@$_GET['c']?>" /> ex. São Paulo
                </label><br/>
                <br/>
                <label>
                    UF:<br/>
                    <input type="text" name="uf" value="<?=@$_GET['uf']?>" maxlength="2" /> ex. SP
                </label><br/>
                <br/>
                <input type="submit" value="Pesquisar" class="btn btn-large" onclick="$('.search-form').hide(500);" />
            </div>
        </form>
        <input type="button" class="btn btn-default" value="Formulário de Pesquisa" onclick="$('.search-form').toggle(500);" />
        <br/><br/>
        <table class="table table-striped" width="100%">
            <tr>
                <th><?=$this->Paginator->sort('fantasia', 'Fantasia')?></th>
                <th><?=$this->Paginator->sort('nome_razao', 'Nome/Razão Social')?></th>
                <th><?=$this->Paginator->sort('municipio', 'Cidade')?></th>
                <th><?=$this->Paginator->sort('uf', 'UF')?></th>
            </tr>
            <?
            $total = count($destinatarios);
            if ($total ==0){
                ?>
            <tr>
                <td colspan="4" align="center">Não existem registros com estes critérios.</td>
            </tr>
                <?
            } else {
            foreach ($destinatarios as $item){
                $checked = false;
                foreach ($item['FluxoLogisticoDestino'] as $destino){
                    if ($destino['fluxo_logistico_id']==$fluxo_id) $checked = true;
                }
            ?>
            <tr>
                <td><?=$item['Destinatario']['fantasia']?></td>
                <td><?=$item['Destinatario']['nome_razao']?></td>
                <td><?=$item['Destinatario']['municipio']?></td>
                <td><?=$item['Destinatario']['uf']?></td>
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
            <?}?>
        </table>
    </fieldset>