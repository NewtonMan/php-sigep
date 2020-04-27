<script src="/js/cep.js" type="text/javascript"></script>
<script>
    var _cidades = <?=json_encode(clearAll($cidades))?>;
    function removerAcentos( newStringComAcento ) {
        newStringComAcento = newStringComAcento ? newStringComAcento:new String;
        var string = newStringComAcento.trim();
        string = string.replace(/\?/g, '');
        string = string.replace(/\./g, '');
        string = string.replace(/,/g, '');
        string = string.replace(/"/g, '');
        string = string.replace(/'/g, '');
        string = string.replace(/:/g, '');
        string = string.replace(/;/g, '');
        var mapaAcentosHex = {
            a: /[\xE0-\xE6]/g,
            A: /[\xC0-\xC6]/g,
            e: /[\xE8-\xEB]/g,
            E: /[\xC8-\xCB]/g,
            i: /[\xEC-\xEF]/g,
            I: /[\xCC-\xCF]/g,
            o: /[\xF2-\xF6]/g,
            O: /[\xD2-\xD6]/g,
            u: /[\xF9-\xFC]/g,
            U: /[\xD9-\xDC]/g,
            c: /\xE7/g,
            C: /\xC7/g,
            n: /\xF1/g,
            N: /\xD1/g
        };
        for ( var letra in mapaAcentosHex ) {
            var expressaoRegular = mapaAcentosHex[letra];
            string = string.replace( expressaoRegular, letra );
        }
        return string;
    }
    
    function MostraCidades(){
        var _uf = $('#DestinoUf option:selected').val();
        $('#DestinoIbgeCidadeId').html('');
        for (var _x = 0; _x < _cidades.length; _x++){
            if (_uf==_cidades[_x].uf){
                for (var _y = 0; _y < _cidades[_x]['cidades'].length; _y++){
                    var _op = $(document.createElement('option'));
                    _op.attr({value: _cidades[_x]['cidades'][_y]['id']});
                    _op.html(_cidades[_x]['cidades'][_y]['nome']);
                    $('#DestinoIbgeCidadeId').append(_op);
                    $('#DestinoIbgeCidadeId').val($('#DestinoIbgeCidadeId').data('sel'));
                }
            }
        }
    }
    
    function UpdateMunicipio(){
        var _s_mun = $('#DestinoIbgeCidadeId option:selected').val();
        var _i_mun = $('#DestinoMunicipio').val();
        console.log('_s_mun = ' + _s_mun);
        console.log('_i_mun = ' + _i_mun);
        if (_s_mun==undefined && _i_mun!=''){
            $('#DestinoIbgeCidadeId option').each(function(){
                var _a = removerAcentos($(this).text().toUpperCase());
                var _b = removerAcentos(_i_mun.toUpperCase());
                if (_a==_b) $(this).attr('selected', true);
            });
        }
        $('#DestinoMunicipio').val(removerAcentos($('#DestinoIbgeCidadeId option:selected').text()));
    }
    
    function PreencheComCEPRequest(){
        $('#cep-status').html('<img src="/img/GIF-correios.gif" />');
        _correiosCep.query($('#DestinoCep').val(), function(obj){ PreencheComCEPResponseOk(obj) }, function(){ PreencheComCEPResponseFail() });
    }
    
    function PreencheComCEPResponseOk(obj){
        if (obj.erro){
            $('#cep-status').html('<p class="text-danger">CEP não existe.</p>');
        } else {
            $('#DestinoEndereco').val(obj.logradouro);
            $('#DestinoBairro').val(obj.bairro);
            $('#DestinoMunicipio').val(removerAcentos(obj.localidade));
            $('#DestinoUf option').each(function(){
                if ($(this).text()==obj.uf){
                    $(this).attr({selected: true});
                    MostraCidades();
                }
            });
            $('#DestinoIbgeCidadeId').val(obj.ibge);
            $('#cep-status').html('<p class="text-success">Localizados os dados do CEP.</p>');
            $('#DestinoNumero').focus();
        }
    }
    
    function PreencheComCEPResponseFail(){
        $('#cep-status').html('<p class="text-danger">Não foi possível localizar dados para o CEP.</p>');
    }
    var _cep_testado = '';
    var _cep_data = null;
    var _vr1 = {class: 'warning', text: 'CPF/CNPJ inválido.'};
    var _vr2 = {class: 'warning', text: 'IE inválida.'};
    var _vr3 = {class: 'warning', text: 'CPF/CNPJ inválido.'};
    var _vr4 = {class: 'warning', text: 'CPF/CNPJ inválido.'};
    var _vr5 = {class: 'warning', text: 'CPF/CNPJ inválido.'};
    var _vr6 = {class: 'warning', text: 'CPF/CNPJ inválido.'};
    var _vr7 = {class: 'warning', text: 'CPF/CNPJ inválido.'};
    var _vr8 = {class: 'warning', text: 'CPF/CNPJ inválido.'};
    function validaRegras(){
        //REGRA 1 - VERIFICA CPF/CNPJ
        var _cpf_cnpj = '<?=(isCNPJ(addLeading($this->request->data['Destino']['cpf_cnpj'], 14)) ? addLeading($this->request->data['Destino']['cpf_cnpj'], 14):addLeading($this->request->data['Destino']['cpf_cnpj'], 11))?>';
        if (valida_cpf_cnpj(_cpf_cnpj)){
            _vr1.class = 'success';
            _vr1.text = 'CPF/CNPJ válido.';
        } else {
            _vr1.class = 'danger';
            _vr1.text = 'CPF/CNPJ inválido.';
        }
        
        //REGRA 2 - Verificar IE
        var _ie = $('#DestinoRgInscEstadual').val();
        var _uf = $('#DestinoUf option:selected').val();
        if (_uf=='' || _ie==''){
            _vr2.class = 'warning';
            _vr2.text = 'Digite a IE e defina uma UF';
        } else {
            if (inscricaoEstadual(_ie, _uf)){
                _vr2.class = 'success';
                _vr2.text = 'Inscrição Estadual parece correta.';
            } else {
                _vr2.class = 'danger';
                _vr2.text = 'Inscrição Estadual não é válida.';
            }
        }
        
        //REGRA 3 - Verificar CEP
        var _cep = $('#DestinoCep').val().toString().replace(/[^0-9]/g, '');
        if (_cep.length==8){
            if (_cep_testado!=_cep){
                _cep_testado = _cep;
                _vr3.class = 'warning';
                _vr3.text = 'Testando CEP, aguarde.';
                _correiosCep.query(_cep, function(obj){
                    var _rcep = obj.cep.toString().replace(/[^0-9]/g, '');
                    if (_rcep == _cep_testado){
                        if (obj.ibge.length==7){
                            _cep_data = obj;
                            _vr3.class = 'success';
                            _vr3.text = 'CEP informado e validado.';
                        } else {
                            _vr3.class = 'danger';
                            _vr3.text = 'CEP inválido/não validado.';
                        }
                    }
                }, function(){
                    _vr3.class = 'warning';
                    _vr3.text = 'Testando CEP falhou.';
                });
            }
        } else {
            _vr3.class = 'danger';
            _vr3.text = 'Campo CEP inválido.';
        }
        
        //REGRA 4 - Verificar Endereço com CEP
        _vr4.class = 'danger';
        _vr4.text = 'Endereço não validado com CEP.';
        if (_vr3.class=='success'){
            var _ediff = false;
            var _campos = [];
            
            var _logradouroA = $('#DestinoEndereco').val();
            var _logradouroB = _cep_data.logradouro;
            if (similarity(_logradouroA, _logradouroB)<0.8){
                _ediff = true;
                _campos[_campos.length] = 'Logradouro';
            }
            
            var _bairroA = $('#DestinoBairro').val();
            var _bairroB = _cep_data.bairro;
            if (similarity(_bairroA, _bairroB)<0.8){
                _ediff = true;
                _campos[_campos.length] = 'Bairro';
            }
            
            var _ufA = $('#DestinoUf').val().toLowerCase();
            var _ufB = _cep_data.uf.toLowerCase();
            if (_ufA!=_ufB){
                _ediff = true;
                _campos[_campos.length] = 'UF';
            }
            
            var _cMunA = $('#DestinoIbgeCidadeId').val();
            var _cMunB = _cep_data.ibge;
            if (_cMunA!=_cMunB){
                _ediff = true;
                _campos[_campos.length] = 'Cidade';
            }
            
            if (!_ediff){
                _vr4.class = 'success';
                _vr4.text = 'Dados do endereço batem com CEP.';
            } else {
                _vr4.class = 'danger';
                _vr4.text = 'Dados ('+_campos.join(', ')+') diferente(s) do CEP.';
            }
        }
        
        //REGRA 5 - Verificar RNTRC
        var _cia_aerea = $('#DestinoCiaAerea').is(':checked');
        var _transportador = $('#DestinoTransportador').is(':checked');
        var _transportador_agente = $('#DestinoTransportadorAgente').is(':checked');
        if (_cia_aerea || _transportador || _transportador_agente){
            if ($('#DestinoRNTRC').val().length==8){
                _vr5.class = 'success';
                _vr5.text = 'RNTRC parece correto.';
                $('.panel-transportador').removeClass('panel-danger').addClass('panel-default');
            } else {
                _vr5.class = 'danger';
                _vr5.text = 'RNTRC não é válido.';
                $('.panel-transportador').removeClass('panel-default').addClass('panel-danger');
            }
        } else {
            _vr5.class = 'success';
            _vr5.text = 'RNTRC não é necessário.';
            $('.panel-transportador').removeClass('panel-danger').addClass('panel-default');
        }
        
        //REGRA 6 - Verificar Diretivas Transporte
        var _cliente = $('#DestinoCliente').is(':checked');
        if (_cliente){
            var _err = false;
            if ($('#DestinoTransporteEmitenteId option:selected').val()=='') _err = true;
            if ($('#DestinoTransporteCteCfop').val()=='') _err = true;
            if ($('#DestinoTransporteTipoCargaId option:selected').val()=='') _err = true;
            if ($('#DestinoTransporteProdPred').val()=='') _err = true;
            if ($('#DestinoTransporteCteNatOpe').val()=='') _err = true;
            if (_err){
                _vr6.class = 'danger';
                _vr6.text = 'Preencha as diretivas de Transporte.';
                $('.panel-transporte').removeClass('panel-default').addClass('panel-danger');
            } else {
                _vr6.class = 'success';
                _vr6.text = 'Diretivas de Transporte OK.';
                $('.panel-transporte').removeClass('panel-danger').addClass('panel-default');
            }
        } else {
            _vr6.class = 'success';
            _vr6.text = 'Diretivas de Transporte OK.';
            $('.panel-transporte').removeClass('panel-danger').addClass('panel-default');
        }
        
        //REGRA 7 - Verificar Diretivas Armazenagem
        var _cliente = $('#DestinoCliente').is(':checked');
        if (_cliente){
            var _err = false;
            if ($('#DestinoArmazenagemSaidaEmitenteId option:selected').val()=='') _err = true;
            if ($('#DestinoArmazenagemEntradaCfop').val()=='') _err = true;
            if ($('#DestinoArmazenagemEntradaNatOp').val()=='') _err = true;
            if ($('#DestinoArmazenagemSaidaCfop').val()=='') _err = true;
            if ($('#DestinoArmazenagemSaidaNatOp').val()=='') _err = true;
            if (_err){
                _vr7.class = 'danger';
                _vr7.text = 'Preencha as diretivas de Armazenagem.';
                $('.panel-armazenagem').removeClass('panel-default').addClass('panel-danger');
            } else {
                _vr7.class = 'success';
                _vr7.text = 'Diretivas de Armazenagem OK.';
                $('.panel-armazenagem').removeClass('panel-danger').addClass('panel-default');
            }
        } else {
            _vr7.class = 'success';
            _vr7.text = 'Diretivas de Armazenagem OK.';
            $('.panel-armazenagem').removeClass('panel-danger').addClass('panel-default');
        }
        
        
        //REGRA 8 - Verificar Diretivas NF-e
        var _nfe = $('#DestinoEmissorNfe').is(':checked');
        if (_nfe){
            var _err = false;
            if ($('#DestinoIm').val()=='') _err = true;
            if ($('#DestinoCnae').val()=='') _err = true;
            if ($('#DestinoCrt option:selected').val()=='') _err = true;
            if ($('#DestinoProximaNf').val()=='') _err = true;
            if (_err){
                _vr8.class = 'danger';
                _vr8.text = 'Preencha as diretivas de NFe.';
                $('.panel-emissor-nfe').removeClass('panel-default').addClass('panel-danger');
            } else {
                _vr8.class = 'success';
                _vr8.text = 'Diretivas de NFe OK.';
                $('.panel-emissor-nfe').removeClass('panel-danger').addClass('panel-default');
            }
        } else {
            _vr8.class = 'success';
            _vr8.text = 'Diretivas de NFe OK.';
            $('.panel-emissor-nfe').removeClass('panel-danger').addClass('panel-default');
        }
        
        mostrarRegras();
    }
    
    function mostrarRegras(){
        $('#validar-regra-1').removeClass('list-group-item-warning').removeClass('list-group-item-danger').removeClass('list-group-item-success');
        $('#validar-regra-2').removeClass('list-group-item-warning').removeClass('list-group-item-danger').removeClass('list-group-item-success');
        $('#validar-regra-3').removeClass('list-group-item-warning').removeClass('list-group-item-danger').removeClass('list-group-item-success');
        $('#validar-regra-4').removeClass('list-group-item-warning').removeClass('list-group-item-danger').removeClass('list-group-item-success');
        $('#validar-regra-5').removeClass('list-group-item-warning').removeClass('list-group-item-danger').removeClass('list-group-item-success');
        $('#validar-regra-6').removeClass('list-group-item-warning').removeClass('list-group-item-danger').removeClass('list-group-item-success');
        $('#validar-regra-7').removeClass('list-group-item-warning').removeClass('list-group-item-danger').removeClass('list-group-item-success');
        $('#validar-regra-8').removeClass('list-group-item-warning').removeClass('list-group-item-danger').removeClass('list-group-item-success');
        $('#validar-regra-1').addClass('list-group-item-'+_vr1.class).html('<i class="fa fa-'+(_vr1.class=='success' ? 'check-circle':(_vr1.class=='warning' ? 'hourglass-half':'exclamation-triangle'))+'"></i> '+_vr1.text);
        $('#validar-regra-2').addClass('list-group-item-'+_vr2.class).html('<i class="fa fa-'+(_vr2.class=='success' ? 'check-circle':(_vr2.class=='warning' ? 'hourglass-half':'exclamation-triangle'))+'"></i> '+_vr2.text);
        $('#validar-regra-3').addClass('list-group-item-'+_vr3.class).html('<i class="fa fa-'+(_vr3.class=='success' ? 'check-circle':(_vr3.class=='warning' ? 'hourglass-half':'exclamation-triangle'))+'"></i> '+_vr3.text);
        $('#validar-regra-4').addClass('list-group-item-'+_vr4.class).html('<i class="fa fa-'+(_vr4.class=='success' ? 'check-circle':(_vr4.class=='warning' ? 'hourglass-half':'exclamation-triangle'))+'"></i> '+_vr4.text);
        $('#validar-regra-5').addClass('list-group-item-'+_vr5.class).html('<i class="fa fa-'+(_vr5.class=='success' ? 'check-circle':(_vr5.class=='warning' ? 'hourglass-half':'exclamation-triangle'))+'"></i> '+_vr5.text);
        $('#validar-regra-6').addClass('list-group-item-'+_vr6.class).html('<i class="fa fa-'+(_vr6.class=='success' ? 'check-circle':(_vr6.class=='warning' ? 'hourglass-half':'exclamation-triangle'))+'"></i> '+_vr6.text);
        $('#validar-regra-7').addClass('list-group-item-'+_vr7.class).html('<i class="fa fa-'+(_vr7.class=='success' ? 'check-circle':(_vr7.class=='warning' ? 'hourglass-half':'exclamation-triangle'))+'"></i> '+_vr7.text);
        $('#validar-regra-8').addClass('list-group-item-'+_vr8.class).html('<i class="fa fa-'+(_vr8.class=='success' ? 'check-circle':(_vr8.class=='warning' ? 'hourglass-half':'exclamation-triangle'))+'"></i> '+_vr8.text);
    }
    
    function similarity(s1, s2) {
      var longer = s1;
      var shorter = s2;
      if (s1.length < s2.length) {
        longer = s2;
        shorter = s1;
      }
      var longerLength = longer.length;
      if (longerLength == 0) {
        return 1.0;
      }
      return (longerLength - editDistance(longer, shorter)) / parseFloat(longerLength);
    }
    function editDistance(s1, s2) {
        s1 = s1.toLowerCase();
        s2 = s2.toLowerCase();

        var costs = new Array();
        for (var i = 0; i <= s1.length; i++) {
          var lastValue = i;
          for (var j = 0; j <= s2.length; j++) {
            if (i == 0)
              costs[j] = j;
            else {
              if (j > 0) {
                var newValue = costs[j - 1];
                if (s1.charAt(i - 1) != s2.charAt(j - 1))
                  newValue = Math.min(Math.min(newValue, lastValue),
                    costs[j]) + 1;
                costs[j - 1] = lastValue;
                lastValue = newValue;
              }
            }
          }
          if (i > 0)
            costs[s2.length] = lastValue;
        }
        return costs[s2.length];
      }
    <?php $this->start('script-onload'); ?>
    $('#DestinoUf').change(function(){ MostraCidades() }).change();
    $('#DestinoIbgeCidadeId').change(function(){ UpdateMunicipio() }).change();
    $('#PreencherComCep').click(function(){ PreencheComCEPRequest() });
    $('#DestinoRgInscEstadual').focus();
    $('#DestinoArmazem').change(function(){
        if ($('#DestinoArmazem').is(':checked')){
            $('.panel-armazem').show();
        } else {
            $('.panel-armazem').hide();
        }
    }).change();
    $('#DestinoTransportador').change(function(){
        if ($('#DestinoTransportador').is(':checked')){
            $('.panel-transportador').show();
        } else {
            $('.panel-transportador').hide();
        }
    }).change();
    $('#DestinoTransportadorAgente').change(function(){
        if ($('#DestinoTransportadorAgente').is(':checked')){
            $('.panel-transportador-agente').show();
        } else {
            $('.panel-transportador-agente').hide();
        }
    }).change();
    $('#DestinoCiaAerea').change(function(){
        if ($('#DestinoCiaAerea').is(':checked')){
            $('.panel-cia_aerea').show();
        } else {
            $('.panel-cia_aerea').hide();
        }
    }).change();
    $('#DestinoCliente').change(function(){
        if ($('#DestinoCliente').is(':checked')){
            $('.panel-cliente').show();
        } else {
            $('.panel-cliente').hide();
        }
    }).change();
    $('#DestinoEmitenteNfe').change(function(){
        if ($('#DestinoEmitenteNfe').is(':checked')){
            $('.panel-emissor-nfe').show();
        } else {
            $('.panel-emissor-nfe').hide();
        }
    }).change();
    var _v = null;
    _v = setInterval('validaRegras()', 10);
    <?php $this->end(); ?>
</script>
<?=$this->Session->flash();?>
<?=$this->Form->create('Destino');?>
<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title">Informações Básicas</h1>
            </div>
            <table class="table table-striped table-condensed table-bordered">
                <tr>
                    <td colspan="4" width="33%">
                        <label for="DestinoCpfCnpj"><i class="fa fa-anchor"></i> CNPJ ou CPF</label>
                        <input type="text" name="data[Destino][cpf_cnpj]" class="form-control mask_int" id="DestinoCpfCnpj" placeholder="CPF/CNPJ" value="<?=exibirCpfCnpj($this->request->data['Destino']['cpf_cnpj'])?>" aria-describedby="basic-addon1">
                    </td>
                    <td colspan="4" width="33%">
                        <?=$this->Form->input('rg_insc_estadual', array('label' => '<i class="fa fa-certificate"></i> Inscrição Estadual ou RG', 'class' => 'form-control'));?>
                    </td>
                    <td colspan="4" width="33%">
                        <?=$this->Form->input('codigo_cliente', array('label' => '<i class="fa fa-chain"></i> Código de Identificação', 'class' => 'form-control'));?>
                    </td>
                </tr>
                <tr>
                    <td width="50%" colspan="6">
                        <?=$this->Form->input('nome_razao', array('label' => '<i class="fa fa-user-md"></i> Razão Social / Nome Completo', 'class' => 'form-control', 'onkeyup' => '$(\'#DestinoFantasia\').val(this.value);'));?>
                    </td>
                    <td width="50%" colspan="6">
                        <?=$this->Form->input('fantasia', array('label' => '<i class="fa fa-user"></i> Nome Fantasia / Apelido', 'class' => 'form-control'));?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title">Informações de Contato</h1>
            </div>
            <table class="table table-striped table-condensed table-bordered">
                <tr>
                    <td colspan="4">
                        <?=$this->Form->input('email', array('label' => '<i class="fa fa-envelope"></i> E-mail (envia NF-es/CT-es)', 'class' => 'form-control'));?>
                    </td>
                    <td colspan="4">
                        <?=$this->Form->input('telefones', array('label' => '<i class="fa fa-phone"></i> Telefones para Contato', 'type' => 'text', 'class' => 'form-control'));?>
                    </td>
                    <td colspan="4">
                        <?=$this->Form->input('site', array('label' => '<i class="fa fa-globe"></i> Site / Página do Facebook', 'class' => 'form-control'));?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title">Dados para Localização</h1>
            </div>
            <table class="table table-striped table-condensed table-bordered">
                <tr>
                    <td colspan="5">
                        <div class="input-group">
                            <span class="input-group-addon">CEP</span>
                            <?=$this->Form->text('cep', array('class' => 'form-control', 'placeholder' => 'Digite o CEP'));?>
                            <span class="input-group-btn">
                              <button class="btn btn-default" type="button" id="PreencherComCep">Puxar Endereço pelo CEP</button>
                            </span>
                        </div>
                    </td>
                    <td colspan="7" id="cep-status"></td>
                </tr>
                <tr>
                    <td width="8%"></td>
                    <td width="8%"></td>
                    <td width="8%"></td>
                    <td width="8%"></td>
                    <td width="8%"></td>
                    <td width="8%"></td>
                    <td width="8%"></td>
                    <td width="8%"></td>
                    <td width="8%"></td>
                    <td width="8%"></td>
                    <td width="8%"></td>
                    <td width="8%"></td>
                </tr>
                <tr>
                    <td colspan="2"><?=$this->Form->input('uf', array('type' => 'select', 'class' => 'form-control', 'label' => 'UF', 'options' => $estados));?></td>
                    <td colspan="5"><?=$this->Form->input('ibge_cidade_id', array('type' => 'select', 'data-sel' => $this->request->data['Destino']['ibge_cidade_id'], 'class' => 'form-control', 'label' => 'Municipio'));?></td>
                    <td colspan="5"><?=$this->Form->input('bairro', array('type' => 'text', 'class' => 'form-control', 'label' => 'Bairro'));?></td>
                </tr>
                <tr>
                    <td colspan="6"><?=$this->Form->input('endereco', array('type' => 'text', 'class' => 'form-control', 'label' => 'Logradouro'));?></td>
                    <td colspan="3"><?=$this->Form->input('numero', array('type' => 'text', 'class' => 'form-control', 'label' => 'Número'));?></td>
                    <td colspan="3"><?=$this->Form->input('complemento', array('type' => 'text', 'class' => 'form-control', 'label' => 'Complemento'));?></td>
                </tr>
            </table>
        </div>
        <div class="panel panel-default panel-cia_aerea panel-transportador-agente panel-transportador" style="display: none;">
            <div class="panel-heading">
                <h1 class="panel-title">Informações de Transportadora/Recebedor/Agente</h1>
            </div>
            <table class="table table-striped table-condensed table-bordered">
                <tr>
                    <td><?=$this->Form->input('RNTRC', array('class' => 'form-control', 'label' => 'Cadastro RNTRC'));?></td>
                    <td><?=$this->Form->input('transportador_raio_em_km', array('class' => 'form-control mask_int', 'label' => 'Raio de Atuação em KMs', 'type' => 'text'));?></td>
                </tr>
            </table>
        </div>
        <div class="panel panel-default panel-cliente panel-emissor-nfe" style="display: none;">
            <div class="panel-heading">
                <h1 class="panel-title">Dados de Emissor de NFe</h1>
            </div>
            <table class="table table-striped">
                <tr>
                    <td width="50%"><?=$this->Form->input('im', array('label'=>'Inscrição Municipal'));?></td>
                    <td width="50%"><?=$this->Form->input('cnae', array('label'=>'CNAE Fiscal'));?></td>
                </tr>
                <tr>
                    <td><?=$this->Form->input('crt', array('label'=>'Código de Regime Tributário - CRT', 'options'=>array(1 => '1 - Simples Nacional', 2 => '2 - Simples Nacional - excesso de sublimite de receita bruta', 3 => '3 - Regime Normal')));?></td>
                    <td><?=$this->Form->input('proxima_nf', array('type'=>'text','label'=>'Número da Última NF Emitida'));?></td>
                </tr>
                <tr>
                    <td><?=$this->Form->input('iest', array('label'=>'Inscrição Estadual do Substituto Tributário'));?></td>
                </tr>
            </table>
        </div>
        <div class="panel panel-default panel-cliente panel-transporte" style="display: none;">
            <div class="panel-heading">
                <h1 class="panel-title">Diretivas do Cliente para Transporte</h1>
            </div>
            <table class="table table-striped table-condensed table-bordered">
                <tr>
                    <td width="25%"><?=$this->Form->input('transporte_emitente_id', array('label' => 'Emitente CT-e', 'class' => 'form-control', 'empty' => ' - Emitente CT-e - ', 'options' => $emitentes));?></td>
                    <td width="25%"><?=$this->Form->input('transporte_cte_cfop', array('class' => 'form-control mask_int', 'label' => 'CFOP: 5353 padrão', 'type' => 'text'));?></td>
                    <td width="25%"><?=$this->Form->input('transporte_tipo_carga_id', array('label' => 'Natureza da Mercadoria', 'class' => 'form-control', 'empty' => ' - Tipo de Carga - ', 'options' => $tipoCarga));?></td>
                    <td width="25%"><?=$this->Form->input('transporte_prodPred', array('class' => 'form-control', 'label' => 'Produto Predominante', 'type' => 'text'));?></td>
                </tr>
                <tr>
                    <td colspan="3">
                        <?=$this->Form->input('transporte_cte_natOpe', array('type' => 'Nat. Op. de Transporte', 'class' => 'form-control', 'placeholder' => 'Prest. Serv. Transp. a Estab. Comercial'));?>
                    </td>
                    <td colspan="4"><a href="/cadastros/gerenciar/icms/<?=($this->request->data['Destino']['icms_nao_cobrar']==1 ? 'desativar':'ativar')?>/<?=$this->request->data['Destino']['id']?>" class="btn btn-info"><?=($this->request->data['Destino']['icms_nao_cobrar']==1 ? 'Não está sendo Faturado ICMS - clique para inverter':'Faturando ICMS para o Cliente - clique para inverter')?></button></td>
                </tr>
            </table>
        </div>
        <div class="panel panel-default panel-cliente panel-armazenagem" style="display: none;">
            <div class="panel-heading">
                <h1 class="panel-title">Diretivas do Cliente para Armazenagem</h1>
            </div>
            <table class="table table-striped table-condensed table-bordered">
                <tr>
                    <td width="50%" colspan="2"><?=$this->Form->input('armazenagem_saida_emitente_id', array('label' => 'Emitente NF-e', 'class' => 'form-control', 'empty' => ' - Emitente NF-e - ', 'options' => $emitentes));?></td>
                    <td width="50%" colspan="2"><?=$this->Form->input('separacao_horizontal', array('type' => 'select', 'class' => 'form-control', 'label' => 'Modelo de Guia de Separação', 'empty' => false, 'options' => [
                        0 => 'Vertical',
                        1 => 'Horizontal',
                    ]));?></td>
                </tr>
                <tr>
                    <td width="25%"><?=$this->Form->input('armazenagem_entrada_cfop', array('class' => 'form-control mask_int', 'label' => 'CFOP de Entrada no Armazém', 'type' => 'text'));?></td>
                    <td colspan="3">
                        <?=$this->Form->input('armazenagem_entrada_natOp', array('type' => 'Nat. Op. de Entrada', 'class' => 'form-control', 'placeholder' => 'Prest. Serv. Transp. a Estab. Comercial'));?>
                    </td>
                </tr>
                <tr>
                    <td width="25%"><?=$this->Form->input('armazenagem_saida_cfop', array('class' => 'form-control mask_int', 'label' => 'CFOP de Saída no Armazém', 'type' => 'text'));?></td>
                    <td colspan="3">
                        <?=$this->Form->input('armazenagem_saida_natOp', array('type' => 'Nat. Op. de Saída', 'class' => 'form-control', 'placeholder' => 'Prest. Serv. Transp. a Estab. Comercial'));?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title">Operações deste Cadastro</h1>
            </div>
            <div class="panel-body">
                <p><strong>ATENÇÃO:</strong> Habilitar ou desabilitar operações podem causar diversos problemas, só altere algo aqui se você tem certeza do que estiver fazendo.</p>
            </div>
            <table class="table table-striped table-condensed">
                <tr>
                    <td><?=$this->Form->input('armazem', ['label' => 'Armazém', 'type' => 'checkbox', 'value' => 1])?></td>
                    <td><?=$this->Form->input('cia_aerea', ['label' => 'Cia Aérea', 'type' => 'checkbox', 'value' => 1])?></td>
                    <td><?=$this->Form->input('emitente_nfe', ['label' => 'Emitente NFe', 'type' => 'checkbox', 'value' => 1])?></td>
                </tr>
                <tr>
                    <td><?=$this->Form->input('emitente_cte', ['label' => 'Emitente CTe', 'type' => 'checkbox', 'value' => 1])?></td>
                    <td><?=$this->Form->input('cliente', ['label' => 'Cliente', 'type' => 'checkbox', 'value' => 1])?></td>
                    <td><?=$this->Form->input('fornecedor', ['label' => 'Fornecedor', 'type' => 'checkbox', 'value' => 1])?></td>
                </tr>
                <tr>
                    <td><?=$this->Form->input('oficina', ['label' => 'Oficinas', 'type' => 'checkbox', 'value' => 1])?></td>
                    <td><?=$this->Form->input('transportador', ['label' => 'Transportadora', 'type' => 'checkbox', 'value' => 1])?></td>
                    <td><?=$this->Form->input('transportador_agente', ['label' => 'Recebedor/Agente', 'type' => 'checkbox', 'value' => 1])?></td>
                </tr>
                <tr>
                    <td><?=$this->Form->input('correios', ['label' => 'Correios', 'type' => 'checkbox', 'value' => 1])?></td>
                    <td><?=$this->Form->input('embarcador', ['label' => 'Embarcador', 'type' => 'checkbox', 'value' => 1])?></td>
                </tr>
            </table>
        </div>
        
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title">Dados Bancários</h1>
            </div>
            <div class="panel-body">
                <?=$this->Form->input('dados_bancarios', array('type' => 'textarea', 'class' => 'form-control', 'label' => false));?>
            </div>
        </div>
        
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title">Verificações do Cadastro</h1>
            </div>
            <div class="list-group">
                <a href="#" id="validar-regra-1" class="list-group-item list-group-item-warning">CPF/CNPJ válido.</a>
                <a href="#" id="validar-regra-2" class="list-group-item list-group-item-warning">Inscrição Estadual válida.</a>
                <a href="#" id="validar-regra-3" class="list-group-item list-group-item-warning">CEP válido informado.</a>
                <a href="#" id="validar-regra-4" class="list-group-item list-group-item-warning">Endereço igual ao CEP.</a>
                <a href="#" id="validar-regra-8" class="panel-emissor-nfe list-group-item list-group-item-warning" style="display:none;">Diretivas de NFe.</a>
                <a href="#" id="validar-regra-5" class="panel-cia_aerea panel-transportador-agente panel-transportador list-group-item list-group-item-warning" style="display:none;">Cia Aérea / Transportadora / Recebedor / Agente informado RNTRC.</a>
                <a href="#" id="validar-regra-6" class="panel-cliente list-group-item list-group-item-warning" style="display:none;">Diretivas de Transporte informadas.</a>
                <a href="#" id="validar-regra-7" class="panel-cliente list-group-item list-group-item-warning" style="display:none;">Diretivas de Armazenagem informadas.</a>
            </div>
        </div>
    </div>
    
</div>
<div class="row">
    <div class="col-md-4 col-md-offset-4 text-center">
        <button type="submit" class="btn btn-success">Salvar Alterações <i class="fa fa-arrow-circle-o-right"></i></button>
    </div>
</div>
<?=$this->Form->hidden('municipio');?>
<?=$this->Form->end();?>