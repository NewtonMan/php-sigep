Relat�rio SIGEPWEB
"Cliente";"Servi�o";"Serie";"NF-e";"ETIQUETA";"Postagem";"Cidade";"Estado"
<?
$total = count($lista);
if ($total == 0) {
    ?>N�o existem registros com estes crit�rios.<?
} else {
    foreach ($lista as $x => $item) {
        ?>
        "<?= $item['Emitente']['fantasia']?>";"<?= $item['Servico']['name'] ?>";"<?= $item['Encomenda']['nfe_serie']?>";"<?= $item['Encomenda']['nfe_numero']?>";"<?= $item['Etiqueta']['codigo_com_dv']?>";"<?=$item['Etiqueta']['created']?>";"<?= $item['DestinoLocal']['municipio']?>";"<?= $item['DestinoLocal']['uf']?>"<?= "\n" ?><? } ?>
    <?
}?>
