<?php echo $this->Session->flash(); ?>
<div class="well">
    <strong>O que é isso?</strong>
    <p>Se o seu cliente desejar visualizar o estoque, consultar OS, ou até mesmo efetuar pedidos, nesta área você criará os usuários com permissão ao módulo de acesso aos dados destes clientes.</p>
    <p>Vale lembrar que, após criar um usuário, é necessário liberar os dados do mesmo, para tanto clique no botão "Liberar Clientes" e marque os clientes que o mesmo poderá consultar.</p>
    <p>Para o cliente efetuar o acesso você deve pedir que ele entre com os dados do usuário criado aqui no seguinte site:<br/><a href="http://<?=$_SERVER['HTTP_HOST']?>/acesso_cliente/">http://<?=$_SERVER['HTTP_HOST']?>/acesso_cliente/</a></p>
</div>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title"><?php echo __('Usuários de Clientes'); ?></h1>
    </div>
    <table class="table table-striped" width="100%">
        <tr>
            <th><?=$this->Paginator->sort('nome', 'Nome')?></th>
            <th><?=$this->Paginator->sort('email', 'E-mail')?></th>
            <th>
                <?=$this->Html->link('Criar Novo', array('controller' => 'acesso_cliente_usuario', 'action' => 'add'), array('escape' => false, 'title'=>'Criar', 'class'=>'btn btn-primary btn-xs'));?>
            </th>
        </tr>
        <?foreach ($usuarios as $item){?>
        <tr>
            <td><?=$item['AcessoClienteUsuario']['nome']?></td>
            <td><?=$item['AcessoClienteUsuario']['email']?></td>
            <td>
                <?=$this->Html->link('Sombrear', '/acesso_cliente/usuario/logar_como/'.$item['AcessoClienteUsuario']['id'], array('escape' => false, 'class'=>'btn btn-default btn-xs'));?>
                <?=$this->Html->link('Liberar Clientes', array('controller' => 'acesso_cliente_usuario', 'action' => 'clientes', $item['AcessoClienteUsuario']['id']), array('escape' => false, 'class'=>'btn btn-info btn-xs'));?>
                <?=$this->Html->link('Liberar Fluxos', array('controller' => 'acesso_cliente_usuario', 'action' => 'fluxos', $item['AcessoClienteUsuario']['id']), array('escape' => false, 'class'=>'btn btn-info btn-xs'));?>
                <?=$this->Html->link('Editar', array('controller' => 'acesso_cliente_usuario', 'action' => 'edit', $item['AcessoClienteUsuario']['id']), array('escape' => false, 'class'=>'btn btn-warning btn-xs'));?>
                <?=$this->Html->link('Excluir', array('controller' => 'acesso_cliente_usuario', 'action' => 'delete', $item['AcessoClienteUsuario']['id']), array('escape' => false, 'class'=>'btn btn-danger btn-xs'));?>
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