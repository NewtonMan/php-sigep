<form action="/embarcador/analitico/tabela_sla/<?= $dados['Embarcador']['id'] ?>/<?= $transportador['Destino']['id'] ?>" method="post" class="form-inline">
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title">Gerencimando de SLA - <?= $dados['Embarcador']['fantasia'] ?> / <?= $transportador['Destino']['fantasia'] ?></h1>
            </div>
            <div class="panel-body">
                <?php foreach ($lista as $i) {?>
                    <div class="row">
                        <div class="form-group col-sm-8 col-sm-offset-3">
                            <label for="<?=$i['TmsRegion']['id']?>" class="col-sm-4" style="padding-top: 7px;"><?= $i['TmsRegion']['nome'] ?></label>
                            <div class="input-group">
                                <input type="text" class="form-control mask_int" name="<?= $i['TmsRegion']['id'] ?>" id="<?=$i['TmsRegion']['id']?>">
                                <span class="input-group-addon" >PRAZO EM DIAS</span>
                            </div>
                        </div>
                    </div>
                <? } ?>
                <input type="hidden" value="<?=$dados['Embarcador']['id']?>" name="embarcador_id" hidden>
                <input type="hidden" value="<?=$transportador['Destino']['id']?>" name="transportador_id" hidden>
                <div class="row text-center">
                    <br>
                    <button class="btn btn-success" type="submit">SALVAR</button>
                </div>
            </div>
        </div>
    </div>
</form>
