<div class="container" style="padding-top: 10%;">
    <?= $this->Form->create('User'); ?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h1 class="panel-title"><?= _('Cadastrar Novo Usuário'); ?></h1>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6"><?= $this->Form->input('nome_completo', ['class' => 'form-control']); ?></div>
                <div class="col-xs-6 col-md-3"><?= $this->Form->input('RG', ['class' => 'form-control']); ?></div>
                <div class="col-xs-6 col-md-3"><?= $this->Form->input('CPF', ['class' => 'form-control']); ?></div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-4"><?= $this->Form->input('email', ['class' => 'form-control']); ?></div>
                <div class="col-xs-6 col-md-2"><?= $this->Form->input('senha', ['class' => 'form-control', 'type' => 'password']); ?></div>
                <div class="col-xs-6 col-md-2"><?= $this->Form->input('sexo', ['class' => 'form-control', 'type' => 'select', 'label' => 'Sexo', 'options' => ['M' => 'Masculino', 'F' => 'Feminino']]); ?></div>
                <div class="col-xs-6 col-md-2"><?= $this->Form->input('telefone1', ['class' => 'form-control']); ?></div>
                <div class="col-xs-6 col-md-2"><?= $this->Form->input('telefone2', ['class' => 'form-control']); ?></div>
            </div>
            <div class="row">
                <div class="col-xs-4"><?= $this->Form->input('tamanho_camiseta', ['class' => 'form-control', 'type' => 'select', 'empty' => ' - Não definido - ', 'label' => 'Tamanho Camiseta', 'options' => ['PP' => 'PP', 'P' => 'P', 'M' => 'M', 'G' => 'G', 'GG' => 'GG']]); ?></div>
                <div class="col-xs-4"><?= $this->Form->input('tamanho_calca', ['class' => 'form-control mask_int', 'label' => 'Tamanho Calça']); ?></div>
                <div class="col-xs-4"><?= $this->Form->input('tamanho_calcado', ['class' => 'form-control mask_int', 'label' => 'Tamanho Calçado']); ?></div>
            </div>
            <div class="row">
                <div class="col-md-4"><?= $this->Form->input('active', ['class' => 'form-control', 'type' => 'select', 'label' => 'Situação', 'options' => [1 => 'Ativo', 0 => 'Inativo']]); ?></div>
            </div>
        </div>
        <div class="panel-footer">
            <?= $this->Form->submit(_('Save User Form'), array('class' => 'btn btn-success')); ?>
        </div>
    </div>
    <?= $this->Form->end(); ?>
</div>