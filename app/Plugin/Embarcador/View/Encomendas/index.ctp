<div class="panel panel-default">
    <div class="panel-heading">
        <h1 class="panel-title">Embarcadores</h1>
    </div>
    <form class="panel-body" method="get" action="/embarcador/encomendas/<?= $this->request->params['action'] ?>">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Nome / CPF / CNPJ" name="crudSearch" value="<?= @$_GET['crudSearch'] ?>" autofocus>
            <span class="input-group-btn">
                <button class="btn btn-success" type="submit">Pesquisar</button>
            </span>
        </div>
    </form>
    <table class="table table-striped table-condensed">
        <tr>
            <th>
                <?= $this->Paginator->sort('Destino.fantasia', 'Apelido/Nome Fantasia') ?>
                <?= $this->Paginator->sort('Destino.cpf_cnpj', 'CPF/CNPJ') ?>
                <?= $this->Paginator->sort('Destino.municipio', 'Cidade') ?>
                <?= $this->Paginator->sort('Destino.uf', 'UF') ?>
            </th>
            <th>Opções</th>
        </tr>
        <?php foreach ($this->request->data['lista'] as $i) { ?>
            <tr>
                <td>
                    <?=$i['Destino']['fantasia']?> - <?=exibirCpfCnpj($i['Destino']['cpf_cnpj'])?><br/>
                    <?= enderecoLinhaDestino($i['Destino'], true)?>
                </td>
                <td>
                    <a class="btn btn-primary btn-lg" href="/embarcador/encomendas/monitoramento/<?= $i['Destino']['id'] ?>">Monitorar Encomendas</a>
                    <!--
                    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#EmbarcadorPainelModal" onclick="EmbarcadorPainel('<?= $i['Encomenda']['id'] ?>');">Painel Encomenda</button>
                    -->
                </td>
            </tr>
        <?php } ?>
    </table>
    <div class="panel-footer text-center">
        <?= $this->Paginator->numbers(['prev' => '<', 'next' => '>']) ?>
    </div>
</div>
