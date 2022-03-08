<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/css/pdf.css">
    <title>ALAB Laboratoria - Ankieta oceny dostawcy {{$supplier->name}}</title>
    <style>
        *{ font-family: DejaVu Sans !important; font-size: 12px;}

        .table-responsive table {
            border-collapse: collapse;
            width: 100%;
        }
        .table-responsive table tr th {
            border: 1px solid black;
            padding: 2px 5px;
            text-align: center;
        }
        .table-responsive table tr td {
            border: 1px solid black;
            padding: 2px 5px;
        }
        /*# sourceMappingURL=pdf.css.map */
    </style>
</head>
<body>

<table style="width: 100%;">
    <tr>
        <td style="width: 50%; font-weight: bold; font-size: 16px;">
            ALAB laboratoria Sp. z o.o.
        </td>
        <td style="text-align: right; font-weight: bold; font-size: 16px;">
            {{$pool->numer_procedury}}_{{date('Y', strtotime($pool->data_wydania_ankiety))}}_{{$supplier->skrot}}
        </td>
    </tr>
    <tr>
        <td colspan="2" style="border-bottom: 1px solid black;">
            ANKIETA OCENY DOSTAWCY USŁUG LABORATORYJNYCH, Średnia z {{date('Y', strtotime($pool->data_wydania_ankiety))}}
        </td>
    </tr>
</table>
<br />
<table>
    <tr>
        <td>Nazwa dostawcy: </td>
        <td>
            {{$supplier->name}}
        </td>
    </tr>
    <tr>
        <td>Kod ankiety:</td>
        <td>
            {{$pool->numer_procedury}}
        </td>
    </tr>
</table>
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
        <tr>
            <th>LP</th>
            <th>Ocena</th>
            <th>Parametr</th>
            <th>Punktacja</th>
            <th>Średni wynik #</th>
            <th>
                Mediana
            </th>
            <th>Średni wynik %</th>
            <th>Uwagi</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        $sum0 = 0;
        $sum1 = 0;
        ?>
        @foreach ($pool->categories as $category)
            @foreach ($category->categoriesParameters as $parameter)
                <tr>
                    <td style="text-align: center">{{$i}}</td>
                    <td>
                        {{$category->name}}
                    </td>
                    <td>
                        {{$parameter->name}}
                    </td>
                    <td style="text-align: center">
                        {{$parameter->rating_min}} -
                        {{$parameter->rating_max}}
                    </td>
                    <td style="text-align: center">
                        <?php
                        $value = ($results['resultsSummary'][$pool->id][$category->id][$parameter->id] / $results['resultsSummaryParam'][$pool->id][$category->id][$parameter->id]);
                        $sum0 = $sum0 + $value;
                        echo sprintf("%.2f", $value * $parameter->rating_max);
                        ?>
                    </td>
                    <td style="text-align: center">{{str_replace('.', ',', median($results['results'][$pool->id][$category->id][$parameter->id]))}}</td>
                    <td style="text-align: center">
                        <?php
                        $value = ($results['resultsSummary'][$pool->id][$category->id][$parameter->id] / $results['resultsSummaryParam'][$pool->id][$category->id][$parameter->id]) *100;
                        $sum1 = $sum1 + $value;
                        echo sprintf("%.2f", $value);
                        ?>
                    </td style="text-align: center">
                    <td>
                        &nbsp;
                    </td>
                </tr>
                <?php $i++;?>
            @endforeach
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="4">
                Suma
            </td>
            <td style="text-align: center">{{sprintf("%.2f", $sum0 / $i)}}</td>
            <td></td>
            <td style="text-align: center">{{sprintf("%.2f", $sum1 / $i)}}%</td>
            <td></td>
        </tr>
        </tfoot>
    </table>
</div>
<div class="table-responsive">
    <br /><br />
    <h3>Wyniki per laboratorium</h3>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>Laboratorium/dział</th>
            <th>Wynik #</th>
            <th>Wynik %</th>
        </tr>
        </thead>
        <tbody>
        @foreach($results['users'] as $user)
            <?php
            $sum = 0;
            $sum1 = 0;
            foreach ($results['results'][$pool->id] as $cat) {
                foreach ($cat as $paramId => $results1) {
                    foreach ($results1 as $userId=>$value) {
                        if ($user == $userId) {
                            $sum = $sum + $value;
                        }
                    }
                }
            }
            foreach ($results['resultsSummaryParam'][$pool->id] as $cat) {
                foreach ($cat as $paramId => $results1) {
                    $sum1 = $sum1 + $results1;
                }
            }
            $sum1 = (int)($sum1/count($results['users']));
            ?>
            <tr>
                <td>{{$ut[$user]}}</td>
                <td><?php echo $sum .'/'.$sum1;?></td>
                <td><?php echo sprintf("%.2f", ($sum/$sum1)*100);?>%</td>
            </tr>
            <?php
            $toDrawData[$ut[$user]] = $sum;
            ?>
        @endforeach
        </tbody>
    </table>
</div>
<br /><br />
<canvas id="myChart2"></canvas>
@if(isset($toDrawData) && !empty($toDrawData))
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="text/javascript">
        <?php
        $keys = array_keys($toDrawData);
        $keys = "'".implode("','", $keys)."'";
        $values = implode(",",array_values($toDrawData))
        ?>
        const drawLabels = [<?php echo $keys;?>];
        const drawData = {
            labels: drawLabels,
            datasets: [{
                label: 'Punkty',
                backgroundColor: '#22A7F0',
                borderColor: 'rgb(255, 99, 132)',
                data: [<?php echo $values;?>]
            }]
        };
        const drawConfig = {
            type: 'bar',
            data: drawData,
            options: {}
        };
        const myChart2 = new Chart(
            document.getElementById('myChart2'), drawConfig );
    </script>
@endif
</body>
</html>




