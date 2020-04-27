<?=$this->Form->create('User', ['type' => 'file'])?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h1 class="panel-title">Importação de Dados no Embarcador</h1>
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
                        <p><strong>ATENÇÃO</strong> Use sempre esta planilha modelo para não importar dados de maneira errada e assim causar problemas irreversíveis nas informações da plataforma, sempre tome todos os cuidados necessários para importar atualizações ao Embarcador, a apresentação correta dos relatórios depende diretamente disso, caso você faça alguma mudança indesejada sem querer, acerte os dados na planilha novamente e faça novamente o upload ao sistema, isso deve corrigir eventuais erros.</p>
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
