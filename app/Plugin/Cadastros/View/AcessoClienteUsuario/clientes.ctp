<script>
    function marca_cliente(_input){
        $.get('/acesso_cliente_usuario/marca_cliente/<?=$user_id?>/'+_input.value);
    }
</script>
    <fieldset>
        <legend><?php echo __('Selecione os Clientes para '.$usuario); ?> <a href="#back" onclick="window.history.back();" title="Voltar"><img src="/img/btn-voltar.png"></a></legend>
        <?php echo $this->Session->flash(); ?>
        <table class="table table-striped" width="100%">
            <tr>
                <th width="20">#</th>
                <th>Cliente</th>
            </tr>
            <?foreach ($lista as $item){?>
            <tr>
                <td><input onclick="marca_cliente(this);" type="checkbox" name="clientes" value="<?=$item['Destinatario']['id']?>" <?=(in_array($item['Destinatario']['id'], $clientes) ? 'checked ':'')?>/></td>
                <td><?=$item['Destinatario']['fantasia']?></td>
            </tr>
            <?}?>
        </table>
    </fieldset>
