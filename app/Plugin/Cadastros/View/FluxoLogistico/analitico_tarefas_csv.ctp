"Atividades Log�sticas"
"C�digo";"OS";"Frete";"Centro de Custo";"Dire��o";"Origem/Destino";"Prazo";"Situa��o"
<?foreach ($lista as $item){?>
"<?=$item['PedidoTransporte']['id']?>";"<?=$item['PedidoTransporte']['movimento_id']?>";"<?=$item['PedidoTransporte']['frete_id']?>";"<?=$fluxo->montaPath($item['PedidoTransporte']['fluxo_logistico_id'])?>";"<?=($item['PedidoTransporte']['direcao']=='coleta' ? "Coleta":'Entrega')?>";"<?=($item['PedidoTransporte']['direcao']=='coleta' ? $item['Origem']['fantasia']:$item['Destino']['fantasia'])?>";"<?=($item['PedidoTransporte']['direcao']=='coleta' ? $item['PedidoTransporte']['prazo_coleta']:$item['PedidoTransporte']['prazo_entrega'])?>";"<?=$item['PedidoTransporteStatus']['nome']?>"
<? } ?>

