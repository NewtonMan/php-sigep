    <fieldset>
        <legend>Atividades Logísticas <a href="#back" onclick="window.history.back();" title="Voltar"><img src="/img/btn-voltar.png"></a></legend>
        <?php echo $this->Session->flash(); ?>
        <form method="get" id="form-pesquisa" style="display: none;">
            <div class="well">
                <h1>Filtrar por Fluxo Logístico</h1>
                <label>
                    Fluxo Logístico:<br/>
                    <select name="flid">
                        <option value="">-- qualquer Fluxo --</option>
                        <?
                        foreach ($opcoes_fluxos_logisticos as $item) {
                            $nome = $item['Empresa']['nome'];
                            if (!empty($item['FluxoLogistico']['id'])){
                                $parents = $fluxo->getPath($item['FluxoLogistico']['id']);
                                foreach ($parents as $y => $subitem) {
                                    $nome .= ' -> '.$subitem['FluxoLogistico']['nome'];
                                }
                            }
                            echo "<option value=\"{$item['FluxoLogistico']['id']}\"".(@$_GET['flid']==$item['FluxoLogistico']['id'] ? ' selected':'').">{$nome}</option>";
                        }
                        ?>
                    </select>
                </label><br/>
                <label>
                    Código<br/>
                    <input type="text" name="codigo" value="<?=@$_GET['codigo']?>" />
                </label><br/>
                <label>
                    Direção<br/>
                    <select name="direcao">
                        <option value=""> - qualquer - </option>
                        <?
                        foreach ($lDirecao as $value => $text){
                            echo "<option value=\"{$value}\"".(@$_GET['direcao']==$value ? ' selected':'').">{$text}</option>";
                        }
                        ?>
                    </select>
                </label><br/>
                <label>
                    Situação<br/>
                    <select name="situacao">
                        <option value=""> - qualquer - </option>
                        <?
                        foreach ($opcoes_analitico_situacao as $value => $text){
                            echo "<option value=\"{$value}\"".(@$_GET['situacao']==$value ? ' selected':'').">{$text}</option>";
                        }
                        ?>
                    </select>
                </label><br/>
                <label>
                    Prazo de<br/>
                    <input type="text" name="prazo_de" value="<?=@$_GET['prazo_de']?>" class="datetime" />
                </label><br/>
                <label>
                    Prazo até<br/>
                    <input type="text" name="prazo_ate" value="<?=@$_GET['prazo_ate']?>" class="datetime" />
                </label><br/>
                <label>
                    Nome do Destinatário/Remetente<br/>
                    <input type="text" name="fantasia" value="<?=@$_GET['fantasia']?>" />
                </label><br/>
                <label>
                    Nome da Cidade<br/>
                    <input type="text" name="municipio" value="<?=@$_GET['municipio']?>" />
                </label><br/>
                <label>
                    Sigla do Estado<br/>
                    <input type="text" name="uf" value="<?=@$_GET['uf']?>" />
                </label>
                <input type="submit" value="Ok" class="btn btn-primary" />
            </div>
        </form>
        <input type="button" value="Formulário de Pesquisa" onclick="$('#form-pesquisa').toggle(500);" class="btn btn-default"><a href="<?=$_SERVER['REQUEST_URI'].(isset($_GET['flid']) ? '&':'?')?>exportar=1"  class="btn btn-default">Exportar Tarefas</a><br/><br/>
        <table class="table table-striped" width="100%">
            <tr>
                <th width="60">Código</th>
                <th width="40%">Fluxo Logístico / Destino</th>
                <th width="20%">Direção</th>
                <th width="20%">Prazo</th>
                <th width="20%">Situação</th>
            </tr>
            <?
            $listaTotal = count($lista);
            if ($listaTotal==0){
                ?>
            <tr>
                <td colspan="5">Não existem atividades com estes critérios.</td>
            </tr>
                <?
            }
            foreach ($lista as $item) {?>
            <tr>
                <td nowrap onmouseover="this.style.cursor='pointer';" onclick="window.location.href='/analitico/fluxo_logistico/controle/<?=$item['PedidoTransporte']['id']?>';"><?=$item['PedidoTransporte']['id']?><?=(!empty($item['PedidoTransporte']['movimento_id']) ? "<br/>OS {$item['PedidoTransporte']['movimento_id']}":'')?><?=(!empty($item['PedidoTransporte']['frete_id']) ? "<br/>Frete {$item['PedidoTransporte']['frete_id']}":'')?></td>
                <td onmouseover="this.style.cursor='pointer';" onclick="window.location.href='/analitico/fluxo_logistico/controle/<?=$item['PedidoTransporte']['id']?>';">
                    <?echo $fluxo->montaPath($item['PedidoTransporte']['fluxo_logistico_id']);?><br/>
                    <?=($item['PedidoTransporte']['direcao']=='coleta' ? $item['Origem']['fantasia']:$item['Destino']['fantasia'])?>
                </td>
                <td onmouseover="this.style.cursor='pointer';" onclick="window.location.href='/analitico/fluxo_logistico/controle/<?=$item['PedidoTransporte']['id']?>';"><?=$lDirecao[$item['PedidoTransporte']['direcao']]?></td>
                <td onmouseover="this.style.cursor='pointer';" onclick="window.location.href='/analitico/fluxo_logistico/controle/<?=$item['PedidoTransporte']['id']?>';">
                    <?
                    if ($item['PedidoTransporte']['direcao']=='coleta'){
                        echo "Coletar até ".DataHoraFromSQL($item['PedidoTransporte']['prazo_coleta']);
                    } elseif ($item['PedidoTransporte']['direcao']=='entrega'){
                        echo "Entregar até ".DataHoraFromSQL($item['PedidoTransporte']['prazo_entrega']);
                    } elseif ($item['PedidoTransporte']['direcao']=='coleta_entrega'){
                        echo "Coletar até ".DataHoraFromSQL($item['PedidoTransporte']['prazo_coleta'])."<br/>";
                        echo "Entregar até ".DataHoraFromSQL($item['PedidoTransporte']['prazo_entrega']);
                    }
                    ?>
                </td>
                <td onmouseover="this.style.cursor='pointer';" onclick="window.location.href='/analitico/fluxo_logistico/controle/<?=$item['PedidoTransporte']['id']?>';"><?=$item['PedidoTransporteStatus']['nome']?></td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td colspan="5">
                    <?if ($this->Paginator->hasPage('PedidoTransporte', 2)):?>
                    <ul class="pagination">
                        <?if ($this->Paginator->hasPrev()){?><li><?=$this->Paginator->prev('Anterior');?></li><?}?>
                        <?=$this->Paginator->numbers(array('separator'=>'', 'tag'=>'li', 'currentClass'=>'active', 'currentTag'=>'li'));?>
                        <?if ($this->Paginator->hasNext()){?><li><?=$this->Paginator->next('Próxima');?></li><?}?>
                    </ul>
                    <?endif;?>
                </td>
            </tr>
        </table>
    </fieldset>