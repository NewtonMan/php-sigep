<?=$this->Form->create('User', ['type' => 'file'])?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h1 class="panel-title">Importa��o de Dados no Embarcador</h1>
        </div>
        <div class="panel-body">
            <?=$this->Flash->render()?>
            <div class="row">
                <div class="col-md-6">
                    <?=$this->Form->input('cliente_id')?>
                    <?=$this->Form->input('transportador_id')?>
                    <?=$this->Form->input('planilha', ['type' => 'file'])?>
                </div>
                <div class="col-md-6">
                    <div class="well">
                        <h1>Modelo de Planilha para Importar</h1>
                        <p><strong>ATEN��O</strong> Use sempre esta planilha modelo para n�o importar dados de maneira errada e assim causar problemas irrevers�veis nas informa��es da plataforma, sempre tome todos os cuidados necess�rios para importar atualiza��es ao Embarcador, a apresenta��o correta dos relat�rios depende diretamente disso, caso voc� fa�a alguma mudan�a indesejada sem querer, acerte os dados na planilha novamente e fa�a novamente o upload ao sistema, isso deve corrigir eventuais erros.</p>
                        <p><a href="/modelos/modelo-embarcador_1.xlsx" class="btn btn-info">Download da Planilha Modelo</a></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary">Importar</button>
        </div>
    </div>
<?=$this->Form->end();?>
