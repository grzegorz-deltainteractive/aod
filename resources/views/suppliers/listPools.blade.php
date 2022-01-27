<?php
/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 26/01/2022
 * Time: 22:47
 */
?>
@extends('voyager::master')

@section('page_title', 'Lista ankiet dostawcy: '.$supplier->name)

@section('page_header')
    <h1 class="page-title">
        Lista ankiet dostawcy {{$supplier->name}}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content read container-fluid">
        <div class="panel panel-bordered">
            <div class="panel-body">

                <fieldset>
                    <legend>
                        Wyniki dział/laboratorum
                    </legend>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <td>Laboratorium/dział</td>
                                <td>Rok</td>
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
                                    <td>
                                        <a href="{{route('suppliers.singlePool', ['id' => $pool->id, 'supplierId' => $supplier->id, 'userId' => $user])}}">
                                        {{$ut[$user]}}
                                        </a>
                                    </td>
                                    <td>{{date('Y', strtotime($pool->data_wydania_ankiety))}}</td>
                                    <td><?php echo $sum .'/'.$sum1;?></td>
                                    <td><?php echo sprintf("%.2f", ($sum/$sum1)*100);?>%</td>
                                </tr>

                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </fieldset>
                <br />
                <fieldset>
                    <a href="{{url('/admin/suppliers/'.$supplier->id)}}" class="btn btn-sm btn-secondary " style="background-color: #cccccc">Powrót</a>
                </fieldset>
            </div>
        </div>
    </div>
@endsection
