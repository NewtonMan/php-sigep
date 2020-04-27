<form method="post">
    <fieldset>
        <legend>Cronograma de Atividades Logísticas <a href="#back" onclick="window.history.back();" title="Voltar"><img src="/img/btn-voltar.png"></a></legend>
        <?php echo $this->Session->flash(); ?>
        <table class="table table-striped" width="100%">
            <tr>
                <th width="40%">Destino</th>
                <th width="30%">Positivação</th>
                <th width="30%">Desativação</th>
            </tr>
            <? foreach ($lista as $item) {?>
            <tr>
                <td><?=$item['Destinatario']['fantasia']?> - <?=$item['Destinatario']['bairro']?> - <?=$item['Destinatario']['municipio']?> - <?=$item['Destinatario']['uf']?></td>
                <td><input style="width: 130px;" type="text" name="positivar[<?=$item['FluxoLogisticoDestino']['id']?>]" value="<?=(!empty($item['FluxoLogisticoDestino']['positivar_em']) ? DataHoraFromSQL($item['FluxoLogisticoDestino']['positivar_em']):'')?>" class="mask_datetime"> dd/mm/aaaa hh:mm</td>
                <td><input style="width: 130px;" type="text" name="coletar[<?=$item['FluxoLogisticoDestino']['id']?>]" value="<?=(!empty($item['FluxoLogisticoDestino']['coletar_em']) ? DataHoraFromSQL($item['FluxoLogisticoDestino']['coletar_em']):'')?>" class="mask_datetime"> dd/mm/aaaa hh:mm</td>
            </tr>
            <? } ?>
            <tr>
                <td colspan="3"><input type="submit" value="Salvar Cronograma" class="btn btn-primary"></td>
            </tr>
        </table>
    </fieldset>
</form>