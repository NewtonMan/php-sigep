<!DOCTYPE html>
<html>
    <head>
        <?php echo $this->Html->charset(); ?>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Gerenciamento de Aplicativo: <?php echo $this->fetch('title'); ?></title>
        <?= $this->element('AdminLTE.headers') ?>
    </head>
    <body class="hold-transition skin-blue">
        <div class="wrapper">
            <?=$this->element('AdminLTE.topo');?>
            <?=$this->element('AdminLTE.sidebar');?>
            <div class="content-wrapper">
                <?=$this->Session->flash();?>
                <section class="content">
                    <?=$this->fetch('content');?>
                </section>
                <?php echo $this->element('sql_dump'); ?>
            </div>
            <footer class="main-footer">
                <div class="pull-right hidden-xs">
                    <b>Version</b> 0.0.1
                </div>
                <strong>Copyright &copy; 2019 <a href="https://www.mkt-trade.com.br">MKT-Trade</a>.</strong> All rights reserved.
            </footer>
        </div>
        <?=$this->element('AdminLTE.scripts') ?>
    </body>
</html>
