<?php
$opcoes['orig'] = array(
    ''=>'',
    '0'=>'0 - Nacional, exceto as indicadas nos códigos 3 a 5;',
    '1'=>'1 - Estrangeira - Importação direta, exceto a indicada no código 6;',
    '2'=>'2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7;',
    '3'=>'3 - Nacional, mercadoria ou bem com Conteúdo de Importação superior a 40%;',
    '4'=>'4 - Nacional, cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes;',
    '5'=>'5 - Nacional, mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%;',
    '6'=>'6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX;',
    '7'=>'7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante em lista da CAMEX.',
);
echo $this->Form->create('Produto', array('type' => 'file', 'class'=>'form-horizontal')); ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title"><?php echo __('Formulário de Produto'); ?></h1>
    </div>
    <div class="panel-body">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1" data-toggle="tab"<?=(isset($problem['produto']) ? ' style="color: red;"':'')?>>Básico</a></li>
            <li><a href="#tab3" data-toggle="tab">Clientes</a></li>
            <li><a href="#tab4" data-toggle="tab">Estoque Mínimo por Fluxo</a></li>
            <li><a href="#tab5" data-toggle="tab">Impostos</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab1">
                <div class="form-group">
                    <label for="ProdutoCodigo" class="control-label col-md-1">Código/cProd NFe</label>
                    <div class="col-md-2"><?echo $this->Form->text('codigo', array('type'=>'text', 'class'=>'form-control'));?></div>
                    <label for="ProdutoCodigoCliente" class="control-label col-md-2">Código do Cliente</label>
                    <div class="col-md-2"><?echo $this->Form->text('codigo_cliente', array('type'=>'text', 'class'=>'form-control'));?></div>
                    <label for="barcode" class="control-label col-md-2">Código de Barras</label>
                    <div class="col-md-3"><?echo $this->Form->text('codigo_barras', array('type'=>'text', 'id'=>'barcode', 'class'=>'form-control'));?></div>
                </div>
                <div class="form-group required">
                    <label for="ProdutoNome" class="control-label col-md-1">Nome</label>
                    <div class="col-md-5"><?echo $this->Form->text('nome', array('class'=>'form-control'));?></div>
                    <label for="ProdutoNomeAbrev" class="control-label col-md-1">Abreviação</label>
                    <div class="col-md-5"><?echo $this->Form->text('nome_abrev', array('class'=>'form-control'));?></div>
                </div>
                <div class="form-group required">
                    <label for="ProdutoDescricao" class="control-label col-md-1">Descrição</label>
                    <div class="col-md-11"><?echo $this->Form->textarea('descricao', array('class'=>'form-control'));?></div>
                </div>
                <div class="form-group required">
                    <label for="ProdutoUnidadeComercial" class="control-label col-md-2">Unidade Comercial</label>
                    <div class="col-md-2"><?echo $this->Form->select('unidade_comercial', array('un'=>'Unidade','pc'=>'Pacote','kg'=>'Kilo','tn'=>'Tonelada','lt'=>'Litro','m2'=>'Metro Quadrado','m3'=>'Metro Cúbico'), array('class'=>'form-control', 'empty'=>false));?></div>
                    <label for="ProdutoNfNCM" class="control-label col-md-2">NCM (<a href="javascript:void(abrePopUp('https://www4.receita.fazenda.gov.br/simulador/PesquisarNCM.jsp'));">Pesquisar</a>)</label>
                    <div class="col-md-2"><?echo $this->Form->text('nf_NCM', array('class'=>'form-control mask_int','type'=>'text'));?></div>
                    <label for="ProdutoNfEXTIPI" class="control-label col-md-1">EXTIPI</label>
                    <div class="col-md-2"><?echo $this->Form->text('nf_EXTIPI', array('class'=>'form-control','type'=>'text'));?></div>
                </div>
                <?=$this->Form->hidden('ProdutoValorVenda.id');?>
                <div class="form-group">
                    <label for="ProdutoValorVendaValorVenda" class="control-label col-md-4">Valor de Venda deste Produto</label>
                    <div class="col-md-8"><?=$this->Form->text('ProdutoValorVenda.valor_venda', array('class'=>'form-control mask_money'));?></div>
                </div>
                <div class="form-group required">
                    <label for="ProdutoUnidadeComercial" class="control-label col-md-2">Origem</label>
                    <div class="col-md-2"><?echo $this->Form->select('origem', $opcoes['orig'], array('class'=>'form-control', 'empty'=>false));?></div>
                    <label for="ProdutoPesoKg" class="control-label col-md-2">Peso 1 Unidade (kg,g)</label>
                    <div class="col-md-2"><?echo $this->Form->text('peso_kg', array('class'=>'form-control mask_weight','type'=>'text'));?></div>
                    <label for="ProdutoCurvaAbc" class="control-label col-md-2">Curva ABC</label>
                    <div class="col-md-2"><?echo $this->Form->select('curva_abc', array('C' => 'C', 'B' => 'B', 'A' => 'A'), array('empty' => 'Indefinido'));?></div>
                </div>
                <h3>Dados para Estatísticas de Armazenagem</h3>
                <div class="row">
                    <div class="col-md-3">
                        <?=$this->Form->input('embTipo', ['label' => 'Tipo da Embalagem', 'options' => [
                            'BR' => 'Barril',
                            'BS' => 'Bisnaga',
                            'CX' => 'Caixa',
                            'CS' => 'Cesto',
                            'LT' => 'Latas',
                            'PC' => 'Pacote',
                            'PC' => 'Saco',
                        ], 'empty' => ' - Defina o Tipo de Embalagem do Produto - ']);?>
                    </div>
                    <div class="col-md-3">
                        <?=$this->Form->input('embItens', ['label' => 'Capacidade da Embalagem em Itens', 'type' => 'text', 'class' => 'mask_int']);?>
                    </div>
                    <div class="col-md-3">
                        <?=$this->Form->input('embItensPalete', ['label' => 'Quantos Produtos Cabem num Palete?', 'type' => 'text', 'class' => 'mask_int']);?>
                    </div>
                    <div class="col-md-3">
                        <?=$this->Form->input('embPesoBruto', ['label' => 'Peso Bruto da Embalgem com os Produtos', 'type' => 'text', 'class' => 'mask_weight']);?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?=$this->Form->input('embDim1', ['label' => 'Embalagem Dimensão: Largura', 'type' => 'text', 'class' => 'mask_weight']);?>
                    </div>
                    <div class="col-md-4">
                        <?=$this->Form->input('embDim2', ['label' => 'Embalagem Dimensão: Altura', 'type' => 'text', 'class' => 'mask_weight']);?>
                    </div>
                    <div class="col-md-4">
                        <?=$this->Form->input('embDim3', ['label' => 'Embalagem Dimensão: Profundidade', 'type' => 'text', 'class' => 'mask_weight']);?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2"><?echo $this->Form->checkbox('ativo', array('value'=>'sim','class'=>'form-control pull-left'));?>
                    <label for="ProdutoAtivo" class="control-label col-md-1 pull-left">Ativo?</label></div>
                </div>
            </div>
            <div class="tab-pane" id="tab3">
                <table class="table table-striped">
                <?
                $x = 0;
                foreach($clientes as $cliente_id=>$cliente_nome){
                    echo "<tr>";
                    echo '<td width="40">'.$this->Form->checkbox("ProdutoCliente.{$x}.cliente_id", array('value'=>$cliente_id, 'checked'=>(@in_array($cliente_id, @$this->request->data['ClientesChecked']) ? true:false))).'</td>';
                    echo "<td><label for=\"ProdutoCliente{$x}ClienteId\">{$cliente_nome}</label></td>";
                    echo "</tr>";
                    $x++;
                }
                ?>
                </table>
            </div>
            <div class="tab-pane" id="tab4">
                <table class="table table-striped">
                    <tr>
                        <th>Fluxo Logístico</th>
                        <th><span title="Defina abaixo a quantidade mínima do material por Fluxo Logístico." class="bs-tooltip">Estoque Mínimo</span></th>
                        <th><span title="Envia email para o responsável do Fluxo diariamente." class="bs-tooltip">Aviso por Email</span></th>
                    </tr>
                    <tr>
                        <td>Sem Fluxo Logístico</td>
                        <td><?echo $this->Form->text('Produto.estoque_minimo', array('class'=>'mask_int'));?></td>
                        <td></td>
                    </tr>
                    <?foreach ($fluxos as $fluxo_id => $fluxo_nome):?>
                    <tr>
                        <td><?=$fluxo_nome?></td>
                        <td><?=$this->Form->text('ProdutoEstoqueMinimo.'.$fluxo_id.'.quantidade_minima', array('type'=>'text', 'class'=>'mask_int'));?></td>
                        <td><?=$this->Form->checkbox('ProdutoEstoqueMinimo.'.$fluxo_id.'.email');?></td>
                    </tr>
                    <?endforeach;?>
                </table>
            </div>
            <div class="tab-pane" id="tab5">
                <div class="form-group required">
                    <label for="ProdutoImpostoPis" class="control-label col-md-2">PIS</label>
                    <div class="col-md-2"><?=$this->Form->select('imposto_pis_id', $imposto_pis, array('class'=>'form-control'));?></div>
                    <label for="ProdutoImpostoCofins" class="control-label col-md-2">COFINS</label>
                    <div class="col-md-2"><?=$this->Form->select('imposto_cofins_id', $imposto_cofins, array('class'=>'form-control'));?></div>
                    <label for="ProdutoImpostoIpi" class="control-label col-md-2">IPI</label>
                    <div class="col-md-2"><?=$this->Form->select('imposto_ipi_id', $imposto_ipi, array('class'=>'form-control','empty'=>' - Não atribuir IPI - '));?></div>
                </div>
                <div class="form-group">
                    <label for="ProdutoImpostoIcms" class="control-label col-md-2">ICMS</label>
                    <div class="col-md-2"><?=$this->Form->select('imposto_icms_id', $imposto_icms, array('class'=>'form-control'));?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <?=$this->Form->submit('Salvar Produto', array('class'=>'btn btn-success'))?>
    </div>
</div>
<?php echo $this->Form->end(); ?>
<!--
ALTER TABLE `produto`  ADD `embTipo` VARCHAR(2) NULL DEFAULT NULL  AFTER `imposto_ipi_id`,  ADD `embItens` INT NULL DEFAULT '0'  AFTER `embTipo`,  ADD `embItensPalete` INT NULL DEFAULT '0'  AFTER `embItens`,  ADD `embPesoBruto` DECIMAL(15,3) NULL DEFAULT '0.000'  AFTER `embItensPalete`,  ADD `embDim1` DECIMAL(15,3) NULL DEFAULT '0.000'  AFTER `embPesoBruto`,  ADD `embDim2` DECIMAL(15,3) NULL DEFAULT '0.000'  AFTER `embDim1`,  ADD `embDim3` DECIMAL(15,3) NULL DEFAULT '0.000'  AFTER `embDim2`;
-->