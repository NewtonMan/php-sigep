<style>
  .ui-autocomplete {
    max-height: 200px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
  }
  /* IE 6 doesn't support max-height
   * we use height instead, but this forces the menu to always be this tall
   */
  * html .ui-autocomplete {
    height: 200px;
  }
</style>
<script>
    function ModalDestino(_title, _id){
        abrePopUp('/cadastros/gerenciar/edit/'+_id+'?mode=modal');
    }
    function BuscaDestino(_prefix){
        $('#'+_prefix+'Info').html('<h5><i class="fa fa-spinner fa-spin"></i></h5>');
        $.ajax(
            {
                type: 'POST',
                url: '/cadastros/gerenciar/add?reply=js',
                cache: false,
                dataType: 'json',
                data: {'data[Destino][cpf_cnpj]':$('#'+_prefix+'CpfCnpj').val()},
                success: function(_d){
                    var _did = $('#DestinoId').val();
                    if (_d.data==false){
                        $('#'+_prefix+'Info').html('<p class="text-danger">CPF/CNPJ Inválido', 'Você deve preencher o campo com um número de CPF ou CNPJ válido.</p>');
                    } else if (_d.data.Destino.fantasia == '') {
                        $('#'+_prefix+'Id').val(_d.data.Destino.id);
                        $('#'+_prefix+'IcmsNaoCobrar').val(_d.data.Destino.icms_nao_cobrar);
                        $('#'+_prefix+'Info').html('<a href="#" class="btn btn-primary" onclick="ModalDestino(\'Finalizar o Cadastro\',\'' + _d.data.Destino.id + '\');">Finalizar o Cadastro</a>');
                        ModalDestino('Finalize o Cadastro', _d.data.Destino.id);
                    } else {
                        $('#'+_prefix+'Id').val(_d.data.Destino.id);
                        $('#'+_prefix+'IcmsNaoCobrar').val(_d.data.Destino.icms_nao_cobrar);
                        $('#'+_prefix+'Info').html('<a href="#" class="btn btn-primary" onclick="ModalDestino(\'Atualizar o Cadastro\',\'' + _d.data.Destino.id + '\');"><input type="hidden" name="data['+_prefix+'][fantasia]" value="' + _d.data.Destino.fantasia + '" /><input type="hidden" name="data['+_prefix+'][municipio]" value="' + _d.data.Destino.municipio + '" /><input type="hidden" name="data['+_prefix+'][uf]" value="' + _d.data.Destino.uf + '" />' + _d.data.Destino.fantasia + " - " + _d.data.Destino.municipio + "/" + _d.data.Destino.uf + '</a>');
                    }
                }
            }
        );
    }
</script>
<?=$this->Session->flash();?>
<?=$this->Form->create($this->request->data['CRUD']['model'], ['type' => 'file']);?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title"><?=$this->request->data['CRUD']['titulo']?></h1>
    </div>
    <div class="panel-body">
        <?php
        foreach ($this->request->data['CRUD']['form'] as $cname => $cdata){
            $model = (isset($cdata['model']) ? $cdata['model']:$this->request->data['CRUD']['model']);
            $field = (isset($cdata['field']) ? $cdata['field']:$cname);
            $options = (isset($cdata['options']) ? $cdata['options']:[]);
            if (@$cdata['componente']=='destino') {
                ?>
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h1 class="panel-title"><?=$options['label']?></h1>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-group<?=(isset($this->validationErrors[$model]['fantasia']) ? ' has-error':'')?>">
                                                <?=$this->Form->hidden($model.'.id');?>
                                                <?=$this->Form->hidden($model.'.icms_nao_cobrar');?>
                                                <?=$this->Form->text($model.'.cpf_cnpj', array('placeholder' => 'Pesquisar pelo Nome ou Documento', 'class' => 'form-control'.(isset($this->validationErrors[$model]['fantasia']) ? ' error':'')));?>
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" id="<?=$model?>Search" type="button"><i class="fa fa-search"></i></button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12" id="<?=$model?>Info">
                                            <?php
                                            if (!empty($this->request->data[$model]['id'])){
                                                echo '<a onclick="ModalDestino(\'Atualizar o Cadastro\',\''.$this->request->data[$model]['id'].'\');" class="btn btn-primary" href="#">' . "<input type=\"hidden\" name=\"data[{$model}][fantasia]\" value=\"{$this->request->data[$model]['fantasia']}\" /><input type=\"hidden\" name=\"data[{$model}][municipio]\" value=\"{$this->request->data[$model]['municipio']}\" /><input type=\"hidden\" name=\"data[{$model}][uf]\" value=\"{$this->request->data[$model]['uf']}\" /> {$this->request->data[$model]['fantasia']} - {$this->request->data[$model]['municipio']} / {$this->request->data[$model]['uf']}" . '</a>';
                                            } else {
                                                echo "<h5>Pesquise para prosseguir.</h5>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                <?php
                $this->start('script-onload');
                ?>
$("#<?=$model?>CpfCnpj").autocomplete({
    source: "/cadastros/gerenciar/source.json",
    minLength: 2,
    focus: function( event, ui ) {
        return false;
    },
    select: function( event, ui ) {
        $("#<?=$model?>CpfCnpj").val(ui.item.Destino.cpf_cnpj);
        $("#<?=$model?>Id").val(ui.item.Destino.id);
        $("#<?=$model?>Search").click();
        return false;
    }
}).autocomplete( "instance" )._renderItem = function( ul, item ) {
    return $( "<li>" )
        .append( "<div><b>" + item.Destino.fantasia + "</b> - CFP/CNPJ: " + item.Destino.cpf_cnpj + "<br>" + item.Destino.endereco + (item.Destino.numero=='' ? '':", "+item.Destino.numero) + (item.Destino.complemento=='' ? '':" - "+item.Destino.complemento) + " - " + item.Destino.municipio + " / " + item.Destino.uf + " CEP " + item.Destino.cep + "</div>" )
        .appendTo( ul );
};
$('#<?=$model?>Search').click(function(){ BuscaDestino('<?=$model?>') });
                <?php
                $this->end();
            } else {
                echo $this->Form->input("{$model}.{$field}", $options);
            }
        }
        ?>
    </div>
    <div class="panel-footer text-center">
        <?=$this->Form->submit('Salvar Registro', ['class' => 'btn btn-success']);?>
    </div>
</div>
<?=$this->Form->end();?>