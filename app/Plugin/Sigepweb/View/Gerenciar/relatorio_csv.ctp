Relatório SIGEPWEB
"Cliente";"Serviço";"Serie";"NF-e";"ETIQUETA";"Postagem";"Cidade";"Estado"
<?
$total = count($lista);
if ($total == 0) {
    ?>Não existem registros com estes critérios.<?
} else {
    foreach ($lista as $x => $item) {
        ?>
        "<?= $item['Emitente']['fantasia']?>";"<?= $item['Servico']['name'] ?>";"<?= $item['Encomenda']['nfe_serie']?>";"<?= $item['Encomenda']['nfe_numero']?>";"<?= $item['Etiqueta']['codigo_com_dv']?>";"<?=$item['Etiqueta']['created']?>";"<?= $item['DestinoLocal']['municipio']?>";"<?= $item['DestinoLocal']['uf']?>"<?= "\n" ?><? } ?>
    <?
}?>
