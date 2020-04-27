<?=$this->Session->flash();?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title"><?=$titulo?></h1>
    </div>
    <table class="table table-striped">
        <tr>
            <th><?=$this->Paginator->sort('Destino.cpf_cnpj', 'CPF/CNPJ')?> - <?=$this->Paginator->sort('Destino.fantasia', 'Nome / Nome Fantasia')?></th>
            <th>
                <form class="form form-search" role="form" method="get" action="/cadastros/gerenciar/<?=(empty($this->request->param['action']) ? 'index':$this->request->param['action'])?>">
                    <div class="input-group input-group-sm">
                        <input type="text" name="sw" value="<?=@$_GET['sw']?>" class="form-control" placeholder="Pesquisar Palavra Chave" />
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
            </th>
            <th><?=$this->Paginator->sort('Destino.municipio', 'Municipio')?> / <?=$this->Paginator->sort('Destino.uf', 'UF')?></th>
            <th>
                <a href="/cadastros/gerenciar/add/<?=$marcar?>" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> Novo Cadastro</a>
                <a href="/cadastros/gerenciar/importar/<?=$marcar?>" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> Importar</a>
            </th>
        </tr>
        <?php foreach ($lista as $i){ ?>
        <tr>
            <td colspan="2">
                <?=highlight(exibirCpfCnpj($i['Destino']['cpf_cnpj']),@$_GET['sw'])?> - <?=highlight($i['Destino']['fantasia'],@$_GET['sw'])?><br/>
                <?=($i['Destino']['armazem']==1 ? '<div class="badge badge-default"><i class="fa fa-cubes"></i> Armazém</div>':'')?>
                <?=($i['Destino']['cliente']==1 ? '<div class="badge badge-default"><i class="fa fa-dollar"></i> Cliente</div>':'')?>
                <?=($i['Destino']['cia_aerea']==1 ? '<div class="badge badge-default"><i class="fa fa-paper-plane"></i> Cia Aérea</div>':'')?>
                <?=($i['Destino']['emitente_nfe']==1 ? '<div class="badge badge-default"><i class="fa fa-sticky-note"></i> NFe</div>':'')?>
                <?//=($i['Destino']['emitente_cte']==1 ? '<div class="badge badge-default"><i class="fa fa-sticky-note"></i> CTe</div>':'')?>
                <?=($i['Destino']['fornecedor']==1 ? '<div class="badge badge-default"><i class="fa fa-building"></i> Fornecedor</div>':'')?>
                <?=($i['Destino']['transportador']==1 ? '<div class="badge badge-default"><i class="fa fa-truck"></i> Transportadora</div>':'')?>
                <?=($i['Destino']['transportador_agente']==1 ? '<div class="badge badge-default"><i class="fa fa-automobile"></i> Recebedor/Agente</div>':'')?>
                <?=($i['Destino']['oficina']==1 ? '<div class="badge badge-default"><i class="fa fa-wrench"></i> Oficina</div>':'')?>
            </td>
            <td><?=highlight($i['Destino']['municipio'],@$_GET['sw'])?> - <?=highlight($i['Destino']['uf'],@$_GET['sw'])?></td>
            <td>
                <div class="btn-toolbar">
                    <?if ($i['Destino']['armazem']==1){?>
                    <div class="btn-group" role="group" aria-label="Alterar Registro">
                        <a href="/enderecamento/mapa/index/<?=$i['Destino']['id']?>" title="Áreas para Endereçamento" class="btn btn-default btn-xs"><i class="fa fa-cubes"></i></a>
                    </div>
                    <?}?>
                    <?if ($i['Destino']['transportador']==1 || $i['Destino']['transportador_agente']==1 || $i['Destino']['cia_aerea']==1){?>
                    <div class="btn-group" role="group" aria-label="Aplicação de Taxas">
                        <a class="btn btn-default btn-xs" title="Exibir Tabelas" href="/tms/custos/tabelas/<?=$i['Destino']['id']?>"><i class="fa fa-list"></i> Tabelas</a>
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Taxas <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <?php foreach ($taxas as $taxa_id => $taxa_nome){?>
                            <li><a href="/tms/custos/tabela_taxa/<?=$i['Destino']['id']?>/<?=$taxa_id?>" title="<?=$taxa_nome?>"><?=$taxa_nome?></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?}?>
                    <?if ($i['Destino']['cliente']==1){
                        ?>
<!-- Single button -->
<div class="btn-group">
  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Movimentação NF-es <span class="caret"></span>
  </button>
    <ul class="dropdown-menu">
                        <?
                        for ($dx=0; $dx<6; $dx++){
                            $dt = date("Y/m", time()-60*60*24*30*$dx);?>
        <li><a href="/movimento/nfe_valores/<?=$i['Destino']['id']?>/<?=$dt?>" title="Exportar NFs"><?=$dt?> <i class="fa fa-calendar"></i></a></li>
                    <?
                        }
                    ?>
  </ul>
</div>
                    <?
                    }?>
                    <div class="btn-group" role="group" aria-label="Alterar Registro">
                        <?if ($i['Destino']['armazem']==1 || $i['Destino']['cliente']==1){?>
                        <a class="btn btn-info btn-xs" href="javascript:void(abrePopUp('/cadastros/gerenciar/logo/<?=$i['Destino']['id']?>'));" title="Modificar Logo">Logotipo <i class="fa fa-picture-o"></i></a>
                        <?}?>
                        <a class="btn btn-warning btn-xs" href="javascript:void(abrePopUp('/cadastros/gerenciar/edit/<?=$i['Destino']['id']?>/<?=$marcar?>.html?mode=modal'));" title="Modificar Detalhes do Cadastro">Visualizar / Editar <i class="fa fa-edit"></i></a>
                    </div>
                </div>
            </td>
        </tr>
        <?php } ?>
    </table>
    <div class="panel-footer text-center">
        <nav>
            <ul class="pagination">
                <?=$this->Paginator->numbers(array(
                    'prev' => '< Anterior',
                    'tag' => 'li',
                    'currentTag' => 'a',
                    'currentClass' => 'active',
                    'separator' => '',
                    'next' => 'Próxima >',
                ))?>
            </ul>
        </nav>
    </div>
</div>