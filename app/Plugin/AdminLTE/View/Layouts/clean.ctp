<!DOCTYPE html>
<html>
    <head>
        <?php echo $this->Html->charset(); ?>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Gerenciamento de Aplicativo: <?php echo $this->fetch('title'); ?></title>
        <?= $this->element('AdminLTE.headers') ?>
    </head>
    <body class="skin-blue">
        <?=$this->fetch('content');?>
        <?= $this->element('AdminLTE.scripts') ?>
    </body>
</html>
