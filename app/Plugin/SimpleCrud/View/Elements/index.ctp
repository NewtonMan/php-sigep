<?=$this->Session->flash();?>
<form method="get" action="<?=(!empty($this->request->params['plugin']) ? "/{$this->request->params['plugin']}":'').(!empty($this->request->params['controller']) ? "/{$this->request->params['controller']}":'').(!empty($this->request->params['action']) ? "/{$this->request->params['action']}":'')?>">
<?php
$paramIgnore = ['page', 'crudSearch'];
foreach ($_GET as $param => $value) {
    if (in_array($param, $paramIgnore)) continue;
    echo "<input type=\"hidden\" name=\"{$param}\" value=\"{$value}\" />";
}
?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title"><?=$this->Paginator->counter($this->request->data['CRUD']['titulo'] . ': página {:page} de {:pages}, registros encontrados {:count}');?></h1>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-9">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Pesquisar por Palavra Chave..." name="crudSearch" value="<?=@$_GET['crudSearch']?>" autofocus>
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
                    </span>
                </div>
            </div>
            <div class="col-sm-3 text-right">
                <div class="btn-group">
                    <?php
                    if (isset($this->request->data['CRUD']['main_actions']) || isset($this->request->data['CRUD']['actions']['create']) || isset($this->request->data['CRUD']['actions']['export']) || isset($this->request->data['CRUD']['actions']['import'])){
                        ?>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Opções <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu  dropdown-menu-right">
                            <?php if (isset($this->request->data['CRUD']['actions']['create'])){?>
                            <li><a href="<?=$this->request->data['CRUD']['actions']['create']?>"><i class="fa fa-plus"></i> Novo Registro</a></li>
                            <?php } ?>
                            <?php if ((isset($this->request->data['CRUD']['actions']['create']) || isset($this->request->data['CRUD']['actions']['export']) || isset($this->request->data['CRUD']['actions']['import'])) && isset($this->request->data['CRUD']['main_actions'])){?>
                            <li role="separator" class="divider"></li>
                            <?php } ?>
                            <?php
                            if (is_array(@$this->request->data['CRUD']['main_actions'])){
                                foreach ($this->request->data['CRUD']['main_actions'] as $mainItem){
                                    $target = (isset($mainItem['target']) ? $mainItem['target']:'self');
                                    echo "<li><a href=\"{$mainItem['href']}\" target=\"{$target}\"><i class=\"{$mainItem['icon']}\"></i> {$mainItem['label']}</a></li>";
                                }
                            }
                            ?>
                        </ul>
                    </div>
                        <?php
                    }
                    ?>
                    <?php if (isset($this->request->data['CRUD']['actions']['export'])){?>
                    <a href="<?=$this->request->data['CRUD']['actions']['export']?>" class="btn btn-default"><i class="fa fa-download"></i> Exportar</a>
                    <?php } ?>
                    <?php if (isset($this->request->data['CRUD']['actions']['import'])){?>
                    <a href="<?=$this->request->data['CRUD']['actions']['import']?>" class="btn btn-default"><i class="fa fa-upload"></i> Importar</a>
                    <?php } ?>
                    
                </div>
            </div>
        </div>
    </div>
    <table class="table table-striped table-condensed table-bordered">
        <tr>
            <?php
            foreach ($this->request->data['CRUD']['cols'] as $cname => $cdata){
                $model = (isset($cdata['model']) ? $cdata['model']:$this->request->data['CRUD']['model']);
                $field = (isset($cdata['field']) ? $cdata['field']:$cname);
                $label = (isset($cdata['label']) ? $cdata['label']:Inflector::humanize($cname));
                echo '<th>'.$this->Paginator->sort("{$model}.{$field}", $label).'</th>';
            }
            if (isset($this->request->data['CRUD']['actions']['update']) || isset($this->request->data['CRUD']['actions']['delete'])){
                echo '<th>Ações</th>';
            }
            ?>
        </tr>
        <?php
        foreach ($this->request->data['CRUD']['data'] as $x => $i){
            echo '<tr>';
            foreach ($this->request->data['CRUD']['cols'] as $cname => $cdata){
                if (isset($cdata['callback'])){
                    echo "<td>{$cdata['callback']($i)}</td>";
                } else {
                    $prefix = (isset($cdata['prefix']) ? $cdata['prefix']:'');
                    $model = (isset($cdata['model']) ? $cdata['model']:$this->request->data['CRUD']['model']);
                    $field = (isset($cdata['field']) ? $cdata['field']:$cname);
                    $sufix = (isset($cdata['sufix']) ? $cdata['sufix']:'');
                    echo "<td>{$prefix}{$i[$model][$field]}{$sufix}</td>";
                }
            }
            echo "<td><div class=\"btn-group btn-group-xs\">";
            $model = $this->request->data['CRUD']['model'];
            if (isset($this->request->data['CRUD']['actions']['custom'])){
                foreach ($this->request->data['CRUD']['actions']['custom'] as $customItem){
                    if (isset($customItem['suffix'])){
                        $suffix = $i[$customItem['suffix']['model']][$customItem['suffix']['field']];
                    } else {
                        $suffix = $i[$model]['id'];
                    }
                    $target = (isset($customItem['target']) ? $customItem['target']:'self');
                    echo "<a href=\"{$customItem['href']}{$suffix}\" target=\"{$target}\" class=\"btn btn-{$customItem['btn']}\"><i class=\"{$customItem['icon']}\"></i> {$customItem['label']}</a>";
                }
            }
            $model = $this->request->data['CRUD']['model'];
            echo (isset($this->request->data['CRUD']['actions']['update']) ? "<a href=\"{$this->request->data['CRUD']['actions']['update']}{$i[$model]['id']}\" class=\"btn btn-warning\"><i class=\"fa fa-edit\"></i> Editar</a>":'')
                . (isset($this->request->data['CRUD']['actions']['delete']) ? "<a href=\"{$this->request->data['CRUD']['actions']['delete']}{$i[$model]['id']}\" class=\"btn btn-danger\" onclick=\"return confirm('Deseja realmente excluir este registro?');\"><i class=\"fa fa-trash\"></i> Deletar</a>":'')
                . "</div></td>";
            echo '</tr>';
        }
        ?>
    </table>
    <div class="panel-footer text-center">
        <?=$this->Paginator->numbers(['prev' => '< anterior', 'next' => 'próxima >']);?>
    </div>
</div>
</form>
