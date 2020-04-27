<div class="container-fluid" style="margin-top: 15px;">
    <div class="row">
        <?=$this->Session->flash()?>
        <div class="col-md-6">
            <?=$this->Form->create('Encomenda');?>
            <div class="well">
                <h2>
                    Dados da Encomenda
                    <a href="javascript:void(abrePopUp('/embarcador/encomendas/danfe/<?=$this->request->data['Encomenda']['id']?>'));" class="btn btn-info btn-xs">DANFE</a>
                    <a href="/embarcador/encomendas/cancelar/<?=$this->request->data['Encomenda']['id']?>" class="btn btn-danger btn-xs" onclick="return confirm('Desejarealmente Cancelar esta Encomenda?');">Cancelar</a>
                </h2>
                <div class="row">
                    <div class="col-sm-4"><?=$this->Form->input('Destinatario.fantasia', ['class' => 'form-group-sm', 'label' => 'Destinatario', 'readonly']);?></div>
                    <div class="col-sm-4"><?=$this->Form->input('Embarcador.fantasia', ['class' => 'form-group-sm', 'label' => 'Embarcador', 'readonly']);?></div>
                    <div class="col-sm-4"><?=$this->Form->input('Transportador.fantasia', ['class' => 'form-group-sm', 'label' => 'Transportador', 'readonly']);?></div>
                </div>
                <?php
                if ($this->request->data['Encomenda']['tipo_encomenda_id']==1){
                ?>
                <div class="row">
                    <div class="col-sm-2"><?=$this->Form->input('Encomenda.data_emissao', ['class' => 'form-group-sm', 'type' => 'text', 'label' => 'Data NF', 'readonly']);?></div>
                    <div class="col-sm-2"><?=$this->Form->input('Encomenda.nfe_serie', ['class' => 'form-group-sm', 'label' => 'Série NF', 'readonly']);?></div>
                    <div class="col-sm-2"><?=$this->Form->input('Encomenda.nfe_numero', ['class' => 'form-group-sm', 'label' => 'Número NF', 'readonly']);?></div>
                    <div class="col-sm-4"><?=$this->Form->input('Encomenda.nfe_chave', ['class' => 'form-group-sm', 'label' => 'Chave NF-e', 'readonly']);?></div>
                    <div class="col-sm-2"><?=$this->Form->input('Encomenda.valor_declarado', ['class' => 'form-group-sm', 'type' => 'text', 'label' => 'Valor Carga', 'readonly']);?></div>
                </div>
                <?php
                } else { // DECLARACAO
                    ?>
                    <?php
                }
                ?>
                <div class="row">
                    <div class="col-sm-12">
                        <p>
                            <strong>Endereço para Entrega</strong><br/>
                            <?=$this->request->data['LocalEntrega']['xLgr']?>, <?=$this->request->data['LocalEntrega']['nro']?> <?=(empty($this->request->data['LocalEntrega']['xCpl']) ? '':" - {$this->request->data['LocalEntrega']['xCpl']}")?>
                            <?=(empty($this->request->data['LocalEntrega']['xBairro']) ? '':" - {$this->request->data['LocalEntrega']['xBairro']}")?> - <?=$this->request->data['LocalEntrega']['xMun']?> - <?=$this->request->data['LocalEntrega']['UF']?> <?=(empty($this->request->data['LocalEntrega']['CEP']) ? '':" - CEP {$this->request->data['LocalEntrega']['CEP']}")?>
                            <?=(empty($this->request->data['Destinatario']['telefones']) ? '':" - Tel: {$this->request->data['Destinatario']['telefones']}")?><br>
                            <?= exibirCpfCnpj($this->request->data['Destinatario']['cpf_cnpj']) ?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <p><strong>Observações</strong><br/><?= nl2br($this->request->data['Encomenda']['observacoes'])?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <p>
                            <label>Modal</label><br/>
                            <?=$this->request->data['Modal']['name']?>
                        </p>
                    </div>
                    <div class="col-sm-8">
                        <p>
                            <label>Contratação do Frete</label><br/>
                            <?=$this->request->data['ModalidadeFrete']['description']?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-8">
                        <?=$this->Form->input('Encomenda.status_id', ['class' => 'form-group-sm', 'label' => 'Status', 'size' => 5, 'options' => $status]);?>
                        <div class="row">
                            <div class="col-md-6">
                                <?=$this->Form->input('Encomenda.ocorrencia_responsavel_id', ['class' => 'form-group-sm', 'label' => 'Transferir Ocorrência', 'empty' => ' - Novo Responsável - ', 'options' => $usuarios]);?>
                            </div>
                            <div class="col-md-6">
                                <?=$this->Form->input('Encomenda.transportador_id', ['class' => 'form-group-sm', 'label' => 'Transferir Transportadora', 'empty' => ' - Nova Responsável - ', 'options' => $transportadoras]);?>
                            </div>
                        </div>
                        <?=$this->Form->input('Encomenda.historico', ['class' => 'form-group-sm', 'type' => 'textarea', 'label' => 'Registrar no Histórico']);?>
                    </div>
                    <div class="col-sm-4">
                        <?=$this->Form->input('Encomenda.data_romaneio', ['class' => 'form-group-sm mask_date2', 'type' => 'text', 'label' => 'Romaneio']);?>
                        <?=$this->Form->input('Encomenda.data_coleta', ['class' => 'form-group-sm mask_date2', 'type' => 'text', 'label' => 'Coleta']);?>
                        <?=$this->Form->input('Encomenda.codigo_rastreamento', ['class' => 'form-group-sm', 'type' => 'text', 'label' => 'Código Rastreio']);?>
                        <?=$this->Form->input('Encomenda.data_previsao', ['class' => 'form-group-sm mask_date2', 'type' => 'text', 'label' => 'Previsão']);?>
                        <?=$this->Form->input('Encomenda.data_conclusao', ['class' => 'form-group-sm mask_date2', 'type' => 'text', 'label' => 'Conclusão']);?>
                        <button type="submit" class="btn btn-success btn-block">Publicar no Histórico</button>
                    </div>
                </div>
            </div>
            <?=$this->Form->end();?>
        </div>
        <div class="col-md-6">
            <?php
            if ($this->request->data['Transportador']['correios']==1 && !empty($this->request->data['Encomenda']['codigo_rastreamento'])){
                ?>
            <iframe src="https://linkcorreios.com.br/<?=$this->request->data['Encomenda']['codigo_rastreamento']?>" id="site-correio" width="100%" height="400" frameborder="0"></iframe><br/>
            LINK RASTREIO: <a href="https://linkcorreios.com.br/<?=$this->request->data['Encomenda']['codigo_rastreamento']?>">https://linkcorreios.com.br/<?=$this->request->data['Encomenda']['codigo_rastreamento']?></a>
                <?php
            } elseif ($this->request->data['Transportador']['cpf_cnpj']==73939449000193){
                $eids = [11860, 11970, 12040];
                foreach ($eids as $eidi){
                    $data = file_get_contents("https://tracking.totalexpress.com.br/poupup_track.php?reid={$eidi}&pedido={$this->request->data['Encomenda']['codigo_rastreamento']}&nfiscal={$this->request->data['Encomenda']['nfe_numero']}");
                    if ($data!='Dados não encontrados!') $eid = $eidi;
                }
                ?>
            <iframe src="https://tracking.totalexpress.com.br/poupup_track.php?reid=<?=$eid?>&pedido=<?=$this->request->data['Encomenda']['codigo_rastreamento']?>&nfiscal=<?=$this->request->data['Encomenda']['nfe_numero']?>" id="site-correio" width="100%" height="400" frameborder="0"></iframe><br/>
            LINK RASTREIO: <a href="https://tracking.totalexpress.com.br/poupup_track.php?reid=<?=$eid?>&pedido=<?=$this->request->data['Encomenda']['codigo_rastreamento']?>&nfiscal=<?=$this->request->data['Encomenda']['nfe_numero']?>">http://tracking.totalexpress.com.br/poupup_track.php?reid=<?=$eid?>&pedido=<?=$this->request->data['Encomenda']['codigo_rastreamento']?>&nfiscal=<?=$this->request->data['Encomenda']['nfe_numero']?></a>
                <?php
            } else{
                ?>
            <iframe src="https://ics.totalexpress.com.br/remetentes/search.php" width="100%" height="400" frameborder="0"></iframe>
                <!-- Início do código Total Express - Tracking Público de Encomendas - v.1.0 -->
                <!--a href="#" onclick="window.open('http://tracking.totalexpress.com.br/tracking/10932', 'tracking_totalexpress', 'width=580, height=400, location=0, scrollbars=1');">
                    <img src="http://static.totalexpress.com.br/site/images/trackingpub.gif" border="0" />
                </a-->
                <!-- Fim do código Total Express - Tracking Público de Encomendas -->
                <?php
            }
            ?>
            <?php
            $h = [];
            foreach ($this->request->data['Historico'] as $i){
                $iteraction = (empty($i['Usuario']['nome']) ? '<i class="fa fa-android"></i> ROBÔ':$i['Usuario']['nome']) . ' em ' . DataHoraHumanize(DataHoraFromSQL($i['created']), false);
                if (!isset($h[$iteraction])){
                    $h[$iteraction] = [];
                }
                $h[$iteraction][] = $i['mensagem'];
            }
            foreach ($h as $i => $is){ ?>
            <div class="well well-sm">
                <small><?=$i?></small><br/>
                <ul><?php
                    foreach ($is as $li){
                        echo "<li>{$li}</li>";
                    }
                    ?></ul>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<script>
    $('iframe#site-correio').on('load', function(event){
        $('iframe#site-correio').contents().find('form').submit();
    });
</script>