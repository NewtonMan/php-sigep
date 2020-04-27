<?=$this->Form->create('User');?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title"><?=_('User Form');?></h1>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-4"><?=$this->Form->input('nome_completo', ['class' => 'form-control']);?></div>
                    <div class="col-xs-4"><?=$this->Form->input('cargo', ['class' => 'form-control']);?></div>
                    <div class="col-xs-4"><?=$this->Form->input('empregador', ['class' => 'form-control']);?></div>
                </div>
                <div class="row">
                    <div class="col-xs-3"><?=$this->Form->input('RG', ['class' => 'form-control']);?></div>
                    <div class="col-xs-3"><?=$this->Form->input('CPF', ['class' => 'form-control']);?></div>
                    <div class="col-xs-3"><?=$this->Form->input('telefone1', ['class' => 'form-control']);?></div>
                    <div class="col-xs-3"><?=$this->Form->input('telefone2', ['class' => 'form-control']);?></div>
                </div>
                <div class="row">
                    <div class="col-xs-6"><?=$this->Form->input('email', ['class' => 'form-control']);?></div>
                    <div class="col-xs-6"><?=$this->Form->input('senha', ['class' => 'form-control', 'type' => 'password']);?></div>
                </div>
                <div class="row">
                    <div class="col-xs-4">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-primary<?=($this->request->data['User']['sexo']=='M' ? ' active':'')?>">
                                <input type="radio" name="data[User][sexo]" value="M" id="UserFormSexoM"<?=($this->request->data['User']['sexo']=='M' ? ' checked':'')?>> Masculino
                            </label>
                            <label class="btn btn-primary<?=($this->request->data['User']['sexo']=='F' ? ' active':'')?>">
                                <input type="radio" name="data[User][sexo]" value="F" id="UserFormSexoF"<?=($this->request->data['User']['sexo']=='F' ? ' checked':'')?>> Feminino
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-4"><?=$this->Form->input('active', ['type' => 'checkbox', 'label' => 'Ativado?']);?></div>
                    <div class="col-xs-4"><?=$this->Form->input('admin', ['type' => 'checkbox', 'label' => 'Administrador?']);?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <?=$this->Form->submit(_('Save User Form'), array('class' => 'btn btn-success'));?>
    </div>
</div>
<?=$this->Form->end();?>
