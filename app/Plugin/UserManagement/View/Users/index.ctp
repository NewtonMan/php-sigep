<?=$this->Session->flash();?>

<script>
function SetSrc(_src){
    $('#user-iframe').attr('src', _src);
}
</script>

<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <iframe id='user-iframe' width="100%" framebborder="0" height="500"></iframe>
    </div>
  </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title"><?=_('Users');?></h1>
    </div>
    <div class="panel-body">
        <form method="get" action="/user_management/users/index">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Pesquisar" name="crudSearch" value="<?=@$_GET['crudSearch']?>" autofocus="">
                <span class="input-group-btn">
                    <button class="btn btn-success" type="submit">Pesquisar</button>
                </span>
            </div>
        </form>
    </div>
    <table class="table table-condensed table-striped">
        <tr>
            <th colspan="2">
                <?=$this->Paginator->sort('User.nome_completo', 'Nome');?> / 
                <?=$this->Paginator->sort('User.empregador', 'Empresa');?> / 
                <?=$this->Paginator->sort('User.RG', 'RG');?> / 
                <?=$this->Paginator->sort('User.CPF', 'CPF');?> / 
                <?=$this->Paginator->sort('User.active', 'Status');?> / 
                <?=$this->Paginator->sort('User.created', 'Cadastro');?> / 
                <?=$this->Paginator->sort('User.modified', 'Atualizado');?> / 
                <?=$this->Paginator->sort('User.last_access', 'Ult. Acesso App');?>
            </th>
            <th class="text-right">
                <div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?=_('Options');?> <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="#" onclick="SetSrc('/user_management/users/add');" data-toggle="modal" data-target="#formModal"><i class="fa fa-plus"></i> <?=_('Novo Cadastro de Pessoa');?></a></li>
                    <li><a href="#" onclick="SetSrc('/user_management/users/importar');" data-toggle="modal" data-target="#formModal"><i class="fa fa-upload"></i> <?=_('Importar Planilha');?></a></li>
                    <li><a href="/user_management/users/exportar"><i class="fa fa-download"></i> <?=_('Exportar Planilha');?></a></li>
                  </ul>
                </div>
            </th>
        </tr>
        <?php
        foreach ($list as $item):
            $item['User']['telefone1'] = trim($item['User']['telefone1']);
            $item['User']['telefone2'] = trim($item['User']['telefone2']);
            ?>
        <tr>
            <td width="100">
                <img src="<?=(empty($item['User']['avatar']) ? '/img/avatar.png':"/files/{$item['User']['avatar']}")?>" width="100%" class="avatar" />
            </td>
            <td>
                <?=$item['User']['nome_completo'];?> - <?=$item['User']['cargo'];?> - <?=$item['User']['empregador'];?> <div class="label label-<?=($item['User']['active']==1 ? 'success':'danger')?>"><?=($item['User']['active']==1 ? 'Ativo':'Inativo')?></div><br/>
                RG <?=$item['User']['RG'];?> / CPF <?=$item['User']['CPF'];?><br/>
                E-mail: <?=$item['User']['email'];?> / Tel: <?=$item['User']['telefone1'];?> <?=(!empty($item['User']['telefone1']) && !empty($item['User']['telefone2']) ? ' / ':'');?> <?=$item['User']['telefone2'];?><br/>
                <small>cadastro: <?=$item['User']['created']?> / atualizado: <?=$item['User']['modified']?> / cadastro: <?=$item['User']['last_seen']?></small>
            </td>
            <td class="text-right">
                <div class="btn-group btn-group-xs">
                    <!--
                    <a href="/user_management/users/avatar/<?=$item['User']['id'];?>" class="btn btn-info btn-xs"><i class="fa fa-photo"></i> avatar</a>
                    <a href="/cracha/download/<?=$item['User']['CPF'];?>" target="_blank" class="btn btn-default btn-xs"><i class="fa fa-id-card"></i> crachá</a>
                    <a href="/convites/email/<?=$item['User']['CPF'];?>" target="_blank" class="btn btn-default btn-xs"><i class="fa fa-id-card"></i> e-mail convite</a>
                    <a href="/convites/download/<?=$item['User']['CPF'];?>" target="_blank" class="btn btn-default btn-xs"><i class="fa fa-id-card"></i> convite</a>
                    <a href="#" onclick="SetSrc('/cracha/etiqueta/<?=$item['User']['id'];?>');" data-toggle="modal" data-target="#formModal" class="btn btn-default btn-xs"><i class="fa fa-id-card"></i> crachá</a>
                    -->
                    <a href="#" onclick="SetSrc('/user_management/users/edit/<?=$item['User']['id'];?>');" data-toggle="modal" data-target="#formModal" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> editar</a>
                    <a href="/user_management/users/del/<?=$item['User']['id'];?>" class="btn btn-danger btn-xs" onclick="return confirm('<?=_('WARNING: Do you really want to delete this?')?>');"><i class="fa fa-trash"></i> excluir</a>
                </div>
            </td>
        </tr>
        <?endforeach;?>
    </table>
    <div class="panel-footer">
        <?=$this->Paginator->numbers(array(
            'prev' => _('Preview'),
            'next' => _('Next'),
        ));?>
    </div>
</div>
<!-- .modal-profile -->
<div class="modal fade modal-profile" tabindex="-1" role="dialog" aria-labelledby="modalProfile" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" type="button" data-dismiss="modal">×</button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<!-- //.modal-profile -->
<script>
<?php
$this->start('script-onload');
?>
$('a.thumb').click(function(event){
    event.preventDefault();
    var content = $('.modal-body');
    content.empty();
    var title = $(this).attr("title");
    $('.modal-title').html(title);      	
    content.html($(this).html());
    $(".modal-profile").modal({show:true});
});
<?php
$this->end();
?>
</script>