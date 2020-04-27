<div id="modalOS" class="modal fade" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">Impressão de OS</h3>
            </div>
            <div class="modal-body">
                <iframe name="os-iframe" id="os-iframe" width="100%" height="300"></iframe>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Fechar</button>
            </div>
        </div>
    </div>
</div>
<form method="post" enctype="multipart/form-data">
    <fieldset>
        <legend>Controle Analítico <a href="#back" onclick="window.history.back();" title="Voltar"><img src="/img/btn-voltar.png"></a></legend>
        <?php echo $this->Session->flash(); ?>
        <h1>Atividade <?=$controle_data['PedidoTransporte']['id']?></h1>
        <table class="table table-striped" width="100%">
            <tr>
                <td width="50%">
                    <b>Responsável pela operação:</b><br/>
                    <?=$controle_data['Empresa']['nome']?>
                </td>
                <td width="50%">
                    <b>Fluxo Logístico:</b><br/>
                    <?
                    $nome = "";
                    if (!empty($controle_data['FluxoLogistico']['id'])){
                        $parents = $fluxo->getPath($controle_data['FluxoLogistico']['id']);
                        foreach ($parents as $y => $subitem) {
                            $nome .= ' -> '.$subitem['FluxoLogistico']['nome'];
                        }
                    }
                    echo " {$nome}";
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <b>Etapas do Frete <?=$controle_data['Frete']['id']?> (<?=$controle_data['Frete']['status']?>)</b><br/>
                    <ol>
                    <?
                    foreach ($controle_data['Frete']['FreteEtapa'] as $etapa_x => $etapa_data){
                        echo "<li".($etapa_data['concluida']==1 ? ' style="color: green"':'').">";
                        echo ($etapa_data['concluida']==1 ? 'CONCLUÍDA - ':'AGUARDANDO - ');
                        switch ($etapa_data['modal']){
                            case 'rodoviario':
                                echo "Motorista {$etapa_data['Motorista']['nome_completo']}".($etapa_data['Motorista']['terceirizado']==1 ? ' (agregado)':'');
                                if (!empty($etapa_data['Motorista']['telefone_fixo1'])) echo " - Tel1: {$etapa_data['Motorista']['telefone_fixo1']}";
                                if (!empty($etapa_data['Motorista']['telefone_fixo2'])) echo " - Tel2: {$etapa_data['Motorista']['telefone_fixo2']}";
                                if (!empty($etapa_data['Motorista']['telefone_movel1'])) echo " - {$etapa_data['Motorista']['telefone_movel1_operadora']}: {$etapa_data['Motorista']['telefone_movel1']}";
                                if (!empty($etapa_data['Motorista']['telefone_movel2'])) echo " - {$etapa_data['Motorista']['telefone_movel2_operadora']}: {$etapa_data['Motorista']['telefone_movel2']}";
                                break;
                            
                            case 'aereo':
                                echo "Via {$etapa_data['CiaAerea']['fantasia']} - Origem: {$etapa_data['AeroportoDe']['info']} / Destino: {$etapa_data['AeroportoPara']['info']}";
                                break;
                        }
                        echo "</li>";
                    }
                    ?>
                    </ol>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table class="table">
                        <tr>
                            <?if ($controle_data['PedidoTransporte']['direcao']=='coleta'){?>
                            <td>
                            <b>Retirar/Coletar Material<?=($controle_data['PedidoTransporte']['coletado_em']!=null ? ' Realizado em '.DataHoraFromSQL($controle_data['PedidoTransporte']['coletado_em']):'')?>:</b><br/>
                            <u><?=$controle_data['Origem']['fantasia']?></u> <?=$controle_data['Origem']['telefones']?> - <a href="sip:0<?=onlyNumbers($controle_data['Origem']['telefones'])?>" class="btn btn-default">Ligar</a><br/>
                            CPF/CNPJ: <?=$controle_data['Origem']['cpf_cnpj']?> RG/Insc. Est.: <?=$controle_data['Origem']['rg_insc_estadual']?><br/>
                            Endereço: <?=$controle_data['Origem']['endereco']?>, <?=$controle_data['Origem']['numero']?>, <?=$controle_data['Origem']['complemento']?><br/>
                            CEP: <?=$controle_data['Origem']['cep']?> - <?=$controle_data['Origem']['municipio']?> / <?=$controle_data['Origem']['uf']?><br/>
                            <b>Prazo de Coleta:</b> <?=$controle_data['PedidoTransporte']['prazo_coleta']?><br/>
                            <?=$this->Form->input('coletado_em', array('type'=>'text','class'=>'mask_datetime','value'=>($controle_data['PedidoTransporte']['coletado_em'])));?>
                            </td>
                            <?} elseif ($controle_data['PedidoTransporte']['direcao']=='entrega'){?>
                            <td>
                            <b>Entregar Material<?=($controle_data['PedidoTransporte']['entregue_em']!=null ? ' Realizado em '.DataHoraFromSQL($controle_data['PedidoTransporte']['entregue_em']):'')?>:</b><br/>
                            <u><?=$controle_data['Destino']['fantasia']?></u> <?=$controle_data['Destino']['telefones']?> - <a href="sip:0<?=onlyNumbers($controle_data['Destino']['telefones'])?>" class="btn btn-default">Ligar</a><br/>
                            CPF/CNPJ: <?=$controle_data['Destino']['cpf_cnpj']?> RG/Insc. Est.: <?=$controle_data['Destino']['rg_insc_estadual']?><br/>
                            Endereço: <?=$controle_data['Destino']['endereco']?>, <?=$controle_data['Destino']['numero']?>, <?=$controle_data['Destino']['complemento']?><br/>
                            CEP: <?=$controle_data['Destino']['cep']?> - <?=$controle_data['Destino']['municipio']?> / <?=$controle_data['Destino']['uf']?><br/>
                            <b>Prazo de Entrega:</b> <?=$controle_data['PedidoTransporte']['prazo_entrega']?><br/>
                            <?=$this->Form->input('entregue_em', array('type'=>'text','class'=>'mask_datetime','value'=>($controle_data['PedidoTransporte']['entregue_em'])));?>
                            </td>
                            <?} elseif ($controle_data['PedidoTransporte']['direcao']=='coleta_entrega'){?>
                            <td>
                            <b>Retirar/Coletar Material<?=($controle_data['PedidoTransporte']['coletado_em']!=null ? ' Realizado em '.DataHoraFromSQL($controle_data['PedidoTransporte']['coletado_em']):'')?>:</b><br/>
                            <u><?=$controle_data['Origem']['fantasia']?></u> <?=$controle_data['Origem']['telefones']?> - <a href="sip:0<?=onlyNumbers($controle_data['Origem']['telefones'])?>" class="btn btn-default">Ligar</a><br/>
                            CPF/CNPJ: <?=$controle_data['Origem']['cpf_cnpj']?> RG/Insc. Est.: <?=$controle_data['Origem']['rg_insc_estadual']?><br/>
                            Endereço: <?=$controle_data['Origem']['endereco']?>, <?=$controle_data['Origem']['numero']?>, <?=$controle_data['Origem']['complemento']?><br/>
                            CEP: <?=$controle_data['Origem']['cep']?> - <?=$controle_data['Origem']['municipio']?> / <?=$controle_data['Origem']['uf']?><br/>
                            <b>Prazo de Coleta:</b> <?=$controle_data['PedidoTransporte']['prazo_coleta']?><br/>
                            <?=$this->Form->input('coletado_em', array('type'=>'text','class'=>'mask_datetime','value'=>($controle_data['PedidoTransporte']['coletado_em'])));?>
                            </td>
                            <td>
                            <b>Entregar Material<?=($controle_data['PedidoTransporte']['entregue_em']!=null ? ' Realizado em '.($controle_data['PedidoTransporte']['entregue_em']):'')?>:</b><br/>
                            <u><?=$controle_data['Destino']['fantasia']?></u> <?=$controle_data['Destino']['telefones']?> - <a href="sip:0<?=onlyNumbers($controle_data['Destino']['telefones'])?>" class="btn btn-default">Ligar</a><br/>
                            CPF/CNPJ: <?=$controle_data['Destino']['cpf_cnpj']?> RG/Insc. Est.: <?=$controle_data['Destino']['rg_insc_estadual']?><br/>
                            Endereço: <?=$controle_data['Destino']['endereco']?>, <?=$controle_data['Destino']['numero']?>, <?=$controle_data['Destino']['complemento']?><br/>
                            CEP: <?=$controle_data['Destino']['cep']?> - <?=$controle_data['Destino']['municipio']?> / <?=$controle_data['Destino']['uf']?><br/>
                            <b>Prazo de Entrega:</b> <?=$controle_data['PedidoTransporte']['prazo_entrega']?><br/>
                            <?=$this->Form->input('entregue_em', array('type'=>'text','class'=>'mask_datetime','value'=>($controle_data['PedidoTransporte']['entregue_em'])));?>
                            </td>
                            <?}?>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <font color="red">ATENÇÃO:</font> Mudanças acima devem ser detalhadas no histórico e salvas no botão ao final da página.
                </td>
            </tr>
        </table>
    </fieldset>
    <fieldset>
        <legend>Históricos</legend>
        <table class="table table-striped" width="100%">
            <tr>
                <td colspan="2">
                    <b>Atualizar Situacão para:</b><br/><select name="data[pedido_transporte_status_id]"><option value=""> - escolha - </option><?
                    foreach ($opcoes_analitico_situacao as $value => $key) {
                        echo "<option value=\"{$value}\"".($controle_data['PedidoTransporte']['pedido_transporte_status_id']==$value ? ' selected':'').">{$key}</option>";
                    }
                    ?></select>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <b>Mensagem:</b><br/>
                    <textarea name="data[mensagem]"></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <b>Anexar: (máx. 10MB por item)</b><br/>
                    <input type="file" name="data[arquivo]" />
                </td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" value="Salvar" class="btn btn-primary"></td>
            </tr>
        </table>
        <?
        $historicos = count(@$controle_data['PedidoTransporteHistorico']);
        if ($historicos==0){
            echo 'Nenhum histórico registrado até o momento.';
        } else {
            ?>
        <table width="100%" class="table table-striped">
            <?
            foreach ($controle_data['PedidoTransporteHistorico'] as $historico) {
                ?>
            <tr>
                <td width="20%"><b><?=(@$historico['Usuario']['nome'].@$historico['AcessoClienteUsuario']['nome'])?></b><br/><?=DataHoraFromSQL($historico['created'])?></td>
                <td><?=nl2br($historico['mensagem']);?></td>
            </tr>
                <?
            }
            ?>
        </table>
            <?
        }
        ?>
    </fieldset>
</form>