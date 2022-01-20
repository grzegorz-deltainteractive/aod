<?php
/**
 * Created by Grzegorz Możdżeń
 * Date: 19/01/2022
 * Time: 01:33
 */
?>
@extends('voyager::master')
@section('content')
    <h1 class="page-title">
        Ankieta "{{$pool->name}}" - wyniki
    </h1>
    <div class="page-content container-fluid">
        <div class="panel panel-bordered">
            <div class="panel-body">
                @foreach($pool->categories as $category)
                    <h4>Kategoria: <strong>{{$category->name}}</strong></h4>
                    <table class="table" style="width: 100%;">
                        <thead>
                        <tr>
                            <th style="width: 30%;">Parametr</th>
                            <th style="width: 10%;">Min</th>
                            <th style="width: 10%;">Max</th>
                            <th style="width: 20%;">Odpowiedź</th>
                            <th>Uwagi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($category->categoriesParameters as $sp)
                            <tr>
                                <td>
                                    {{$sp->name}}
                                </td>
                                <td>
                                    {{$sp->rating_min}}
                                </td>
                                <td>
                                    {{$sp->rating_max}}
                                </td>
                                <td>
                                    {{\App\Models\SupplierPoolQuestion::getValue($sp->id, $data, $supplier_id, $pool_id, $user_id)}}
                                </td>
                                <td>
                                    {{\App\Models\SupplierPoolQuestion::getNotices($sp->id, $data, $supplier_id, $pool_id, $user_id)}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <br />
                @endforeach
                <br />
                <a href="{{route('suppliers.pools.filled', ['id' => $supplier_id, 'poolId' => $pool_id])}}" class="btn btn-sm btn-secondary " style="background-color: #cccccc">Powrót</a>
            </div>
        </div>
    </div>
@stop
