<?=$this->Form->create('Destino', array('type'=>'file'))?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h1 class="panel-title">Importar Cadastros</h1>
        </div>
        <div class="panel-body">
            <?=$this->Session->flash()?>
            <div class="well">
                <h4>ATENÇÃO</h4>
                <p>Para importar cadastros use a <a href="/planilha-destino-2018-04.xlsx">planilha modelo anexa aqui <span class="badge badge-success">Atualizada em Mar/2016</span></a>.</p>
            </div>
            <?=$this->Form->input('arquivo', array('label'=>'Planilha', 'accept'=>'*.xlsx', 'type'=>'file'))?>
        </div>
        <div class="panel-footer">
            <?=$this->Form->submit('Importar', array('class'=>'btn btn-success'))?>
        </div>
    </div>
<?=$this->Form->end();?>