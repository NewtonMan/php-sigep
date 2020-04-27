<video autoplay muted loop style="position: fixed;">
    <source src="/loop.mp4" type="video/mp4" style="width: 100%; height: 100%;">
</video>

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2 vcenter" style="padding-top: 10%;">
            <?= $this->Form->create('User', array('class' => 'form-horizontal')); ?>
            <div class="panel panel-primary panel-transparent">
                <div class="panel-heading">
                    <h4 class="panel-title">Fazer login com um e-mail</h4>
                </div>
                <div class="panel-body text-center">
                    <?= $this->Session->flash(); ?>
                    <div class="row">
                        <div class="col-md-4">
                            <img src="/img/logo.png" width="100%" style="margin-bottom: 20px;" /><br/>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <?= $this->Form->email('email', array('class' => 'form-control email-input-ppview autofocus', 'placeholder' => 'Insira seu email e prossiga')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <?= $this->Form->password('senha', array('class' => 'form-control pass-input-ppview', 'placeholder' => 'Senha de Acesso')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <?= $this->Form->submit(_('Login'), array('class' => 'btn btn-success btn-block')); ?>
                                </div>
                            </div>
                            <div>
                                <a href="/forgotPassword">Esqueceu sua Senha?</a><br/>
                                <span>Não possui uma conta?</span> <a href="/userRegister">Cadastre-se?</a><br/>
                                <a href="/testes.apk?v=<?=time()?>">APK de Testes</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
