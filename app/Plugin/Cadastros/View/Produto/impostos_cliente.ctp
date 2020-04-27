<?php
echo $this->Form->create('Produto', array('type' => 'file', 'class'=>'form-horizontal')); ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title"><?php echo __('Atualização de Impostos por Cliente'); ?></h1>
    </div>
    <div class="panel-body">
        <div style="padding-left: 15px; padding-right: 15px;">
            <?=$this->Form->input('cliente_id');?>
            <div class="row">
                <div class="col-md-3"><?=$this->Form->input('imposto_icms_id', array('label' => 'ICMS', 'empty' => ' - Não se aplica - ', 'options' => $imposto_icms));?></div>
                <div class="col-md-3"><?=$this->Form->input('imposto_pis_id', array('label' => 'PIS', 'empty' => ' - Não se aplica - ', 'options' => $imposto_pis));?></div>
                <div class="col-md-3"><?=$this->Form->input('imposto_cofins_id', array('label' => 'COFINS', 'empty' => ' - Não se aplica - ', 'options' => $imposto_cofins));?></div>
                <div class="col-md-3"><?=$this->Form->input('imposto_ipi_id', array('label' => 'IPI', 'empty' => ' - Não se aplica - ', 'options' => $imposto_ipi));?></div>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <?=$this->Form->submit('Aplicar Definições', array('class'=>'btn btn-success'))?>
    </div>
</div>
<?php echo $this->Form->end(); ?>
<!--
ALTER TABLE `produto`  ADD `embTipo` VARCHAR(2) NULL DEFAULT NULL  AFTER `imposto_ipi_id`,  ADD `embItens` INT NULL DEFAULT '0'  AFTER `embTipo`,  ADD `embItensPalete` INT NULL DEFAULT '0'  AFTER `embItens`,  ADD `embPesoBruto` DECIMAL(15,3) NULL DEFAULT '0.000'  AFTER `embItensPalete`,  ADD `embDim1` DECIMAL(15,3) NULL DEFAULT '0.000'  AFTER `embPesoBruto`,  ADD `embDim2` DECIMAL(15,3) NULL DEFAULT '0.000'  AFTER `embDim1`,  ADD `embDim3` DECIMAL(15,3) NULL DEFAULT '0.000'  AFTER `embDim2`;
-->