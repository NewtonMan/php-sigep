<?=$this->Session->flash();?>
<?=$this->Form->create('User', ['type' => 'file']);?>
<div class="row">
    <div class="col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
        <div class="well">
            <h3>Dados de Viagem</h3>
            <ul>
                <li>Total de usuários ativos no momento: <?=$this->request->data['total']?></li>
                <li>Total de usuários ativos com dados de viagem: <?=$this->request->data['completos']?></li>
            </ul>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1 class="panel-title">Processamento da Planilha</a></h1>
                </div>
                <div class="panel-body">
                    <p>Preencha a planilha modelo com os dados de cada usuário e depois anexa o arquivo no campo abaixo e clique em "Fazer Upload e Processar"</p>
                    <div class="row">
                        <div class="col-sm-6">
                            <?=$this->Form->input('planilha', ['type' => 'file', 'label' => 'Planilha Preenchida', 'accept' => '.xlsx']);?>
                        </div>
                        <div class="col-sm-6">
                            <a href="/modelos/DADOS_VIAGEM_032019.xlsx" class="btn btn-primary btn-block">Download da Planilha Modelo</a>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-center">
                    <button type="submit" class="btn btn-success">Fazer Upload e Processar</button>
                </div>
            </div>
        </div>
    </div>
</div>
        <?=$this->Form->end();?>