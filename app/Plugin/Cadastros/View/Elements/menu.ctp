<ul class="nav nav-pills nav-stacked sidebar-nav">
    <li class="nav-header"><span>Gestão de Cadastros</span></li>
    <?if (!$modelUsuario->estaRestrito('Produto', 'index')):?><li><a href="/cadastros/produto">Produtos</a></li><?endif;?>
    <?if (!$modelUsuario->estaRestrito('Embalagem', 'index')):?><li><a href="/cadastros/embalagem">Tipos de Embalagem</a></li><?endif;?>
    <?if (!$modelUsuario->estaRestrito('FluxoLogistico', 'index')):?><li><a href="/cadastros/fluxo_logistico">Fluxo Logístico</a></li><?endif;?>
    <?if (!$modelUsuario->estaRestrito('Destinatario', 'index')):?><li><a href="/cadastros/gerenciar/index">Cadastro Geral de Empresas</a></li><?endif;?>
    <?if (!$modelUsuario->estaRestrito('Destinatario', 'clientes')):?><li><a href="/cadastros/gerenciar/cliente">Clientes</a></li><?endif;?>
    <?if (!$modelUsuario->estaRestrito('Destinatario', 'fornecedores')):?><li><a href="/cadastros/gerenciar/fornecedor">Fornecedores / Prestadores</a></li><?endif;?>
    <?if (!$modelUsuario->estaRestrito('Destinatario', 'transportadores')):?><li><a href="/cadastros/gerenciar/transportador">Transportadoras</a></li><?endif;?>
    <?if (!$modelUsuario->estaRestrito('Destinatario', 'transportadores')):?><li><a href="/cadastros/gerenciar/agentes">Recebedores / Agentes</a></li><?endif;?>
    <?if (!$modelUsuario->estaRestrito('Destinatario', 'oficinas')):?><li><a href="/cadastros/gerenciar/oficina">Oficinas / Autorizadas</a></li><?endif;?>
    <?if (!$modelUsuario->estaRestrito('Destinatario', 'cias_aereas')):?><li><a href="/cadastros/gerenciar/cia_aerea">Cias Aéreas</a></li><?endif;?>
    <?if (!$modelUsuario->estaRestrito('Destinatario', 'armazens')):?><li><a href="/cadastros/gerenciar/armazem">Armazéns</a></li><?endif;?>
    <?if (!$modelUsuario->estaRestrito('Usuario', 'index')):?><li><a href="/cadastros/usuario">Usuários</a></li><?endif;?>
</ul>
