<form method="post">
    <fieldset>
        <legend><?php echo __('Restrições de ').$u['Usuario']['nome']; ?> <a href="#back" onclick="window.history.back();" title="Voltar"><img src="/img/btn-voltar.png"></a></legend>
        <?php echo $this->Session->flash(); ?>
        <table class="table table-striped" width="100%">
            <tr>
                <th>Módulo / Seção / Área</th>
                <th>Bloquear?</th>
            </tr>
            <?
            foreach ($skell as $modulo=>$item){
                if ($u['Usuario']['modulo_estoque']!=1 && $modulo=='Estoque') continue;
                if ($u['Usuario']['modulo_analitico']!=1 && $modulo=='Analítico') continue;
                if ($u['Usuario']['modulo_financeiro']!=1 && $modulo=='Financeiro') continue;
                ?>
            <tr>
                <th colspan="2"><?=$modulo?></th>
            </tr>
            <?
                foreach ($item as $controller => $sitem){
                    foreach ($sitem as $action => $descricao){
            ?>
            <tr>
                <td><?=$descricao?></td>
                <td><input type="checkbox" name="<?=$controller.'|'.$action?>" value="1"<?=(in_array($controller.'|'.$action,$bloqueados) ? ' checked="checked"':'')?> /></td>
            </tr>
            <?}}}?>
            <tr>
                <td colspan="2">
                    <button type="submit" class="btn btn-primary">Salvar Restrições</button>
                </td>
            </tr>
        </table>
    </fieldset>
</form>
