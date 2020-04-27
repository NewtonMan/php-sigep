<script>
    function EmbarcadorPainel(_id) {
        var _title = 'Painel da Encomenda '+_id;
        $('#EmbarcadorPainelModal .modal-title').html(_title);
        $('#EmbarcadorPainelModal iframe').attr('src', '/embarcador/encomendas/painel/'+_id);
    }
</script>
<style>
.progress-steps {
width: 100%;
margin: 20px auto;
text-align: center;
}
.progress-steps .circle,
.progress-steps .bar {
display: inline-block;
background: #fff;
width: 40px; 
height: 40px;
border-radius: 40px;
border: 1px solid #d5d5da;
}
.progress-steps .bar {
position: relative;
width: 80px;
height: 6px;
top: -33px;
margin-left: -5px;
margin-right: -5px;
border-left: none;
border-right: none;
border-radius: 0;
}
.progress-steps .circle .progress-steps-label {
display: inline-block;
width: 32px;
height: 32px;
line-height: 32px;
border-radius: 32px;
margin-top: 3px;
color: #b5b5ba;
font-size: 17px;
}
.progress-steps .circle .title {
color: #b5b5ba;
font-size: 13px;
line-height: 30px;
margin-left: -5px;
}

/* Done / Active */
.progress-steps .bar.done,
.progress-steps .circle.done {
background: #eee;
}
.progress-steps .bar.active {
background: linear-gradient(to right, #EEE 40%, #FFF 60%);
}
.progress-steps .circle.done .progress-steps-label {
color: #FFF;
background: #81CE97;
box-shadow: inset 0 0 2px rgba(0,0,0,.2);
}
.progress-steps .circle.done .title {
color: #444;
}
.progress-steps .circle.active .progress-steps-label {
color: #FFF;
background: #ffcc00;
box-shadow: inset 0 0 2px rgba(0,0,0,.2);
}
.progress-steps .circle.active .title {
color: #0c95be;
}
</style>
<!-- Modal -->
<div class="modal fade" id="EmbarcadorPainelModal" tabindex="-1" role="dialog" aria-labelledby="EmbarcadorPainelModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="EmbarcadorPainelModalLabel">Painel da Encomenda</h4>
            </div>
            <iframe frameborder="0" style="width: 100%; height: 500px;"></iframe>
        </div>
    </div>
</div>

<div class="row-fluid">
    <div class="col-md-3">
        <?=$this->element('sidebar_left')?>
    </div>
    <div class="col-md-6">
<div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title">
                    <?=$this->Paginator->counter('Encomendas {:count} - página {:page} de {:pages}');?>
                    <div class="pull-right">
                        <div class="label label-success">Sucesso</div>
                        <div class="label label-primary">Transito</div>
                        <div class="label label-warning">Atenção</div>
                        <div class="label label-danger">Ocorrência</div>
                    </div>
                </h1>
            </div>
            <table class="table table-striped table-condensed table-hover">
                <tr>
                    <th>
                        <?= $this->Paginator->sort('Encomenda.data_emissao', 'Data') ?> / 
                        <?= $this->Paginator->sort('Tipo.name', 'Tipo Doc') ?> / 
                        <?= $this->Paginator->sort('Encomenda.nfe_serie', 'Série NF') ?> / 
                        <?= $this->Paginator->sort('Encomenda.nfe_numero', 'Número NF') ?> / 
                        <?= $this->Paginator->sort('Destinatario.fantasia', 'Destinatario') ?> / 
                        <?= $this->Paginator->sort('Transportador.fantasia', 'Transportador') ?>
                    </th>
                </tr>
                <?php foreach ($this->request->data['lista'] as $i) { ?>
                <tr>
                    <td onmouseover="this.style.cursor='pointer';" onclick="javascript:void(abrePopUp('/embarcador/encomendas/painel/<?= $i['Encomenda']['id'] ?>'));">
                        <div class="label label-<?=$i['Status']['label']?>"><?=$i['Status']['name']?></div>
                        <?php if ($i['Status']['ocorrencia']==1 && empty($i['Encomenda']['ocorrencia_lida'])){ ?>
                        <div class="label label-warning"><i class="fa fa-star"></i> Nova</div>
                        <?php } ?>
                        <?php if ($i['Status']['ocorrencia']==1 && !empty($i['Encomenda']['ocorrencia_responsavel_id'])){ ?>
                        <div class="label label-primary"><i class="fa fa-user"></i> <?=$i['OcorrenciaResponsavel']['nome']?></div>
                        <?php } ?>
                        <br/>
                        <?= $i['Tipo']['name'] ?> <?= ($i['Tipo']['id'] == 1 ? "Série {$i['Encomenda']['nfe_serie']} Número {$i['Encomenda']['nfe_numero']}" : "{$i['Encomenda']['declaracao_numero']}") ?> - Data Emissão <?= $i['Encomenda']['data_emissao'] ?>
                        <br/>
                        <?= exibirCpfCnpj($i['Destinatario']['cpf_cnpj']) ?> - <?= $i['Destinatario']['fantasia'] ?> - <?= $i['City']['name'] ?> / <?= $i['City']['uf'] ?> <?= (!empty($i['Destinatario']['telefones']) ? "- Tel: {$i['Destinatario']['telefones']}" : '') ?><br/>
                        <?= $i['Transportador']['fantasia'] ?><br/>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="progress-steps">
                                    <div class="circle<?=(!empty($i['Encomenda']['data_coleta']) ? ' done':'')?>">
                                        <span class="progress-steps-label"><i class="fa fa-cube"></i></span>
                                        <span class="title">Coleta</span>
                                    </div>
                                    <span class="bar<?=(!empty($i['Encomenda']['data_coleta']) && !empty($i['Encomenda']['codigo_rastreamento']) ? ' done':(!empty($i['Encomenda']['data_coleta']) && empty($i['Encomenda']['codigo_rastreamento']) ? ' half':''))?>"></span>
                                    <div class="circle<?=(!empty($i['Encomenda']['data_coleta']) && !empty($i['Encomenda']['codigo_rastreamento']) ? ' done':(!empty($i['Encomenda']['data_coleta']) && empty($i['Encomenda']['codigo_rastreamento']) ? ' active':''))?>">
                                        <span class="progress-steps-label"><i class="fa fa-truck"></i></span>
                                        <span class="title">Transporte</span>
                                    </div>
                                    <span class="bar<?=(!empty($i['Encomenda']['codigo_rastreamento']) && !empty($i['Encomenda']['data_conclusao']) ? ' done':(!empty($i['Encomenda']['codigo_rastreamento']) && empty($i['Encomenda']['data_conclusao']) ? ' half':''))?>"></span>
                                    <div class="circle<?=(!empty($i['Encomenda']['codigo_rastreamento']) && !empty($i['Encomenda']['data_conclusao']) ? ' done':(!empty($i['Encomenda']['codigo_rastreamento']) && empty($i['Encomenda']['data_conclusao']) ? ' active':''))?>">
                                        <span class="progress-steps-label"><i class="fa fa-check"></i></span>
                                        <span class="title">Entrega</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-center"><?=(!empty($i['Encomenda']['data_previsao']) && empty($i['Encomenda']['data_conclusao']) ? "Previsão de Entrega {$i['Encomenda']['data_previsao']}":'')?></p>
                    </td>
                </tr>
                <?php } ?>
            </table>
            <div class="panel-footer text-center">
                <?= $this->Paginator->numbers(['prev' => '<', 'next' => '>']) ?>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <?=$this->element('sidebar_right')?>
    </div>
</div>
<script>
$(document).ready(function() {
var i = 1;
$('.progress .circle').removeClass().addClass('circle');
$('.progress .bar').removeClass().addClass('bar');
setInterval(function() {
$('.progress .circle:nth-of-type(' + i + ')').addClass('active');

$('.progress .circle:nth-of-type(' + (i - 1) + ')').removeClass('active').addClass('done');

$('.progress .circle:nth-of-type(' + (i - 1) + ') .label').html('&#10003;');

$('.progress .bar:nth-of-type(' + (i - 1) + ')').addClass('active');

$('.progress .bar:nth-of-type(' + (i - 2) + ')').removeClass('active').addClass('done');

i++;

if (i == 0) {
$('.progress .bar').removeClass().addClass('bar');
$('.progress div.circle').removeClass().addClass('circle');
i = 1;
}
}, 1000);
});
</script>
