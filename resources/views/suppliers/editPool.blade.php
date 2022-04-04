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
        <img src="/images/gray_ankiety.png" alt="" class="header-icon-img" />  Edycja ankiety
    </h1>
    <div class="page-content container-fluid">
        <div class="panel panel-bordered">
            <div class="panel-body">
                @if (isset($messasge) && !empty($messasge))
                    <h5>{{$messasge}}</h5>
                @endif
                <form action="{{route('suppliers.pools.edit', ['id' => $supplier_id, 'poolId' => $pool->id, 'userId' => $userId])}}" method="post">
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
                                        <?php
                                            $value = '';
                                            $notice = '';
                                            if (isset($parameters[$category->id][$parameter->id])) {
                                                $value = $parameters[$category->id][$parameter->id];
                                            }
                                            if (isset($notices[$category->id][$parameter->id])) {
                                                $notice = $notices[$category->id][$parameter->id];
                                            }
                                        ?>
                                        <input name="parameter[{{$category->id}}][{{$parameter->id}}]" type="number" min="{{$parameter->rating_min}}" max="{{$parameter->rating_max}}"
                                        value="{{$value}}" class="form-control"/>
                                    </td>
                                    <td style="vertical-align: middle">
                                        <textarea name="parameter-notices[{{$category->id}}][{{$parameter->id}}]" class="form-control">{{$notice}}</textarea>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </table>
                        <br /><br />
                    @endforeach
                        <p class="strong" style="font-weight: bold; color: red">PAMIĘTAJ! Po kliknięciu Zaktualizuj ankietę zostanie zapisany status że to Ty zmieniłeś tą ankietę.</p>
                    <button class="btn btn-sm btn-primary"  type="submit">Zaktualizuj ankietę</button>
                    <br />
                    <a href="javascript:history.back();" class="btn btn-sm btn-secondary " style="background-color: #cccccc">Powrót</a>
                </form>
            </div>
        </div>
    </div>
@stop
