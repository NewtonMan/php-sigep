<div class="container-fluid" style="margin-top: 15px;">
    <div class="row">
        <div class="col">
            <p>
                Download da planilha padrão para atualizar o EMBARCARDOR: 
                <a href="<?= (DS. 'documentos' . DS . 'ferramentas'.DS.'implantacao'. DS . 'modelo-embarcador.xlsx')?>">Modelo Padrão</a>
            </p>
        </div>
    </div>
    <div class="row">
        <?=$this->Session->flash()?>
        <div class="col">
            <?= $this->Form->create('Encomendas', ['type' => 'file'])?>
            <div class="form-group">
                <?= $this->Form->control('XLSX', ['type' => 'file'])?>
            </div>
            <?= $this->Form->button('UPLOAD', ['class' => 'btn btn-success'])?>
            <?= $this->Form->end()?>
        </div>
    </div>
</div>
