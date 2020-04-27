<?=$this->Form->create('Destino');?>
<?=$this->Session->flash();?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title">Novo Cadastro</h1>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
                Informe o CPF ou o CNPJ abaixo para uma pesquisa.
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
                <?=$this->Form->text('cpf_cnpj', array('class' => 'form-control', 'placeholder' => 'Digite aqui o CPF ou CNPJ'));?>
            </div>
        </div>
    </div>
    <div class="panel-footer text-center">
        <button type="submit" class="btn btn-success">Próximo Passo <i class="fa fa-arrow-circle-o-right"></i></button>
    </div>
</div>
<?=$this->Form->end();?>