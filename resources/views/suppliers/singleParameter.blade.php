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
        Ankiety Dostawcy, Wykres parametru {{$parameter->name}} {{date('Y', strtotime($pool->data_wydania_ankiety))}}, {{$pool->numer_procedury}}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

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
                    <div style="min-height: 300px"></div>
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
            </div>
        </div>
    </div>
@endsection
