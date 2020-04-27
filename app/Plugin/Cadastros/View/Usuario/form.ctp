<?php echo $this->Form->create('Usuario'); ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title">Formulário de Cadastro de Usuário</h1>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <?php
                echo $this->Form->input('acesso_cliente_id', ['options' => $clientes, 'empty' => 'Acesso Colaborador', 'label' => 'Acesso Colaborador ou Cliente']);
                echo $this->Form->input('nome');
                echo $this->Form->input('email', array('type'=>'text'));
                echo $this->Form->input('senha', array('type'=>'password'));
                echo $this->Form->input('default_module', array('label' => 'Opção Após Login', 'class' => 'acesso-colaborador', 'options' => array('/' => 'Abrir Módulo Estoque', '/fretes/listagem?data[em_andamento]=1' => 'Abrir Módulo Transporte', '/financeiro/fin_report/periodo' => 'Abrir Módulo Financeiro')));
                echo $this->Form->input('ativo');
                ?>
            </div>
            <div class="col-md-6 acesso-colaborador">
                <?php
                if (!isset($this->request->data['Usuario']['id'])){
                    $this->request->data['Usuario']['modulo_estoque'] = 1;
                    $this->request->data['Usuario']['modulo_analitico'] = 1;
                    $this->request->data['Usuario']['modulo_financeiro'] = 1;
                    $this->request->data['Usuario']['modulo_nfe'] = 1;
                    $this->request->data['Usuario']['modulo_cte'] = 1;
                    $this->request->data['Usuario']['modulo_frota'] = 1;
                    $this->request->data['Usuario']['modulo_transporte'] = 1;
                    $this->request->data['Usuario']['modulo_embarcador'] = 1;
                }
                echo '<div class="row"><div class="col-xs-12">'.$this->Form->input('modulo_estoque', array('type'=>'checkbox')).'</div></div>';
                echo '<div class="row"><div class="col-xs-12">'.$this->Form->input('modulo_analitico', array('type'=>'checkbox')).'</div></div>';
                echo '<div class="row"><div class="col-xs-12">'.$this->Form->input('modulo_financeiro', array('type'=>'checkbox')).'</div></div>';
                echo '<div class="row"><div class="col-xs-12">'.$this->Form->input('modulo_nfe', array('type'=>'checkbox')).'</div></div>';
                echo '<div class="row"><div class="col-xs-12">'.$this->Form->input('modulo_cte', array('type'=>'checkbox')).'</div></div>';
                echo '<div class="row"><div class="col-xs-12">'.$this->Form->input('modulo_frota', array('type'=>'checkbox')).'</div></div>';
                echo '<div class="row"><div class="col-xs-12">'.$this->Form->input('modulo_transporte', array('type'=>'checkbox')).'</div></div>';
                echo '<div class="row"><div class="col-xs-12">'.$this->Form->input('modulo_embarcador', array('type'=>'checkbox')).'</div></div>';
                ?>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <?=$this->Form->submit('Salvar', array('class'=>'btn btn-primary'));?>
    </div>
</div>
<?php echo $this->Form->end(); ?>
<script>
    $('#UsuarioAcessoClienteId').change(function(){
        var _c = $('#UsuarioAcessoClienteId option:selected').val();
        if (_c==''){
            $('.acesso-colaborador').show();
        } else {
            $('.acesso-colaborador').hide();
        }
    }).change();
</script>