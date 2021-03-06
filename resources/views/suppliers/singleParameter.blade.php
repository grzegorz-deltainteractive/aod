<?php
/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 26/01/2022
 * Time: 22:47
 */
?>
@extends('voyager::master')

@section('page_title', 'Pojedyńczy parametr dla dostawcy: '.$supplier->name)

@section('page_header')
    <h1 class="page-title">
        <img src="/images/gray_ankiety.png" alt="" class="header-icon-img" /> Ankiety Dostawcy, Wykres parametru {{$parameter->name}} {{date('Y', strtotime($pool->data_wydania_ankiety))}}, {{$pool->numer_procedury}}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop
<?php
    $toDrawData = [];
    for ($i = $parameter->rating_min; $i <= $parameter->rating_max; $i++) {
        $toDrawData[$i] = 0;
    }
?>
@section('content')
    <div class="page-content read container-fluid">
        <div class="panel panel-bordered">
            <div class="panel-body">
                <fieldset>
                    <legend>Ankiety Dostawcy, wykres parametru {{$parameter->name}}, średnia z {{date('Y', strtotime($pool->data_wydania_ankiety))}}, {{$pool->numer_procedury}}</legend>
                    <div class="row">
                        <div class="col-12 col-lg-2">
                            Nazwa dostawcy
                        </div>
                        <div class="col-12 col-lg-10">
                            {{$supplier->name}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-2">
                            Kod ankiety
                        </div>
                        <div class="col-12 col-lg-10">
                            {{$pool->numer_procedury}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-2">
                            Minimalna ocena
                        </div>
                        <div class="col-12 col-lg-10">
                            {{$parameter->rating_min}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-2">
                            Maksymalna ocena
                        </div>
                        <div class="col-12 col-lg-10">
                            {{$parameter->rating_max}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-2">
                            Liczba odpowiedzi
                        </div>
                        <div class="col-12 col-lg-10">
                            {{count($results['users'])}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-2">
                            Liczba laboratoriów
                        </div>
                        <div class="col-12 col-lg-10">
                            {{count($results['users'])}}
                        </div>
                    </div>

                </fieldset>
                <fieldset>
                    <legend>Wykres</legend>
                    <div style="min-height: 300px">
                        <canvas id="myChart2"></canvas>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>
                        Wyniki dział/laboratorum
                    </legend>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <td>Laboratorium/dział</td>
                                <td>Ocena</td>
                                <td>Uwagi</td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($results['users'] as $user)
                                <tr>
                                    <td>{{$ut[$user]}}</td>
                                    <td>
                                        <?php
                                        foreach ($results['results'][$pool->id] as $cat) {
                                            foreach ($cat as $paramId => $results1) {
                                                foreach ($results1 as $userId=>$value) {
                                                    if ($user == $userId && $paramId == $parameter->id) {
                                                        echo $value;
                                                        $toDrawData[$value] = $toDrawData[$value] + 1;
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        {{\App\Models\SupplierPoolQuestion::getSingleNotice($parameter->id, $supplier->id, $pool->id, $user)}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </fieldset>
                <br />
                <fieldset>
                    <a href="{{route('suppliers.displayPools', ['id' => $pool->id, 'supplierId' => $supplier->id])}}" class="btn btn-sm btn-secondary " style="background-color: #cccccc">Powrót</a>
                </fieldset>
            </div>
        </div>
    </div>
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
                    label: 'Liczba odpowiedzi',
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
@endsection
