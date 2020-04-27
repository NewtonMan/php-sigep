<div class="container">
    <?= $this->Form->create('User', array('class' => 'form-horizontal')); ?>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="panel-title">Recuperar Senha</h4>
                </div>
                <div class="panel-body">
                    <?= $this->Session->flash(); ?>
                    <span>Insira o e-mail que você usou no cadastro do seu usuário. Nós iremos enviar um e-mail e um link que você redefina a sua senha.</span>
                    <? if (!empty($msg)) { ?>
                        <div class="row">
                            <div class="col-xs-12"><p class="bg-<?= $label ?> text-<?= $label ?>"><?= $msg ?></p></div>
                        </div>
                    <? } ?>
                    <div class="form-group">
                        <div class="col-md-12">
                            <?= $this->Form->email('email', array('class' => 'form-control email-input-ppview autofocus', 'placeholder' => 'Insira seu email e prossiga')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <?= $this->Form->submit(_('Prosseguir'), array('class' => 'btn btn-success btn-block')); ?>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-center">
                    <a href="/login">Voltar para tela de Login?</a>
                    <span>Não possui uma conta?</span><a class="ppview-register-link" href="/userRegister">Cadastre-se?</a>
                </div>
            </div>
        </div>
    </div>
    <?= $this->Form->end(); ?>
</div>
