<?$this->start('script');?>
<link href="/np_photo_gallery/css/npgallery.css" media="all" rel="stylesheet" />
<script src="/np_photo_gallery/js/jquery.npphotogallery.js"></script>
<?$this->end();?>
<?$this->start('script-onload');?>
$().jNpPhotoGallery();
<?$this->end();?>
<?php echo $this->Session->flash(); ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title"><?=$titulo?></h1>
    </div>
    <div class="panel-body">
        <form method="get" id="form-pesquisa">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-4">
                            <?=$this->Form->input('cliente_id', array('options'=>$clientes, 'label' => false, 'selected'=>@$_GET['data']['cliente_id']))?>
                        </div>
                        <div class="col-md-4">
                            <?=$this->Form->input('nome', array('type'=>'text', 'label' => false, 'placeholder' => 'Produto / Codigo', 'value'=>@$_GET['data']['nome']))?>
                        </div>
                        <div class="col-md-4">
                            <input type="submit" value="Pesquisar" class="btn btn-primary" />
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="btn-group">
                        <a href="/gerenciar_produtos/importar" class="btn btn-default">Importar Produtos</a>
                        <a href="/cadastros/produto/imprimir<?=(isset($_SERVER['REDIRECT_QUERY_STRING']) ? "?{$_SERVER['REDIRECT_QUERY_STRING']}":'')?>" class="btn btn-default">Imprimir</a>
                        <a href="/cadastros/produto/impostos_cliente" class="btn btn-warning" onclick="return confirm('ATENÇÃO: Não utilize esta ferramenta a menos que você tenha profundo conhecimento dos seus impactos, não nos responsabilizamos por danos causados pelo uso indiscriminado desta função.');">Impostos por Cliente</a>
                        <a href="/cadastros/produto/duplicidade" class="btn btn-warning" onclick="return confirm('ATENÇÃO: Não utilize esta ferramenta a menos que você tenha profundo conhecimento dos seus impactos, alterações realizadas aqui são irreversíveis e não nos responsabilizamos por danos causados pelo uso indiscriminado desta função.');">Duplicidades</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <table class="table table-condensed table-striped">
        <tr>
            <th colspan="2">
                <?=$this->Paginator->sort('Produto.id', 'ID');?> - 
                <?=$this->Paginator->sort('Produto.codigo', 'cProd');?> - 
                <?=$this->Paginator->sort('Produto.codigo_cliente', 'Cod. Cli.');?> - 
                <?=$this->Paginator->sort('Produto.codigo_barras', 'EAN');?>
            </th>
            <th>
                <?=$this->Paginator->sort('Produto.nome', 'Nome');?> - 
                <?=$this->Paginator->sort('Produto.descricao', 'Descrição');?>
            </th>
            <th>
                <?=$this->Paginator->sort('Produto.NCM', 'NCM');?> - 
                <?=$this->Paginator->sort('Produto.nf_EXTIPI', 'EXTIPI');?>
            </th>
            <th align="right">
                <a href="/cadastros/produto/add" class="btn btn-info">Novo Produto</a>
            </th>
        </tr>
        <?php foreach ($lista as $i){ ?>
        <tr>
            <td>
                <img src="<?=(isset($i['Photo'][0]['id']) ? "/np_photo_gallery/{$i['Photo'][0]['webpath']}/{$i['Photo'][0]['id']}_100.{$i['Photo'][0]['ext']}":'/no-image.png')?>"  class="img-responsive" />
            </td>
            <td>
                ID <?=$i['Produto']['id'];?> | 
                cProd <?=$i['Produto']['codigo'];?><br/>
                Cod. Cli. <?=$i['Produto']['codigo_cliente'];?><br/>
                EAN <?=$i['Produto']['codigo_barras'];?>
            </td>
            <td>
                <?=$i['Produto']['nome'];?>
                <?=$i['Produto']['descricao'];?>
            </td>
            <td>
                NCM <?=$i['Produto']['nf_NCM'];?><br/>
                EXTIPI <?=$i['Produto']['nf_EXTIPI'];?><br/>
                <div class="label label-<?=($i['Produto']['ativo']=='sim' ? 'success':'danger')?>">Ativo</div>
            </td>
            <td align="right">
                <a href="/print_server/etiquetas/caixa_master/<?=$i['Produto']['id']?>/1" class="btn btn-default bs-tooltip" title="Gerar Etiqueta Caixa Master">Cx. Master <i class="fa fa-barcode"></i></a>
                <a href="/cadastros/produto/geraCodigoBarras/<?=$i['Produto']['id']?>" class="btn btn-default bs-tooltip" title="Gerar imagem do Código de Barras"><i class="fa fa-barcode"></i></a>
                <a href="/np_photo_gallery/manage/photos/<?=$i['Produto']['id']?>" class="btn btn-default bs-tooltip" title="Galeria de Fotos do Produto"><i class="fa fa-images"></i></a>
                <a href="/etiquetas/dun14/index/<?=$i['Produto']['id']?>" class="btn btn-default bs-tooltip" title="Gerar Etiqueta DUN-14"><i class="fa fa-cube"></i></a>
                <a href="/cadastros/produto/edit/<?=$i['Produto']['id']?>" class="btn btn-warning bs-tooltip" title="Editar Detalhes do Produto"><i class="fa fa-edit"></i></a>
                <a href="/cadastros/produto/delete/<?=$i['Produto']['id']?>" class="btn btn-danger bs-tooltip" title="Tentar Excluir o Produto" onclick="return confirm('Deseja realmente excluir este Produto?');"><i class="fa fa-trash-alt"></i></a>
            </td>
        </tr>
        <?php
        }
        ?>
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
