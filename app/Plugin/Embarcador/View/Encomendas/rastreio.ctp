<div class="container-fluid" style="margin-top: 15px;">
    <div class="row">
        <div class="col">
            <p>
                Download da planilha para NOTA x ETIQUETA: 
                <a href="<?= (DS. 'documentos' . DS . 'ferramentas'.DS.'implantacao'. DS . 'modelo_padrao.csv')?>">Modelo Padrão</a>
            </p>
        </div>
    </div>
    <div class="row">
        <?=$this->Session->flash()?>
        <div class="col">
            <?= $this->Form->create('Encomendas', ['type' => 'file'])?>
            <?= $this->Form->control('CSV', ['type' => 'file'])?>
            <?= $this->Form->button('UPLOAD', ['class' => 'btn btn-success'])?>
            <?= $this->Form->end()?>
        </div>
    </div>
</div>
