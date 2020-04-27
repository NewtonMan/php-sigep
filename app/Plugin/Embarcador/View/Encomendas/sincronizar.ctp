<div class="container-fluid" style="margin-top: 15px;">
    <div class="row">
        <div class="col">
            <p>
                Deseja realmente sinccroniar ?
            </p>
        </div>
    </div>
    <div class="row">
        <?=$this->Session->flash()?>
        <div class="col">
            <?= $this->Form->create('Encomendas')?>
            <?= $this->Form->button('SINCRONIZAR', ['class' => 'btn btn-success'])?>
            <?= $this->Form->end()?>
        </div>
    </div>
</div>
