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
        Wypełnij ankietę
    </h1>
    <div class="page-content container-fluid">
        <div class="panel panel-bordered">
            <div class="panel-body">
                @if (isset($messasge) && !empty($messasge))
                    <h5>{{$messasge}}</h5>
                @endif
                <form action="{{route('suppliers.pools.fill', ['id' => $supplier_id, 'poolId' => $pool->id])}}" method="post">
                    <?php echo e(csrf_field()); ?>
                    @foreach ($pool->categories as $category)
                        <h4>Kategoria: {{$category->name}}</h4>
                        <table class="table table-add-pool-values" style="width: 100%;">
                            <thead>
                            <tr>
                                <th style="width: 30%;">Nazwa</th>
                                <th style="width: 10%;">Min</th>
                                <th style="width: 10%;">Max</th>
                                <th style="width: 20%;">Ocena</th>
                                <th>Komentarz</th>
                            </tr>
                            </thead>
                            @foreach ($category->categoriesParameters as $parameter)
                                @if(checkDisplayField($parameter->visible_for_lab))
                                <tr>
                                    <td style="vertical-align: middle">{{$parameter->name}}</td>
                                    <td  style="vertical-align: middle">{{$parameter->rating_min}}</td>
                                    <td style="vertical-align: middle">{{$parameter->rating_max}}</td>
                                    <td style="vertical-align: middle">
                                        <input name="parameter[{{$category->id}}][{{$parameter->id}}]" type="number" min="{{$parameter->rating_min}}" max="{{$parameter->rating_max}}"
                                        value="{{$parameter->rating_min}}" class="form-control"/>
                                    </td>
                                    <td style="vertical-align: middle">
                                        <textarea name="parameter-notices[{{$category->id}}][{{$parameter->id}}]" class="form-control"></textarea>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </table>
                        <br /><br />
                    @endforeach
                    <button class="btn btn-sm btn-primary"  type="submit">Zapisz ankietę</button>
                    <br />
                    <a href="{{route('suppliers.pools', ['id' => $pool->id])}}" class="btn btn-sm btn-secondary " style="background-color: #cccccc">Powrót</a>
                </form>
            </div>
        </div>
    </div>
@stop
