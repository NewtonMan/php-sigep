<?=$this->Session->flash();?>
<?=$this->Form->create('TmsTableCost', ['type' => 'file']);?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title">Formulário para Tabela de Frete</h1>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-4"><?=$this->Form->input('TmsTableCost.transportadora_id', ['label' => 'Transportadora', 'empty' => false, 'options' => $transportadoras])?></div>
            <div class="col-sm-4"><?=$this->Form->input('TmsTableCost.embarcador_tms_model_id', ['label' => 'Modal', 'empty' => false, 'options' => $modal])?></div>
            <div class="col-sm-4"><?=$this->Form->input('TmsTableCost.embarcador_tms_cost_method_id', ['label' => 'Tipo Tabela', 'empty' => false, 'options' => $methods])?></div>
        </div>
        <div class="row">
            <div class="col-sm-4"><?=$this->Form->input('TmsTableCost.name', ['type' => 'text', 'label' => 'Nome da Tabela'])?></div>
            <div class="col-sm-4"><?=$this->Form->input('TmsTableCost.value_minimum', ['type' => 'text', 'label' => 'Valor Mínimo', 'class' => 'mask_money'])?></div>
            <div class="col-sm-4"><?=$this->Form->input('TmsTableCost.value_exced', ['type' => 'text', 'label' => 'Valor Excedente', 'class' => 'mask_money'])?></div>
\        </div>
    </div>
    <div class="panel-footer text-center">
        <?=$this->Form->submit('Salvar Tabela', ['class' => 'btn btn-success']);?>
    </div>
</div>
<?=$this->Form->end();?>