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
        Wypełnione ankiety
    </h1>
    <div class="page-content container-fluid">
        <div class="panel panel-bordered">
            <div class="panel-body">
                @if(!empty($pools))
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Lp</th>
                            <th>Data wypełnienia</th>
                            <th>Użytkownik</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1;?>
                        @foreach($pools as $pool)
                            <tr>
                                <td>
                                    {{$i}}
                                </td>
                                <td>
                                    {{$pool->created_at}}
                                </td>
                                <td>
                                    {{$pool->user->name}}
                                </td>
                                <td>
                                    <a href="{{route('suppliers.pools.filled.single', ['id' => $pool->supplier_id, 'poolId' => $pool->pool_id, 'userId' => $pool->user_id])}}" class="btn btn-sm btn-primary ">Przeglądnij wyniki</a>
                                </td>
                            </tr>
                            <?php $i++;?>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <h4>Brak wypełnionych ankiet</h4>
                @endif
                    <br />
                <a href="{{route('suppliers.pools', ['id' => $supplier_id])}}" class="btn btn-sm btn-primary btn-danger ">Powrót</a>
            </div>
        </div>
    </div>
@stop
