<script>
    function marca_fluxo(_input){
        $.get('/acesso_cliente_usuario/marca_fluxo/<?=$user_id?>/'+_input.value);
        alert("Item marcado/desmarcado com sucesso!");
    }
</script>
    <fieldset>
        <legend><?php echo __('Selecione os Fluxos Logísticos para '.$usuario); ?> <a href="#back" onclick="window.history.back();" title="Voltar"><img src="/img/btn-voltar.png"></a></legend>
        <?php echo $this->Session->flash(); ?>
        <table class="table table-striped" width="100%">
            <tr>
                <th width="20">#</th>
                <th>Árvore</th>
            </tr>
            <?foreach ($lista as $id=>$fluxo){?>
            <tr>
                <td><input onclick="marca_fluxo(this);" type="checkbox" name="fluxos" value="<?=$id?>" <?=(in_array($id, $fluxos) ? 'checked ':'')?>/></td>
                <td><?=$fluxo?></td>
            </tr>
            <?}?>
        </table>
    </fieldset>
