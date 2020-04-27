<div class="panel panel-default">
    <div class="panel-heading">
        <h1 class="panel-title">Operação 12 Meses</h1>
    </div>
    <table class="table table-striped table-condensed">
        <tr>
            <th>Data Inicio / Data Final</th>
            <th>Encomendas (média)</th>
            <th>Finalizadas</th>
            <th>Pendentes</th>
            <th>Ocorrências</th>
            <th>#</th>
        </tr>
        <?php foreach ($periodos as $datas => $encomendas) {
            if ($encomendas['total']==0) continue;
            $etotal = (100 - ((100 / $media['total']) * $encomendas['total'])) * -1;
            $finalizados = (((100 / $encomendas['total']) * $encomendas['finalizadas']));
            $pendentes = (((100 / $encomendas['total']) * $encomendas['pendentes']));
            $ocorrencias = (((100 / $encomendas['total']) * $encomendas['ocorrencias']));
            
            // etotal Color
            if ($etotal>5){
                $etotalColor = 'success';
            } elseif ($etotal>-5){
                $etotalColor = 'info';
            } elseif ($etotal>-15){
                $etotalColor = 'warning';
            } else {
                $etotalColor = 'danger';
            }
            
            // finalizados Color
            if ($finalizados>95){
                $finalizadosColor = 'success';
            } elseif ($finalizados>85){
                $finalizadosColor = 'info';
            } elseif ($finalizados>50){
                $finalizadosColor = 'warning';
            } else {
                $finalizadosColor = 'danger';
            }
            
            // pendentes Color
            if ($pendentes<5){
                $pendentesColor = 'success';
            } elseif ($pendentes<15){
                $pendentesColor = 'info';
            } elseif ($pendentes<30){
                $pendentesColor = 'warning';
            } else {
                $pendentesColor = 'danger';
            }
            
            // ocorrencias Color
            $ocorrenciasColor = 'primary';
            if ($ocorrencias>0 && $ocorrencias<=15){
                $ocorrenciasColor = 'warning';
            } elseif ($ocorrencias>15){
                $ocorrenciasColor = 'danger';
            } elseif ($ocorrencias<0 && $ocorrencias>=-15){
                $ocorrenciasColor = 'info';
            } elseif ($ocorrencias<-15){
                $ocorrenciasColor = 'success';
            }
            ?>
            <tr>
                <td><?=str_replace('|', ' até ', $datas)?></td>
                <td class="bg-<?=$etotalColor?>"><?=$encomendas['total']?> (<?=number_format($etotal, 1, ',', '.')?>%)</td>
                <td class="bg-<?=$finalizadosColor?>"><?=$encomendas['finalizadas']?> (<?=number_format($finalizados, 1, ',', '.')?>%)</td>
                <td class="bg-<?=$pendentesColor?>"><?=$encomendas['pendentes']?> (<?=number_format($pendentes, 1, ',', '.')?>%)</td>
                <td class="bg-<?=$ocorrenciasColor?>"><?=$encomendas['ocorrencias']?> (<?=number_format($ocorrencias, 1, ',', '.')?>%)</td>
                <td>
                    <a class="btn btn-primary btn-lg" href="/embarcador/analitico/relatorio/<?= $cliente_id ?>/<?=str_replace('|', '/', $datas)?>">Analisar Período</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
