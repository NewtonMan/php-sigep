<?=$this->Session->flash();?>
<?=$this->Form->create('TmsTableCost', ['type' => 'file']);?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title">Importação de CVS</h1>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-4"><?=$this->Form->input('', ['label' => 'Upload de Arquivo', 'empty' => false, 'type' => 'file'])?></div>
        </div>
    </div>
    <div class="panel-footer text-center">
        <?=$this->Form->submit('Importar tabela', ['class' => 'btn btn-success']);?>
    </div>
</div>
<?=$this->Form->end();?>