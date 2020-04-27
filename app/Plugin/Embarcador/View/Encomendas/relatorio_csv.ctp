Relatório Embarcador
"nfe_chave";"nfe_serie";"nfe_numero";"data_emissa";"Proprietário";"Destinatário";"Transportadora";"Local_entrega";"Cidade";"UF";"Valor";"Frete";"Romaneio";"Coleta";"Previsão";"Conclusão";"ETIQUETA";"STATUS"
<?
$total = count($lista);
if ($total == 0) {
    ?>Não existem registros com estes critérios.<?
} else {
    foreach ($lista as $x => $item) {
        ?>
        "'<?= $item['Encomenda']['nfe_chave'] ?>'";"<?= $item['Encomenda']['nfe_serie'] ?>";"<?= $item['Encomenda']['nfe_numero'] ?>";"<?= $item['Encomenda']['data_emissao'] ?>";"<?= $item['Embarcador']['fantasia'] ?>";"<?= $item['Destinatario']['fantasia'] ?>";"<?= $item['Transportador']['fantasia'] ?>";"<?= $item['LocalEntrega']['xLgr'] ?>, <?= $item['LocalEntrega']['nro'] ?>";"<?= $item['City']['name'] ?>";"<?= $item['City']['uf'] ?>";"<?= $item['Encomenda']['valor_declarado'] ?>";"<?= $item['Encomenda']['valor_frete'] ?>";"<?= $item['Encomenda']['data_romaneio'] ?>";"<?= $item['Encomenda']['data_coleta'] ?>";"<?= $item['Encomenda']['data_previsao'] ?>";"<?= $item['Encomenda']['data_conclusao'] ?>";"<?= $item['Encomenda']['codigo_rastreamento'] ?>";"<?= $item['Status']['name'] ?>"<?= "\n" ?><? } ?>
    <?
}?>