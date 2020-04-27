<form action="/user_management/users/importar" method="post" enctype="multipart/form-data">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h1 class="panel-title">Importar Usuários</h1>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="arquivo">Planilha em Formato CSV</label>
                        <input type="file" class="control-form" name="arquivo" id="arquivo" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="well">
                        <p><strong>ATENÇÃO</strong> use sempre a planilha modelo para preencher ou exporte sua planilha em CSV com a mesma sequência de colunas.</p>
                        <a href="/modelo-usuarios-importar.csv?v=<?=time()?>" class="btn btn-info">Download da Planilha</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <?=$this->Form->submit(_('Save User Form'), array('class' => 'btn btn-success'));?>
        </div>
    </div>
</form>
