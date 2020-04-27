<!DOCTYPE html>
<html>
    <head>
        <?php echo $this->Html->charset(); ?>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Gerenciamento de Aplicativo: <?php echo $this->fetch('title'); ?></title>
        <?= $this->element('AdminLTE.headers') ?>
        <!-- jQuery 3 -->
        <script src="/vendor/adminlte/bower_components/jquery/dist/jquery.min.js"></script>
        <!-- Bootstrap 3.3.7 -->
        <script src="/vendor/adminlte/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- iCheck -->
        <script src="/vendor/adminlte/plugins/iCheck/icheck.min.js"></script>
    </head>
    <body class="hold-transition login-page">
        <div class="container">
            <div class="col-lg-12">
                <?=$this->fetch('content')?>
            </div>
        </div>
    </body>
</html>