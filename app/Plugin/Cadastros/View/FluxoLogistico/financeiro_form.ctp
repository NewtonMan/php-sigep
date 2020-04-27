<?php echo $this->Form->create('FluxoLogistico'); ?>
    <fieldset>
        <legend><?php echo __('Formulário de Fluxo Financeiro'); ?> <a href="#back" onclick="window.history.back();" title="Voltar"><img src="/img/btn-voltar.png"></a></legend>
    <?php
        echo $this->Form->input('usuario_id', array('type'=>'hidden', 'value'=>$session->read('Auth.User.id')));
        echo $this->Form->input('parent_id', array('label'=>'Fluxo Filho de', 'options'=>$fluxos));
        echo $this->Form->input('nome');
        echo $this->Form->input('responsavel_nome');
        echo $this->Form->input('responsavel_email');
        echo $this->Form->input('periodo_de', array('type'=>'text', 'class'=>'mask_date', 'label'=>'Ativo de'));
        echo $this->Form->input('periodo_ate', array('type'=>'text', 'class'=>'mask_date', 'label'=>'Ativo até'));
        echo $this->Form->input('cliente_id', array('label'=>'Cliente', 'options'=>$clientes));
        echo $this->Form->input('ativo', array('options'=>array('sim'=>'Sim', 'nao'=>'Não'), 'type'=>'radio'));
        echo $this->Form->submit('Salvar', array('class'=>'btn btn-primary'));
    ?>
    </fieldset>
<?php echo $this->Form->end(); ?>
