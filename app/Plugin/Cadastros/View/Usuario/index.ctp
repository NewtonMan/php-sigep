<?php echo $this->Session->flash(); ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title"><?php echo __('Usuários'); ?></h1>
    </div>
    <table class="table table-striped">
        <tr>
            <th><?=$this->Paginator->sort('Cliente.fantasia', 'Cliente')?></th>
            <th><?=$this->Paginator->sort('nome', 'Nome')?></th>
            <th><?=$this->Paginator->sort('email', 'E-mail')?></th>
            <th>
                <?=$this->Html->link('Criar Novo', array('controller' => 'usuario', 'action' => 'add'), array('escape' => false, 'title'=>'Criar', 'class'=>'btn btn-primary btn-xs'));?>
            </th>
        </tr>
        <?foreach ($usuarios as $item){?>
        <tr>
            <td<?=(empty($item['Usuario']['ativo']) ? " style=\"color: red;\"":'')?>><?=(empty($item['Usuario']['acesso_cliente_id']) ? 'COLABORADOR':$item['Cliente']['fantasia'])?></td>
            <td<?=(empty($item['Usuario']['ativo']) ? " style=\"color: red;\"":'')?>><?=$item['Usuario']['nome']?></td>
            <td<?=(empty($item['Usuario']['ativo']) ? " style=\"color: red;\"":'')?>><?=$item['Usuario']['email']?></td>
            <td>
                <?=$this->Html->link('Sombrear', array('controller' => 'usuario', 'action' => 'logar_como', $item['Usuario']['id']), array('escape' => false, 'class'=>'btn btn-default btn-xs'));?>
                <?php if (empty($item['Usuario']['acesso_cliente_id'])){ ?>
                <?=$this->Html->link('Restrições', array('controller' => 'usuario_permissao', 'action' => 'index', $item['Usuario']['id']), array('escape' => false, 'class'=>'btn btn-default btn-xs'));?>
                <?=$this->Html->link('Clientes', array('controller' => 'usuario', 'action' => 'clientes', $item['Usuario']['id']), array('escape' => false, 'class'=>'btn btn-info btn-xs'));?>
                <?php } ?>
                <?=$this->Html->link('Editar', array('controller' => 'usuario', 'action' => 'edit', $item['Usuario']['id']), array('escape' => false, 'class'=>'btn btn-warning btn-xs'));?>
                <?=$this->Html->link('Excluir', array('controller' => 'usuario', 'action' => 'delete', $item['Usuario']['id']), array('escape' => false, 'class'=>'btn btn-danger btn-xs'));?>
            </td>
        </tr>
        <?}?>
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
