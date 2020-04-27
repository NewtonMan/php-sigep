<script type="text/javascript">
function drawColorPalette(stageID, callback) {
    var listColor = ["00", "33", "66", "99", "CC", "FF"];
    var table = document.createElement("table");
    table.border = 1;
    table.cellPadding = 0;
    table.cellSpacing = 0;
    table.style.borderColor = "#666666";
    table.style.borderCollapse = "collapse";
    var tr, td;
    var color = "";
    var tbody = document.createElement("tbody");
    for (var i = 0; i < listColor.length; i++){
        tr = document.createElement("tr");
        for (var x = 0; x < listColor.length; x++) {
            for (var y = 0; y < listColor.length; y++) {
                color = "#"+listColor[i]+listColor[x]+listColor[y];
                td = document.createElement("td");
                td.style.width = "11px";
                td.style.height = "11px";
                td.style.background = color;
                td.color = color;
                td.style.borderColor = "#000";
                td.style.cursor = "pointer";
               
                if (typeof(callback) == "function") {
                    td.onclick = function() {
                        callback.apply(this, [this.color]);
                    }
                }
                tr.appendChild(td);
            }
        }
        tbody.appendChild(tr);
    } 
    table.appendChild(tbody);
    var element = document.getElementById(stageID);
    if (element) element.appendChild(table);
    return table;
}
 
window.onload = function() {
    drawColorPalette("mydiv", function(color) {
        document.getElementById("EmpresaStyleCor1").value = color;
    });
}
</script>
<?php echo $this->Form->create('EmpresaStyle', array('type' => 'file')); ?>
    <fieldset>
        <legend><?php echo __('Configurações do Sistema'); ?> <a href="#back" onclick="window.history.back();" title="Voltar"><img src="/img/btn-voltar.png"></a></legend>
        <?php echo $this->Session->flash(); ?>
    <?php
        echo $this->Form->input('logo_file', array('type'=>'file', 'label'=>'Imagem do Topo', 'style'=>'width: auto; height: auto'));
        echo $this->Form->input('cor1', array('type'=>'text', 'label'=>'Cor Padrão'));
        ?><div id="mydiv"></div><?
        echo $this->Form->input('font-family', array('label'=>'Tipografia para o Sistema', 'options'=>array('Arial'=>'Arial', 'Verdana'=>'Verdana', 'Times New Roman'=>'Times New Roman')));
    ?>
        <h2>Personalização de URL</h2>
        <p>Para você utilizar esse sistema como por ex. sistema.site_da_empresa.com.br você deve solicitar ao responsável pela hospedagem do "site_da_empresa.com.br", que siga os passos a seguir:</p>
        <ol>
            <li>Criar um registro de tipo CNAME no domínio do site, com o nome que desejar, apontando para o nome canônico "estoque.mkt-trade.com.br".</li>
            <li>Veja um exemplo de registro de zona BIND: "sistema    IN    CNAME    estoque.mkt-trade.com.br."</li>
            <li>Você pode possuir quantos URLs você desejar, podendo inclusive personalizar com nomes de clientes.</li>
            <li>Após 24 horas da alteração de seu DNS, informe no campo abaixo, sendo um endereço por linha, que deseja apontar.</li>
        </ol>
    <?php
        echo $this->Form->input('urls', array('type'=>'textarea', 'label'=>'Endereços/URLs'));
        echo $this->Form->submit('Salvar', array('class'=>'btn btn-primary'));
    ?>
    </fieldset>
<?php echo $this->Form->end(); ?>