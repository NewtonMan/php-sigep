<?php echo $this->Session->flash(); ?>
<?php echo $this->Form->create('FluxoLogistico'); ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title"><?php echo __('Formul�rio de Fluxo Log�stico'); ?></h1>
    </div>
    <div class="panel-body">
        <?php
        echo $this->Form->input('cliente_id', array('label'=>'Cliente', 'options'=>$clientes));
        echo $this->Form->input('parent_id', array('label'=>'Fluxo Filho de', 'options'=>$fluxos,'empty'=>' - Fluxo Pai - '));
        echo $this->Form->input('nome');
        //echo $this->Form->input('responsavel_nome');
        //echo $this->Form->input('responsavel_email');
        //echo $this->Form->input('periodo_de', array('type'=>'text', 'class'=>'mask_date', 'label'=>'Ativo de'));
        //echo $this->Form->input('periodo_ate', array('type'=>'text', 'class'=>'mask_date', 'label'=>'Ativo at�'));
        echo $this->Form->input('distribuicao', array('label'=>'Ativar Como Canal de Distribui��o'));
        echo $this->Form->input('ativo', array('options'=>array('sim'=>'Sim', 'nao'=>'N�o'), 'type'=>'select'));
        ?>
    </div>
    <div class="panel-footer">
        <?=$this->Form->submit('Salvar', array('class'=>'btn btn-success'));?>
    </div>
</div>
<?php echo $this->Form->end(); ?>
