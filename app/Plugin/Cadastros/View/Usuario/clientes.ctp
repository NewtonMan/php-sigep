<?php echo $this->Session->flash(); ?>
<?=$this->Form->create('UsuarioCliente');?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title"><?php echo __('Selecione os Clientes Atendidos por ').$usuario; ?></h1>
    </div>
    <table class="table table-striped">
        <tr>
            <th width="20">#</th>
            <th>Cliente</th>
        </tr>
        <?foreach ($lista as $x => $item){?>
        <tr>
            <td>
                <input type="hidden" name="data[UsuarioCliente][<?=$x?>][usuario_id]" value="<?=$user_id?>" />
                <input id="check-<?=$item['Destinatario']['id']?>" type="checkbox" name="data[UsuarioCliente][<?=$x?>][cliente_id]" value="<?=$item['Destinatario']['id']?>" <?=(in_array($item['Destinatario']['id'], $clientes) ? 'checked ':'')?>/>
            </td>
            <td><label for="check-<?=$item['Destinatario']['id']?>"><?=$item['Destinatario']['fantasia']?></label></td>
        </tr>
        <?}?>
    </table>
    <div class="panel-footer">
        <button type="submit" class="btn btn-success">Salvar</button>
    </div>
</div>
<?=$this->Form->end();?>