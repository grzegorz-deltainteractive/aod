<?php
/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 26/01/2022
 * Time: 22:47
 */
?>
@extends('voyager::master')

@section('page_title', 'Karta dostawcy: '.$supplier->name.', pojedyńcza ankieta')

@section('page_header')
    <h1 class="page-title">
        <img src="/images/gray_ankiety.png" alt="" class="header-icon-img" /> Ankiety Dostawcy, pojedyńcza ankieta z {{date('Y', strtotime($pool->data_wydania_ankiety))}}, {{$pool->numer_procedury}}
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
                            Miasto i kod
                        </div>
                        <div class="col-12 col-lg-10">
                            {{$supplier->city ?? ''}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-2">
                            Ulica
                        </div>
                        <div class="col-12 col-lg-10">
                            {{$supplier->street ?? ''}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-2">
                            NIP
                        </div>
                        <div class="col-12 col-lg-10">
                            {{$supplier->nip ?? ''}}
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
                              <th>Wynik #</th>
                              <th>Wynik %</th>
                              <th>Uwagi</th>
                          </tr>
                          </thead>
                            <tbody>
                            <?php
                                $i = 1;

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
                                                $value1 = 0;
                                                foreach ($results['results'][$pool->id] as $cat) {
                                                    foreach ($cat as $paramId => $results1) {
                                                        if ($paramId == $parameter->id) {
                                                            foreach ($results1 as $userId=>$value) {
                                                                if ($userIdGlobal == $userId) {
                                                                    $value1 = $value;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                echo sprintf("%.2f", $value1);
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $value = ($value1 / $parameter->rating_max) * 100;
                                            echo sprintf("%.2f", $value);
                                            ?>
                                        </td>
                                        <td>
                                            {{\App\Models\SupplierPoolQuestion::getSingleNotice($parameter->id, $supplier->id, $pool->id, $userIdGlobal)}}
                                        </td>
                                    </tr>
                                    <?php $i++;?>
                                @endforeach
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                </fieldset>
                <br />
                <fieldset>
                    <a href="{{route('suppliers.listPools', ['id' => $pool->id, 'supplierId' => $supplier->id])}}" class="btn btn-sm btn-secondary " style="background-color: #cccccc">Powrót</a>
                </fieldset>
            </div>
        </div>
    </div>
@endsection
