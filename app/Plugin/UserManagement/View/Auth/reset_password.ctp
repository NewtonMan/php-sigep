<?=$this->Session->flash();?>
<?=$this->Form->create('User', array('class' => 'form-horizontal'));?>
<div class="row">
    <div class="col-xs-4 col-xs-offset-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h1 class="panel-title"><?=_('Informe uma Nova Senha');?></h1>
            </div>
            <div class="panel-body">
                <?if (!empty($msg)){?>
                <div class="row">
                    <div class="col-xs-12"><p class="bg-<?=$label?> text-<?=$label?>"><?=$msg?></p></div>
                </div>
                <?}?>
                <div class="form-group">
                    <label class="control-label col-xs-3">Senha</label>
                    <div class="col-xs-8">
                        <?=$this->Form->email('senha', array('type' => 'password', 'class' => 'form-control autofocus', 'placeholder' => 'Digite a Nova Senha'));?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12 text-center">
                        <?=$this->Form->submit(_('Salvar a Senha'), array('class' => 'btn btn-success btn-block btn-raised'));?>
                    </div>
                </div>
            </div>
            <div class="panel-footer text-center">
                <a href="/userRegister" class="pull-left">Ainda não possuí Cadastro?</a>
                <a href="/login" class="pull-right">Voltar a Tela de Login?</a>
                <span class="clearfix">&nbsp;</span>
            </div>
        </div>
    </div>
</div>
<?=$this->Form->end();?>
