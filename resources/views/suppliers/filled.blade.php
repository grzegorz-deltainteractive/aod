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
        <img src="/images/gray_ankiety.png" alt="" class="header-icon-img" />  Wypełnione ankiety
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
                            <th>Status</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1;?>
                        @foreach($pools as $pool)
                            <?php
                                $status = \App\Models\SupplierPoolStatus::getPoolFilledStatus($pool->user->id, $pool_id, $supplier_id );
                                $color = 'transparent';
                                $statusText = '';
                                $accepted = false;
                                if ($status == 'unfilled') {
                                    $color = 'yellow';
                                    $statusText = 'Niewypełniona';
                                } else if ($status == 'unaceppted') {
                                    $color = 'red';
                                    $statusText = 'Wypełniona, ale nie zaakceptowana';
                                } else {
                                    $color = 'green';
                                    $statusText = $status;
                                    $accepted = true;
                                }
                            ?>
                            <tr>
                                <td style="vertical-align: middle;">
                                    {{$i}}
                                </td>
                                <td style="vertical-align: middle;">
                                    {{$pool->created_at}}
                                </td>
                                <td style="vertical-align: middle;">
                                    {{$pool->user->name}}
                                </td>
                                <td style="background-color: {{$color}}; color: black; text-align: center; vertical-align: middle">
                                    {{$statusText}}
                                </td>
                                <td>
                                    <a href="{{route('suppliers.pools.filled.single', ['id' => $pool->supplier_id, 'poolId' => $pool->pool_id, 'userId' => $pool->user_id])}}" class="btn btn-sm btn-primary ">Przeglądnij wyniki</a>
                                    @if(canAcceptPool() && !$accepted)
                                        <a href="{{route('suppliers.pools.accept', ['id' => $pool->supplier_id, 'poolId' => $pool->pool_id, 'userId' => $pool->user_id])}}" class="btn btn-sm btn-primary " onclick="return confirm('Czy chcesz zaakceptować ankietę? Zostanie zapisany status z datą i Twoim użytkownikiem jako użytkownik akceptujący daną ankietę.')">Akceptuj ankietę</a>
                                    @endif
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
                <a href="{{route('suppliers.pools', ['id' => $supplier_id])}}" class="btn btn-sm btn-secondary" style="background-color: #cccccc">Powrót</a>
            </div>
        </div>
    </div>
@stop
