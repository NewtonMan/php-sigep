'use strict';
class Cadastro {
    constructor(_id, _name){
        this.id = _id;
        this.name = _name;
        this.value = $('#'+_id).val();
        this.urlRead = '/cadastros/rest/read.json?value=';
        this.urlList = '/cadastros/rest/list.json';
    }
    
    get state(){
        return this._state;
    }
    
    set state(value){
        this._state = value;
    }
    
    get required(){
        return this._required;
    }
    
    set required(value){
        this._required = value;
    }
    
    get page(){
        return this._page;
    }
    
    set page(value){
        this._page = value;
    }
    
    get pages(){
        return this._pages;
    }
    
    set pages(value){
        this._pages = value;
    }
    
    get list(){
        return this._list;
    }
    
    set list(value){
        this._list = value;
    }
    
    get data(){
        return this._data;
    }
    
    set data(value){
        this._data = value;
    }
    
    get selector(){
        return this._selector;
    }
    
    set selector(value){
        this._selector = value;
    }
    
    get placeholder(){
        return (this._placeholder===null ? '':this._placeholder);
    }
    
    set placeholder(value){
        this._placeholder = (value===null ? '':value);
    }
    
    get urlRead(){
        return this._urlRead;
    }
    
    set urlRead(value){
        this._urlRead = value;
    }
    
    get urlList(){
        return this._urlList;
    }
    
    set urlList(value){
        this._urlList = value;
    }
    
    get name(){
        return this._name;
    }
    
    set name(value){
        this._name = value;
    }
    
    get value(){
        return this._value;
    }
    
    set value(value){
        this._value = value;
        this.loadData();
    }
    
    get error(){
        return this._error;
    }
    
    set error(value){
        this._error = value;
    }
    
    get loading(){
        return this._loading;
    }
    
    set loading(value){
        this._loading = value;
    }
    
    get id(){
        return this._id;
    }
    
    set id(value){
        this._id = value;
    }
    
    get term(){
        return this._term;
    }
    
    set term(value){
        this._term = value;
    }
    
    loadData(){
        this.data = {};
        if (this._value!==null){
            this.loading = true;
            $.get(this.urlRead+this.value, function(json) {
                this.loading = false;
                if (json.data===undefined){
                    $('#'+this.id+'-display').val('');
                } else if (json.data!==false && json.data.Destino!==undefined){
                    this.data = json.data.Destino;
                    $('#'+this.id+'-display').val(this.data.info);
                } else {
                    $('#'+this.id+'-display').val('');
                }
            }.bind(this)).fail(function(){
                this.loading = false;
                this.error = false;
                $('#'+this.id+'-display').val('Erro no carregamento...');
            }.bind(this));
        }
    }
    
    results(){
        var _html = '';
        for (var i=0; i<this.list.length; i++){
            var Destino = this.list[i].Destino;
            _html = _html + '<tr><td data-destino-id="'+Destino.id+'" onclick="$(this).addClass(\'active\');" onmouseover="this.style.cursor=\'pointer\';"><strong>'+Destino.info+'\n\
'+(Destino.fornecedor==1 ? '<div class="label label-default">Fornecedor</div>':'')+'\
'+(Destino.cliente==1 ? '<div class="label label-default">Cliente</div>':'')+'\
'+(Destino.armazem==1 ? '<div class="label label-default">Armazem</div>':'')+'\
'+(Destino.cia_aerea==1 ? '<div class="label label-default">Cia Aérea</div>':'')+'\
'+(Destino.transportador==1 ? '<div class="label label-default">Transportadora</div>':'')+'\
'+(Destino.transportador_agente==1 ? '<div class="label label-default">Agente</div>':'')+'\
</strong><br/>'+Destino.endereco+(Destino.numero=='' ? '':', '+Destino.numero)+(Destino.complemento=='' ? '':', '+Destino.complemento)+' - '+Destino.municipio+'/'+Destino.uf+' - Tel: '+Destino.telefones+' / Email: '+Destino.email+'</td></tr>';
        }
        $('#'+this.id+'-results tbody.results').html(_html);
        $('#'+this.id+'-results tbody.results tr td').click(function(){
            this.value = $('#'+this.id+'-results tbody.results tr td.active').data('destino-id');
            $('#'+this.id+'-value').val(this.value);
            this.loadData();
            this.state = 'selected';
        }.bind(this));
    }
    
    get html(){
        return '\n\
<input id="'+this.id+'-value" type="hidden" name="'+this.name+'" value="'+this.value+'"'+(this.required ? ' required':'')+'>\n\
<div class="input-group" id="'+this.id+'-search">\n\
    <input type="text" id="'+this.id+'-term" class="form-control" placeholder="'+this.placeholder+'">\n\
    <span class="input-group-btn">\n\
        <button class="btn btn-primary btn-search" type="button">Pesquisar</button>\n\
        <button class="btn btn-success btn-new" type="button">Novo Cadastro</button>\n\
    </span>\n\
</div>\n\
<table class="table table-striped table-condensed table-hover" id="'+this.id+'-results">\n\
<thead class="loading"><tr><th class="text-center"><img src="/img/carregando-bar.gif" /></th></tr></thead>\n\
<tbody class="results"></tbody>\n\
<tfooter>\n\
<tr><td class="text-center"></td></tr>\n\
</tfooter>\n\
</table>\n\
<div class="input-group" id="'+this.id+'-selected">\n\
    <input type="text" id="'+this.id+'-display" class="form-control disabled" value="Carregando..." disabled>\n\
    <span class="input-group-btn">\n\
        <button class="btn btn-default btn-change" type="button">Alterar</button>\n\
    </span>\n\
</div>\n\
';
    }
    
    search(){
        this.loading = true;
        $.get(this.urlList+'?term='+this.term+'&page='+this.page, function(response){
            this.loading = false;
            this.list = response.data;
            this.pages = response.paginator.pageCount;
            this.results();
        }.bind(this)).fail(function(){
            this.loading = false;
        }.bind(this));
    }
    
    replace(){
        this.value = $('#'+this.id).val();
        $(this.selector).parent().html(this.html);
        $( "#"+this.id+'-search .btn-search').click(function(){
            var _term = $( "#"+this.id+'-term').val();
            this.term = _term;
            this.page = 1;
            this.search();
        }.bind(this));
        $( "#"+this.id+'-search .btn-new').click(function(){
            window.open('/cadastros/gerenciar/add?modo=modal', this.id, "resizable=no, toolbar=no, scrollbars=no, menubar=no, status=no, directories=no, width=" + window.innerWidth + ", height=" + window.innerHeight + ", left=0, top=0");
        });
        $( "#"+this.id+'-selected .btn-change').click(function(){
            this.value = '';
            this.state = 'search';
            this.list = [];
            $( "#"+this.id+'-term').val('').focus();
            this.results();
        }.bind(this));
        if (this.i!==null){
            window.clearInterval(this.i);
        }
        window.setInterval(function(){
            if (this.loading){
                if ($("#"+this.id+'-results .loading').is(':hidden')) $("#"+this.id+'-results .loading').show(300);
            } else {
                if (!$("#"+this.id+'-results .loading').is(':hidden')) $("#"+this.id+'-results .loading').hide(200);
            }
            if (this.value=='' || this.value==null){
                this.state = 'search';
            } else {
                this.state = 'selected';
            }
            if (this.state=='search'){
                if ($( "#"+this.id+'-search').is(':hidden')) $( "#"+this.id+'-search').show(300);
                if ($( "#"+this.id+'-results').is(':hidden')) $( "#"+this.id+'-results').show(300);
                if (!$( "#"+this.id+'-selected').is(':hidden')) $( "#"+this.id+'-selected').hide(200);
            }
            if (this.state=='selected'){
                if (!$( "#"+this.id+'-search').is(':hidden')) $( "#"+this.id+'-search').hide(200);
                if (!$( "#"+this.id+'-results').is(':hidden')) $( "#"+this.id+'-results').hide(200);
                if ($( "#"+this.id+'-selected').is(':hidden')) $( "#"+this.id+'-selected').show(300);
            }
        }.bind(this), 25);
    }
    
}