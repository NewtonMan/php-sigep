<?php $this->start('meta') ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart','table']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data1 = new google.visualization.DataTable();
        data1.addColumn('string', 'Estado');
        data1.addColumn('number', 'Performance');
        data1.addColumn('number', 'Entregas');
        data1.addColumn('number', 'Antes do Prazo');
        data1.addColumn('number', 'No Prazo');
        data1.addColumn('number', 'Fora Prazo');
        data1.addRows(<?=json_encode(($charts[0]))?>);
        var table1 = new google.visualization.Table(document.getElementById('chart1'));
        var formatter = new google.visualization.BarFormat({width: 80});
        var formatterVerde = new google.visualization.BarFormat({width: 80, colorPositive: 'green'});
        var formatterAzul = new google.visualization.BarFormat({width: 80, colorPositive: 'blue'});
        var formatterVermelho = new google.visualization.BarFormat({width: 80, colorPositive: 'red'});
        formatter.format(data1, 1);
        formatter.format(data1, 2);
        formatterVerde.format(data1, 3);
        formatterAzul.format(data1, 4);
        formatterVermelho.format(data1, 5);
        table1.draw(data1, {allowHtml: true, width: '100%', height: '100%'});
    }
</script>
<?php $this->end() ?>
<div class="row">
    <div class="col-md-12">
        <h1>Performance de Entregas</h1>
        <div class="progress">
            <div class="progress-bar" role="progressbar" aria-valuenow="<?=(int)$performanceTotal?>" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em; width: <?=$performanceTotal?>%;">
                <?=$performanceTotal?>%
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div id="chart1"></div>
    </div>
</div>
