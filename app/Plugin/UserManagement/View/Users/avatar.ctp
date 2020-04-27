<?=$this->Form->create('User', ['type' => 'file']);?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title"><?=_('Trocar Avatar');?></h1>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-8"><img src="<?= str_replace('/avatar-', '/o-avatar-', $this->request->data['User']['avatar'])?>" width="100%" /></div>
            <div class="col-xs-4">
                <div class="well">
                    <h2>Você pode atualizar o Avatar</h2>
                    <?=$this->Form->input('avatar', ['class' => 'form-control', 'type' => 'file']);?>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <?=$this->Form->submit(_('Save User Form'), array('class' => 'btn btn-success'));?>
    </div>
</div>
<?=$this->Form->end();?>
