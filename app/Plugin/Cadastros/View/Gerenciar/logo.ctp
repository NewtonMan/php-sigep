<?=$this->Session->flash();?>
<?=$this->Form->create('Destino', ['type' => 'file']);?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title">Logo</h1>
    </div>
    <div class="panel-body">
        <?if (file_exists(WWW_ROOT . 'files' . DS . 'logo-' . $id . '.jpg')){?>
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3 text-center">
                <img src="/files/logo-<?=$id?>.jpg" />
            </div>
        </div>
        <?}?>
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
                <?=$this->Form->input('logo', array('class' => 'form-control', 'type' => 'file', 'label' => 'Logo deve ser .JPG'));?>
            </div>
        </div>
    </div>
    <div class="panel-footer text-center">
        <button type="submit" class="btn btn-success">Upload do Logo <i class="fa fa-arrow-circle-o-right"></i></button>
    </div>
</div>
<?=$this->Form->end();?>