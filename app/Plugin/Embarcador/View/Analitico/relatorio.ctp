<?php $this->start('meta') ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart','table']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data0 = google.visualization.arrayToDataTable(<?=json_encode($charts[0])?>);
        var options0 = {
            title: 'Encomendas por Transportadora',
            is3D: true
        };
        var chart0 = new google.visualization.PieChart(document.getElementById('chart0'));
        chart0.draw(data0, options0);
        
        var data1 = new google.visualization.DataTable();
        data1.addColumn('string', 'Transportadora');
        data1.addColumn('number', 'Encomendas');
        data1.addColumn('number', 'Finalizadas');
        data1.addColumn('number', 'Pendentes');
        data1.addColumn('number', 'Ocorrências');
        data1.addColumn('string', 'Relatórios');
        data1.addRows(<?=json_encode($charts[1])?>);
        var table1 = new google.visualization.Table(document.getElementById('chart1'));
        var formatter = new google.visualization.BarFormat({width: 80});
        var formatterFinalizadas = new google.visualization.BarFormat({width: 80, colorPositive: 'green'});
        var formatterPendentes = new google.visualization.BarFormat({width: 80, colorPositive: 'yellow'});
        var formatterOcorrencias = new google.visualization.BarFormat({width: 80, colorPositive: 'red'});
        formatter.format(data1, 1);
        formatterFinalizadas.format(data1, 2);
        formatterPendentes.format(data1, 3);
        formatterOcorrencias.format(data1, 4);
        table1.draw(data1, {allowHtml: true, width: '100%', height: '100%'});
    }
</script>
<?php $this->end() ?>
<div class="row">
    <div class="col-md-12">
        <div id="chart0" style="width: 100%; height: 500px;"></div>
        <div id="chart1"></div>
    </div>
</div>
