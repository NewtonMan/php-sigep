<?php echo $this->Form->create('AcessoClienteUsuario'); ?>
    <fieldset>
        <legend><?php echo __('Adicionar Usuário'); ?> <a href="#back" onclick="window.history.back();" title="Voltar"><img src="/img/btn-voltar.png"></a></legend>
    <?php
        echo $this->Form->input('nome');
        echo $this->Form->input('email', array('type'=>'text'));
        echo $this->Form->input('senha', array('type'=>'password'));
        echo $this->Form->input('bloqueia_avisos', array('label' => 'Não Permitir Fazer Pedidos'));
        echo $this->Form->input('ativo', array('options' => array(1 => 'Sim', 0 => 'Nao', 'empty' => false)));
        echo $this->Form->submit('Salvar', array('class'=>'btn btn-primary'));
    ?>
    </fieldset>
<?php echo $this->Form->end(); ?>
