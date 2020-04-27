<div class="panel panel-default">
    <div class="panel-heading">
        <h1 class="panel-title">Consultar etiquetas por período</h1>
    </div>
    <?= $this->Form->create('encomendas', ['class' => 'panel-body']) ?>
    <div class="row">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon2">Data Inicio</span>
                <input type="date" class="form-control date_mask" id="data1" name="data1">
            </div>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon2">Data Final</span>
                <input type="date" class="form-control date_mask" id="data2" name="data2">
            </div>
        </div>
        <div class="col-md-4"><button class="btn btn-success" type="submit">Gerar Planilha</button></div>
    </div>
    <?= $this->Form->end() ?>
    <div class="panel-footer text-center">
        <?= $this->Paginator->numbers(['prev' => '<', 'next' => '>']) ?>
    </div>
</div>
