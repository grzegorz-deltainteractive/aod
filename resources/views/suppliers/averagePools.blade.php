<?php
/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 26/01/2022
 * Time: 22:47
 */
?>
@extends('voyager::master')

@section('page_title', 'Karta dostawcy: '.$supplier->name)

@section('page_header')
    <h1 class="page-title">
        Ankiety Dostawcy, średnia z {{date('Y', strtotime($pool->data_wydania_ankiety))}}, {{$pool->numer_procedury}}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content read container-fluid">
        <div class="panel panel-bordered">
            <div class="panel-body">
                <fieldset>
                    <legend>Ankiety Dostawcy, średnia z {{date('Y', strtotime($pool->data_wydania_ankiety))}}, {{$pool->numer_procedury}}</legend>
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
                            Kod ankiety
                        </div>
                        <div class="col-12 col-lg-10">
                            {{$pool->numer_procedury}}
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                          <tr>
                              <th>LP</th>
                              <th>Ocena</th>
                              <th>Wypełnia</th>
                              <th>Parametr</th>
                              <th>Min</th>
                              <th>Max</th>
                              <th>Średni wynik #</th>
                              <th>
                                  Mediana
                              </th>
                              <th>Średni wynik %</th>
                              <th>Wykres</th>
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
                                        <td>{{$i}}</td>
                                        <td>
                                            {{$category->name}}
                                        </td>
                                        <td>
                                            @if($parameter->visible_for_lab == 1)
                                                Laboratorium
                                            @else
                                                Biuro
                                            @endif
                                        </td>
                                        <td>
                                            {{$parameter->name}}
                                        </td>
                                        <td>
                                            {{$parameter->rating_min}}
                                        </td>
                                        <td>
                                            {{$parameter->rating_max}}
                                        </td>
                                        <td>
                                            <?php
                                                $value = ($results['resultsSummary'][$pool->id][$category->id][$parameter->id] / $results['resultsSummaryParam'][$pool->id][$category->id][$parameter->id]);
                                                $sum0 = $sum0 + $value;
                                                echo sprintf("%.2f", $value);
                                            ?>
                                        </td>
                                        <td>{{str_replace('.', ',', median($results['results'][$pool->id][$category->id][$parameter->id]))}}</td>
                                        <td>
                                            <?php
                                            $value = ($results['resultsSummary'][$pool->id][$category->id][$parameter->id] / $results['resultsSummaryParam'][$pool->id][$category->id][$parameter->id]) *100;
                                            $sum1 = $sum1 + $value;
                                            echo sprintf("%.2f", $value);
                                            ?>
                                        </td>
                                        <td>
                                            <a class="btn btn-small" href="{{route('suppliers.displayParameterDraw', ['id' => $pool->id, 'supplierId' => $supplier->id, 'parameterId' => $parameter->id])}}">
                                                >
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $i++;?>
                                @endforeach
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="6">
                                    Suma
                                </td>
                                <td>{{sprintf("%.2f", $sum0 / $i)}}</td>
                                <td></td>
                                <td>{{sprintf("%.2f", $sum1 / $i)}}%</td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>
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
                                <td>Wynik #</td>
                                <td>Wynik %</td>
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

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
@endsection
