<!doctype html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <?php echo $this->Html->charset(); ?>
    <title>Unity - Embarcador</title>
    <?=$this->element('headers')?>
</head>
<body data-offset="50" data-target=".subnav" data-spy="scroll">
    <?=$this->element('TopoAcesso.menu-topo', array('embarcador'=>1));?>
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li><a href="/embarcador/encomendas">Embarcador</a></li>
            <?php
            if (!isset($crumbs)) $crumbs = [];
            foreach ($crumbs as $crumb){ ?>
            <li<?=($crumb['active'] ? ' class="active"':'')?>><a href="<?=$crumb['href']?>"><?=$crumb['name']?></a></li>
            <?php } ?>
        </ol>
        <?=$this->fetch('content')?>
    </div>
</body>
<?=$this->element('script_footer')?>
</html>