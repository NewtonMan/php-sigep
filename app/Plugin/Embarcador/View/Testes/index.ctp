<?=$this->Form->create('Encomenda', ['type' => 'file']);?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title">Upload NFe</h1>
    </div>
    <div class="panel-body">
        <?=$this->Form->input('arquivo', ['type' => 'file']);?>
    </div>
    <div class="panel-footer">
        <button class="btn btn-primary" type="submit">Upload</button>
    </div>
</div>
<?=$this->Form->end();?>

