<?php
$cakeDescription = __d('cake_dev', 'Cadastros');
?>
<!doctype html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <?php echo $this->Html->charset(); ?>
        <title><?php echo $cakeDescription ?>: <?php echo $title_for_layout; ?></title>
        <?=$this->element('headers')?>
    </head>
    <body data-spy="scroll" data-target=".subnav" data-offset="50">
        <?=$this->element('ga')?>
        <div id="equipamento" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3>Modal header</h3>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>
        <div id="container">
            <?=$this->element('TopoAcesso.menu-topo', array('cadastros' => 1));?>
            <table width="100%">
                <?= $this->element('topo_personalizado', array('menu' => $this->element('menu')), array('cache' => array('config' => 'default', 'key' => 'personalizado_' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']))); ?>
            </table>
        </div>
    </body>
    <?= $this->element('script_footer') ?>
</html>