<?php echo $this->Form->create('Embalagem'); ?>
    <fieldset>
        <legend><?php echo __('Cadastro de Tipo de Embalagem'); ?> <a href="#back" onclick="window.history.back();" title="Voltar"><img src="/img/btn-voltar.png"></a></legend>
    <?php
        echo $this->Form->input('nome', array('label'=>'Nome da Embalagem'));
        echo $this->Form->input('pallet_pbr', array('label'=>'Pallet PBR?'));
        echo $this->Form->input('largura_em_cm', array('type'=>'text', 'class'=>'mask_cm'));
        echo $this->Form->input('altura_em_cm', array('type'=>'text', 'class'=>'mask_cm'));
        echo $this->Form->input('profundidade_em_cm', array('type'=>'text', 'class'=>'mask_cm'));
        echo $this->Form->submit('Salvar', array('class'=>'btn btn-primary'));
    ?>
    </fieldset>
<?php echo $this->Form->end(); ?>
<?php $this->start('script-onload');?>
$('#EmbalagemPalletPbr').click(function(){
    if ($('#EmbalagemPalletPbr').prop('checked')==true){
        $('#EmbalagemProfundidadeEmCm').val('100,0');
        $('#EmbalagemLarguraEmCm').val('120,0');
        $('#EmbalagemAlturaEmCm').val('150,0');
    }
});
<?php $this->end();?>
