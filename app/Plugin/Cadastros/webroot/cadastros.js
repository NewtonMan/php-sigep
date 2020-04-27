var _cadastros = {
    montar: function(_prefix, _title, _element_id, _data_id, _pode_alterar){
        var _html = '\n\
                <div class="row" id="Cadastro'+_prefix+'Form">\n\
                    <div class="col-md-12">\n\
                        <input name="data[' + _prefix + '][id]" id="' + _prefix + 'Id" type="hidden">\n\
                        <input name="data[' + _prefix + '][icms_nao_cobrar]" id="' + _prefix + 'IcmsNaoCobrar" type="hidden">\n\
                        <div class="form-control">\n\
                            <div class="input-group input-group-sm">\n\
                                <input name="data[' + _prefix + '][cpf_cnpj]" placeholder="' + _title + '" class="form-control ui-autocomplete-input" id="' + _prefix + 'CpfCnpj" type="text">\n\
                                <span class="input-group-btn">\n\
                                    <button class="btn btn-default" id="' + _prefix + 'Search" type="button">Pesquisar <i class="fa fa-search"></i></button>\n\
                                </span>\n\
                            </div>\n\
                        </div>\n\
                    </div>\n\
                </div>\n\
                <div class="row" id="Cadastro'+_prefix+'Data" style="display: none;">\n\
                    <div class="col-md-12" id="' + _prefix + 'Info"></div>\n\
                </div>';
        $('#'+_element_id).append(_html);
        if (_data_id!=undefined) _cadastros.getById(_prefix, _title, _data_id, _pode_alterar);
        $('#'+_prefix+'CpfCnpj').autocomplete({
            minLength: 2,
            focus: function( event, ui ) {
                return false;
            },
            select: function( event, ui ) {
                _cadastros.setObjData(_prefix, _title, ui.item.Destino, _pode_alterar);
                return false;
            },
            source: function( request, response ) {
                var _url = "/cadastros/gerenciar/source.json?term=" + $('#'+_prefix+'CpfCnpj').val();
                $.ajax({
                    url: _url,
                    data: { query: request.term},
                    success: function(data){
                        response(data);
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        _cadastros.formError(_prefix);
                    },
                    timeout: 1000,
                    dataType: 'json'
                });
            }
        }).autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div><b>" + item.Destino.fantasia + "</b> - CFP/CNPJ: " + item.Destino.cpf_cnpj + "<br>" + item.Destino.municipio + " / " + item.Destino.uf + "</div>" )
                .appendTo( ul );
        };
    },
    modalEdit: function(_prefix){
        var _id = $('#'+_prefix+'Id').val();
        if (_id!=undefined){
            abrePopUp('/cadastros/gerenciar/edit/'+_id+'?mode=modal');
        } else {
            alert('Nenhum Cadastro identificado para editar.');
        }
    },
    setObjData: function(_prefix, _title, _obj, _pode_alterar){
        $('#'+_prefix+'CpfCnpj').val(_obj.cpf_cnpj);
        $('#'+_prefix+'Id').val(_obj.id);
        $('#'+_prefix+'Info').html('\
    <div class="form-group">\n\
        <div class="input-group input-group-sm" style="width: 100%">\n\
            <span class="input-group-btn">\n\
                ' + (_pode_alterar != false ? "<a id=\"Cadastro"+_prefix+"Mudar\" class=\"btn btn-primary\"><i class=\"fa fa-times\"></i></a>":"") + '\n\
            </span>\n\
            <input id="Cadastro'+_prefix+'BtnEdit" class="form-control" id="' + _prefix + 'Fantasia" type="text" value="' + _title + ": " + _obj.fantasia + '" readonly="readonly" onmouseover="this.style.cursor=\'pointer\';" />\n\
        </div>\n\
    </div>\n\
');
        $("#Cadastro"+_prefix+"BtnEdit").click(function(){
            _cadastros.modalEdit(_prefix);
        });
        $("#Cadastro"+_prefix+"Mudar").click(function(){
            _cadastros.formReset(_prefix, _title);
        });
        $('#Cadastro'+_prefix+'Form').hide(500);
        $('#Cadastro'+_prefix+'Data').show(500);
    },
    formReset: function(_prefix, _title){
        $('#'+_prefix+'Id').val('');
        $('#'+_prefix+'CpfCnpj').val('');
        $('#'+_prefix+'IcmsNaoCobrar').val('');
        $('#Cadastro'+_prefix+'Form').show(500);
        $('#Cadastro'+_prefix+'Data').hide(500);
    },
    formError: function(_prefix){
        $('#'+_prefix+'Id').val('');
        $('#'+_prefix+'CpfCnpj').val('');
        $('#'+_prefix+'IcmsNaoCobrar').val('');
        $('#'+_prefix+'Info').html('<h5 class="text-danger">ERRO: Parece que há um problema com sua conexão com a internet.</h5>');
        $('#Cadastro'+_prefix+'Form').show(500);
    },
    getById: function(_prefix, _title, _data_id, _pode_alterar){
        $('#' + _prefix+'Info').html('<p>Carregando, aguarde...</p>');
        $.get('/cadastros/gerenciar/view/'+_data_id+'.json', function(data){
            if (data.data.Destino==undefined){
                _cadastros.formReset(_prefix, _title);
            } else {
                _cadastros.setObjData(_prefix, _title, data.data.Destino, _pode_alterar);
            }
        });
    },
};