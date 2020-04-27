<script>
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
$opcoes['cst'][] = array(
    ''=>'',
    '00' => '00 - Tributada integralmente;',
    '10' => '10 - Tributada e com cobrança do ICMS por substituição tributária;',
    '20' => '20 - Com redução de base de cálculo;',
    '30' => '30 - Isenta ou não tributada e com cobrança do ICMS por substituição tributária;',
    '40' => '40 - Isenta;',
    '41' => '41 - Não tributada;',
    '50' => '50 - Suspensão;',
    '51' => '51 - Diferimento;',
    '60' => '60 - ICMS cobrado anteriormente por substituição tributária;',
    '70' => '70 - Com redução de base de cálculo e cobrança do ICMS por substituição tributária;',
    '90' => '90 - Outros;',
    'P10' => 'P10 - ICMSPart com CST=10;',
    'P90' => 'P90 - ICMSPart com CST=90;',
    'S41' => 'S41 - ICMSST;',
);
$opcoes['cst'][] = array(
    ''=>'',
    '101' => '101 - Tributada pelo Simples Nacional com permissão de crédito;',
    '102' => '102 - Tributada pelo Simples Nacional sem permissão de crédito;',
    '103' => '103 - Isenção do ICMS no Simples Nacional para faixa de receita bruta;',
    '201' => '201 - Tributada pelo Simples Nacional com permissão de crédito e com cobrança do ICMS por substituição tributária;',
    '202' => '202 - Tributada pelo Simples Nacional sem permissão de crédito e com cobrança do ICMS por substituição tributária;',
    '203' => '203 - Isenção do ICMS no Simples Nacional para faixa de receita bruta e com cobrança do ICMS por substituição tributária;',
    '300' => '300 - Imune;',
    '400' => '400 - Não tributada pelo Simples Nacional;',
    '500' => '500 - ICMS cobrado anteriormente por substituição tributária (substituído) ou por antecipação;',
    '900' => '900 - Outros.',
);
$opcoes['modBC'] = array(
    ''=>'',
    '0'=>'0 - Margem Valor Agregado (%);',
    '1'=>'1 - Pauta (valor);',
    '2'=>'2 - Preço Tabelado Máximo (valor);',
    '3'=>'3 - Valor da Operação.',
);
$opcoes['modBCST'] = array(
    ''=>'',
    '0'=>'0 - Preço tabelado ou máximo sugerido;',
    '1'=>'1 - Lista Negativa (valor);',
    '2'=>'2 - Lista Positiva (valor);',
    '3'=>'3 - Lista Neutra (valor);',
    '4'=>'4 - Margem Valor Agregado (%);',
    '5'=>'5 - Pauta (valor).',
);
$opcoes['motDesICMS'] = array(
    '0'=>'',
    '1'=>'1 - Táxi;',
    '2'=>'2 - Deficiente Físico;',
    '3'=>'3 - Produtor Agropecuário;',
    '4'=>'4 - Frotista/Locadora;',
    '5'=>'5 - Diplomático/Consular;',
    '6'=>'6 - Utilitários e Motocicletas da Amazônia Ocidental e Áreas de Livre Comércio (Resolução 714/88 e 790/94 - CONTRAN e suas alterações);',
    '7'=>'7 - SUFRAMA;',
    '9'=>'9 - outros.',
);
echo "var _opcoes_cst_normal = ".json_encode(to_utf8(arrayToJSONSelectOptions($opcoes['cst'][0])));
echo ";\nvar _opcoes_cst_simples = ".json_encode(to_utf8(arrayToJSONSelectOptions($opcoes['cst'][1])));
?>;
    
    function hideICMSAll(){
        $("#ProdutoIcmsVBC").parent().css('display', 'none');
        $("#ProdutoIcmsModBC").parent().css('display', 'none');
        $("#ProdutoIcmsPRedBC").parent().css('display', 'none');
        $("#ProdutoIcmsPICMS").parent().css('display', 'none');
        $("#ProdutoIcmsVICMS").parent().css('display', 'none');
        $("#ProdutoIcmsModBCST").parent().css('display', 'none');
        $("#ProdutoIcmsPMVAST").parent().css('display', 'none');
        $("#ProdutoIcmsPRedBCST").parent().css('display', 'none');
        $("#ProdutoIcmsVBCST").parent().css('display', 'none');
        $("#ProdutoIcmsPICMSST").parent().css('display', 'none');
        $("#ProdutoIcmsVICMSST").parent().css('display', 'none');
        $("#ProdutoIcmsVBCSTRet").parent().css('display', 'none');
        $("#ProdutoIcmsVICMSSTRet").parent().css('display', 'none');
        $("#ProdutoIcmsVBCSTDest").parent().css('display', 'none');
        $("#ProdutoIcmsVICMSSTDest").parent().css('display', 'none');
        $("#ProdutoIcmsVDesICMS").parent().css('display', 'none');
        $("#ProdutoIcmsMotDesICMS").parent().css('display', 'none');
        $("#ProdutoIcmsPBCOp").parent().css('display', 'none');
        $("#ProdutoIcmsUFST").parent().css('display', 'none');
        $("#ProdutoIcmsPCredSN").parent().css('display', 'none');
        $("#ProdutoIcmsVCredICMSSN").parent().css('display', 'none');
        $("#hicms").css('display', 'none');
        $("#hicmsst").css('display', 'none');
    }
    
    function changeCRT(_CRT){
        hideICMSAll();
        if (_CRT.options[_CRT.selectedIndex].value=='0'){
            $('#icms_data').css('display', 'block');
            $("#ProdutoIcmsCST option").remove();
            for (i = 0; i < _opcoes_cst_normal.length; i++){
                $("#ProdutoIcmsCST").append("<option value='"+_opcoes_cst_normal[i].value+"'>"+_opcoes_cst_normal[i].text+"</option>");
            }
        } else if (_CRT.options[_CRT.selectedIndex].value=='1'){
            $('#icms_data').css('display', 'block');
            $("#ProdutoIcmsCST option").remove();
            for (i = 0; i < _opcoes_cst_simples.length; i++){
                $("#ProdutoIcmsCST").append("<option value='"+_opcoes_cst_simples[i].value+"'>"+_opcoes_cst_simples[i].text+"</option>");
            }
        } else {
            $('#icms_data').css('display', 'none');
        }
    }
    
    function changeCST(){
        hideICMSAll();
        var _cst = $('#ProdutoIcmsCST option:selected').val();
        
        // SIMPLES NACIONAL
        if (_cst=='101'){
            $("#ProdutoIcmsPCredSN").parent().css('display', 'block');
        }
        if (_cst=='201'){
            $("#hicmsst").css('display', 'block');
            $("#ProdutoIcmsPCredSN").parent().css('display', 'block');
            $("#ProdutoIcmsModBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPRedBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPMVAST").parent().css('display', 'block');
            $("#ProdutoIcmsPICMSST").parent().css('display', 'block');
        }
        if (_cst=='202'){
            $("#hicmsst").css('display', 'block');
            $("#ProdutoIcmsModBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPRedBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPMVAST").parent().css('display', 'block');
            $("#ProdutoIcmsPICMSST").parent().css('display', 'block');
        }
        if (_cst=='203'){
            $("#hicmsst").css('display', 'block');
            $("#ProdutoIcmsModBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPRedBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPMVAST").parent().css('display', 'block');
            $("#ProdutoIcmsPICMSST").parent().css('display', 'block');
        }
        if (_cst=='900'){
            $("#hicms").css('display', 'block');
            $("#ProdutoIcmsModBC").parent().css('display', 'block');
            $("#ProdutoIcmsPRedBC").parent().css('display', 'block');
            $("#ProdutoIcmsPICMS").parent().css('display', 'block');
            
            $("#hicmsst").css('display', 'block');
            $("#ProdutoIcmsModBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPRedBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPMVAST").parent().css('display', 'block');
            $("#ProdutoIcmsPICMSST").parent().css('display', 'block');
        }
        
        // TRIBUTAÇÃO NORMAL
        if (_cst=='00'){
            $("#hicms").css('display', 'block');
            $("#ProdutoIcmsModBC").parent().css('display', 'block');
            $("#ProdutoIcmsPICMS").parent().css('display', 'block');
        }
        if (_cst=='10'){
            $("#hicms").css('display', 'block');
            $("#ProdutoIcmsModBC").parent().css('display', 'block');
            $("#ProdutoIcmsPICMS").parent().css('display', 'block');
            
            $("#hicmsst").css('display', 'block');
            $("#ProdutoIcmsModBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPRedBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPMVAST").parent().css('display', 'block');
            $("#ProdutoIcmsPICMSST").parent().css('display', 'block');
        }
        if (_cst=='P10'){
            $("#hicms").css('display', 'block');
            $("#ProdutoIcmsModBC").parent().css('display', 'block');
            $("#ProdutoIcmsPRedBC").parent().css('display', 'block');
            $("#ProdutoIcmsPICMS").parent().css('display', 'block');
            $("#ProdutoIcmsVICMSSTRet").parent().css('display', 'block');
            
            $("#hicmsst").css('display', 'block');
            $("#ProdutoIcmsModBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPRedBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPMVAST").parent().css('display', 'block');
            $("#ProdutoIcmsPICMSST").parent().css('display', 'block');
            $("#ProdutoIcmsUFST").parent().css('display', 'block');
        }
        if (_cst=='20'){
            $("#hicms").css('display', 'block');
            $("#ProdutoIcmsModBC").parent().css('display', 'block');
            $("#ProdutoIcmsPRedBC").parent().css('display', 'block');
            $("#ProdutoIcmsPICMS").parent().css('display', 'block');
        }
        if (_cst=='30'){
            $("#hicmsst").css('display', 'block');
            $("#ProdutoIcmsModBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPRedBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPMVAST").parent().css('display', 'block');
            $("#ProdutoIcmsPICMSST").parent().css('display', 'block');
        }
        if (_cst=='40'){
            $("#hicms").css('display', 'block');
            $("#ProdutoIcmsVDesICMS").parent().css('display', 'block');
            $("#ProdutoIcmsMotDesICMS").parent().css('display', 'block');
        }
        if (_cst=='41'){
            $("#hicms").css('display', 'block');
            $("#ProdutoIcmsVDesICMS").parent().css('display', 'block');
            $("#ProdutoIcmsMotDesICMS").parent().css('display', 'block');
        }
        if (_cst=='50'){
            $("#hicms").css('display', 'block');
            $("#ProdutoIcmsVDesICMS").parent().css('display', 'block');
            $("#ProdutoIcmsMotDesICMS").parent().css('display', 'block');
        }
        if (_cst=='51'){
            $("#hicms").css('display', 'block');
            $("#ProdutoIcmsModBC").parent().css('display', 'block');
            $("#ProdutoIcmsPRedBC").parent().css('display', 'block');
            $("#ProdutoIcmsPICMS").parent().css('display', 'block');
        }
        if (_cst=='70'){
            $("#hicms").css('display', 'block');
            $("#ProdutoIcmsModBC").parent().css('display', 'block');
            $("#ProdutoIcmsPRedBC").parent().css('display', 'block');
            $("#ProdutoIcmsPICMS").parent().css('display', 'block');
            
            $("#hicmsst").css('display', 'block');
            $("#ProdutoIcmsModBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPRedBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPMVAST").parent().css('display', 'block');
            $("#ProdutoIcmsPICMSST").parent().css('display', 'block');
        }
        if (_cst=='P90'){
            $("#hicms").css('display', 'block');
            $("#ProdutoIcmsModBC").parent().css('display', 'block');
            $("#ProdutoIcmsPRedBC").parent().css('display', 'block');
            $("#ProdutoIcmsPICMS").parent().css('display', 'block');
            $("#ProdutoIcmsVICMSSTRet").parent().css('display', 'block');
            
            $("#hicmsst").css('display', 'block');
            $("#ProdutoIcmsModBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPRedBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPMVAST").parent().css('display', 'block');
            $("#ProdutoIcmsPICMSST").parent().css('display', 'block');
            $("#ProdutoIcmsUFST").parent().css('display', 'block');
        }
        if (_cst=='90'){
            $("#hicms").css('display', 'block');
            $("#ProdutoIcmsModBC").parent().css('display', 'block');
            $("#ProdutoIcmsPRedBC").parent().css('display', 'block');
            $("#ProdutoIcmsPICMS").parent().css('display', 'block');
            
            $("#hicmsst").css('display', 'block');
            $("#ProdutoIcmsModBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPRedBCST").parent().css('display', 'block');
            $("#ProdutoIcmsPMVAST").parent().css('display', 'block');
            $("#ProdutoIcmsPICMSST").parent().css('display', 'block');
        }
    }
    
</script>
<?php echo $this->Form->create('Produto', array('type' => 'file')); ?>
    <fieldset>
        <legend><?php echo __('Formulário de Produto'); ?> <a href="#back" onclick="window.history.back();" title="Voltar"><img src="/img/btn-voltar.png"></a></legend>
        <?php echo $this->Session->flash(); ?>
<ul class="nav nav-tabs">
    <li class="active"><a href="#tab1" data-toggle="tab"<?=(isset($problem['produto']) ? ' style="color: red;"':'')?>>Geral</a></li>
    <li><a href="#tab4" data-toggle="tab"<?=(isset($problem['embalagem']) ? ' style="color: red;"':'')?>>Embalagem</a></li>
    <li><a href="#tab5" data-toggle="tab">Estoque Mínimo por Fluxo</a></li>
    <li><a href="#tab2" data-toggle="tab"<?=(isset($problem['icms']) ? ' style="color: red;"':'')?>>ICMS</a></li>
    <!--li><a href="#tab3" data-toggle="tab">IPI</a></li-->
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="tab1">
        <h4>Clientes</h4>
        <div class="produto-clientes">
            <?
            $x = 0;
            foreach($clientes as $cliente_id=>$cliente_nome){
                echo $this->Form->checkbox("ProdutoCliente.{$x}.cliente_id", array('value'=>$cliente_id, 'checked'=>(@in_array($cliente_id, @$this->request->data['ClientesChecked']) ? true:false)));
                echo "<label for=\"ProdutoCliente{$x}ClienteId\">{$cliente_nome}</label>";
                $x++;
            }
            ?>
        </div>
    <?php
        echo $this->Form->input('tracking', array('options'=>array('sim'=>'Sim', 'nao'=>'Não')));
        echo $this->Form->input('codigo', array('type'=>'text', 'label'=>'Código'));
        echo $this->Form->input('codigo_cliente', array('type'=>'text', 'label'=>'Código de Produto do Cliente'));
        echo $this->Form->input('codigo_barras', array('type'=>'text', 'label'=>'Código de Barras (EAN)','class'=>'mask_int', 'id'=>'barcode'));
        echo $this->Form->input('nome');
        echo $this->Form->input('nome_abrev', array('label'=>'Nome Abreviado (até 20 posições)', 'after'=>'usado em etiquetas'));
        echo $this->Form->input('descricao', array('label'=>'Descrição'));
        echo $this->Form->input('unidade_comercial', array('label'=>'Unidade', 'options'=>array('un'=>'Unidade','pc'=>'Pacote','cx'=>'Caixa','pl'=>'Pallet','kg'=>'Kilo','tn'=>'Tonelada','lt'=>'Litro','m2'=>'Metro Quadrado','m3'=>'Metro Cúbico')));
        echo $this->Form->input('nf_NCM', array('label'=>'NCM','class'=>'mask_int','type'=>'text'));
        echo $this->Form->input('nf_EXTIPI', array('type'=>'text', 'label'=>'EX TIPI'));
        echo $this->Form->input('CFOP_interno', array('label'=>'CFOP','class'=>'mask_int','type'=>'text'));
        echo $this->Form->input('CFOP_externo', array('label'=>'CFOP fora do Estado','class'=>'mask_int','type'=>'text'));
        echo $this->Form->input('peso_kg', array('class'=>'mask_weight','type'=>'text'));
        echo $this->Form->input('peso_maximo_suportado', array('label'=>'Peso Máximo suportado em KG','class'=>'mask_int','type'=>'text'));
        if (isset($foto) && !empty($foto)){?><img src="/files/produtos/p/<?=$foto?>" /><br/><?}
        echo $this->Form->input('foto', array('type'=>'file'));
        echo $this->Form->input('ativo', array('options'=>array('sim'=>'Sim', 'nao'=>'Não'), 'empty'=>false));
    ?>
    </div>
    <div class="tab-pane" id="tab2">
        <?
        echo $this->Form->input('ProdutoIcms.CRT', array('label'=>'Código de Regime Tributário - CRT','onchange'=>'changeCRT(this);','options'=>array(''=>' - escolha a modalidade - ','0'=>'Tributação Normal', '1'=>'Simples Nacional')));
        echo "<div id=\"icms_data\">\n";
        echo $this->Form->input('ProdutoIcms.CST', array('onchange'=>'changeCST();','label'=>'Tributação do ICMS - CST', 'options'=>array()));
        echo $this->Form->input('ProdutoIcms.orig', array('onchange'=>'changeOrig();','label'=>'Origem da mercadoria', 'options'=>$opcoes['orig']));
        echo $this->Form->input('ProdutoIcms.pCredSN', array('label'=>'Alíquota aplicável de cálculo do crédito', 'class'=>'mask_percent','type'=>'text'));
        echo "<h3 id=\"hicms\">ICMS</h3>";
        echo $this->Form->input('ProdutoIcms.modBC', array('label'=>'Modalidade de determinação da BC do ICMS', 'options'=>$opcoes['modBC']));
        echo $this->Form->input('ProdutoIcms.pRedBC', array('label'=>'Percentual da Redução de BC', 'class'=>'mask_percent','type'=>'text'));
        echo $this->Form->input('ProdutoIcms.vBC', array('label'=>'Valor da BC do ICMS', 'class'=>'mask_money','type'=>'text'));
        echo $this->Form->input('ProdutoIcms.pICMS', array('label'=>'Alíquota do ICMS', 'class'=>'mask_percent','type'=>'text'));
        echo $this->Form->input('ProdutoIcms.vICMS', array('label'=>'Valor do ICMS', 'class'=>'mask_money','type'=>'text'));
        echo $this->Form->input('ProdutoIcms.vICMSSTRet', array('label'=>'Percentual da BC operação própria', 'class'=>'mask_money','type'=>'text'));
        echo "<h3 id=\"hicmsst\">ICMS ST</h3>";
        echo $this->Form->input('ProdutoIcms.modBCST', array('label'=>'Modalidade de determinação da BC do ICMS ST','options'=>$opcoes['modBCST']));
        echo $this->Form->input('ProdutoIcms.pMVAST', array('label'=>'Percentual da margem de valor Adicionado do ICMS ST', 'class'=>'mask_percent','type'=>'text'));
        echo $this->Form->input('ProdutoIcms.pRedBCST', array('label'=>'Percentual da Redução de BC do ICMS ST', 'class'=>'mask_percent','type'=>'text'));
        echo $this->Form->input('ProdutoIcms.vBCST', array('label'=>'Valor da BC do ICMS ST', 'class'=>'mask_money','type'=>'text'));
        echo $this->Form->input('ProdutoIcms.pICMSST', array('label'=>'Alíquota do ICMS ST', 'class'=>'mask_percent','type'=>'text'));
        echo $this->Form->input('ProdutoIcms.vICMSST', array('label'=>'Valor do ICMS ST', 'class'=>'mask_money','type'=>'text'));
        echo $this->Form->input('ProdutoIcms.UFST', array('label'=>'UF para qual é devido o ICMS ST','type'=>'text'));
        echo $this->Form->input('ProdutoIcms.vBCSTRet', array('label'=>'Valor da BC do ICMS Retido Anteriormente', 'class'=>'mask_money','type'=>'text'));
        echo $this->Form->input('ProdutoIcms.vICMSSTDest', array('label'=>'Valor do ICMS Retido Anteriormente', 'class'=>'mask_money','type'=>'text'));
        echo $this->Form->input('ProdutoIcms.motDesICMS', array('label'=>'Motivo da desoneração do ICMS', 'options'=>$opcoes['motDesICMS']));
        echo $this->Form->input('ProdutoIcms.pBCOp', array('label'=>'Valor da BC do ICMS ST da UF destino', 'class'=>'mask_percent','type'=>'text'));
        //echo $this->Form->input('ProdutoIcms.', array('label'=>'Valor do ICMS ST da UF destino'));
        echo $this->Form->input('ProdutoIcms.vCredICMSSN', array('label'=>'Valor crédito do ICMS que pode ser aproveitado nos termos do art. 23 da LC 123 (SIMPLES NACIONAL)', 'class'=>'mask_money','type'=>'text'));
        echo "</div>\n";
        ?>
        <?
        echo $this->Form->submit('Salvar', array('class'=>'btn btn-primary'));
        ?>
    </div>
    <!--div class="tab-pane" id="tab3">
        <?
        echo $this->Form->input('ProdutoIpi.clEnq', array('label'=>'Classe de enquadramento (cigarros e bebidas)','type'=>'text'));
        echo $this->Form->input('ProdutoIpi.CNPJProd', array('label'=>'Código de enquadramento legal','type'=>'text'));
        echo $this->Form->input('ProdutoIpi.cEnq', array('label'=>'CNPJ do Produtor','class'=>'mask_cnpj','type'=>'text'));
        ?>
    </div-->
    <div class="tab-pane" id="tab4">
        <?
        echo $this->Form->input('Embalagem.nome', array('label'=>'Tipo de Embalagem (ex: Caixa, Pacote, Pallet, Prisma, etc...)'));
        echo $this->Form->input('Embalagem.quantidade', array('label'=>'Quantidade de Produtos Nesta Embalagem', 'class'=>'mask_int','type'=>'text'));
        echo $this->Form->input('Embalagem.largura_em_cm', array('class'=>'mask_cm', 'label'=>'Largura da Embalagem (cm)','type'=>'text'));
        echo $this->Form->input('Embalagem.altura_em_cm', array('class'=>'mask_cm', 'label'=>'Altura da Embalagem (cm)','type'=>'text'));
        echo $this->Form->input('Embalagem.profundidade_em_cm', array('class'=>'mask_cm', 'label'=>'Profundidade da Embalagem (cm)','type'=>'text'));
        ?>
    </div>
    <div class="tab-pane" id="tab5">
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
</div>
        <script>
            window.onload = function(){
                changeCRT(document.getElementById('ProdutoIcmsCRT'));
<?if (isset($this->request->data['Produto']['id'])){?>
                $("#ProdutoIcmsCST option[value='<?=@$this->request->data['ProdutoIcms']['CST']?>']").attr('selected', 'selected');
                changeCST();
<?}?>
            }
        </script>
    </fieldset>
<?php echo $this->Form->end(); ?>
