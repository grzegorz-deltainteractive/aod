<?php
/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 25/01/2022
 * Time: 23:28
 */

$pool = [];
$poolStatuses = App\Models\Pool::getStatuses();
$departmentsList = \App\Models\Department::getAllDepartmentsList();
$laboratoriesList = App\Models\Laboratory::getAllLaboratoriesList();
if ($supplier) {
    $supplierPools = \App\Models\Supplier::pools($supplier);
} else {
    $supplierPools = [];
}
$years = array_keys($supplierPools);
$suppliersPoolsResult = [];
if (!empty($supplierPools)) {
    $suppliersPoolsResult = \App\Models\SupplierPoolQuestion::getResults($supplierPools, $supplier);
//    dd($suppliersPoolsResult);
}
$statuses = [
    'Wypełniona, ale nie zaakceptowana',
    'Niewypełniona',
    'Zaakceptowano'
];

$poolsRelation = $supplier->poolsRelation;
$poolsList = [];
if (!empty($poolsRelation)) {
    foreach ($poolsRelation as $pool) {
        foreach($supplier->laboratories as $laboratory) {
            // tworze mixa ankieta_dostawca_laboratorium
            $name = $pool->numer_procedury.'_'.\App\Models\Supplier::getSupplierShortcode($supplier->id);
            $name .= '_'.\App\Models\Laboratory::getLaboratoryShortcode($laboratory->id);
            $filledData = \App\Models\SupplierPoolQuestion::getFilledDataAll($pool->id, $supplier->id);
            $users = \App\Models\Laboratory::getLaboratoryUsers($laboratory->id);
            if (count($users) == 1) {
                $poolsList[$name] = [
                    'name' => $name,
                    'pool' => $pool,
                    'laboratory' => $laboratory,
                    'user' => [
                        'id' => array_key_first($users),
                        'name' => reset($users)
                    ]
                ];
            } else {
                foreach ($users as $userId => $userName) {
                    $poolsList[$name.$userId] = [
                        'name' => $name,
                        'pool' => $pool,
                        'laboratory' => $laboratory,
                        'user' => [
                            'id' => $userId,
                            'name' => $userName
                        ]
                    ];
                }
            }
        }
    }
}
?>
@extends('voyager::master')

@section('page_title', 'Lista ankiet dostawcy: '.$supplier->name);

@section('page_header')
    <h1 class="page-title">
        <img src="/images/gray_ankiety.png" alt="" class="header-icon-img" />  {{'Lista ankiet dostawcy: '.$supplier->name}} &nbsp;
    </h1>
@stop

@section('content')
<div class="page-content browse container-fluid">
    <div class="panel panel-bordered">
        <div class="panel-body">
            <div class="row">
                <div class="form-group col-12 col-md-3 ">
                    <label class="control-label">
                        Dział
                    </label>
                    <select name="dzial" id="search-dzial" class="form-control">
                        <option value="">Wybierz dział</option>
                        @foreach ($departmentsList as $listItem)
                            <option value="{{$listItem}}">{{$listItem}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-3 ">
                    <label class="control-label">
                        Laboratorium
                    </label>
                    <select name="laboratorium" id="search-laboratorium" class="form-control" >
                        <option value="">Wybierz laboratorium</option>
                        @foreach ($laboratoriesList as $listItem)
                            <option value="{{$listItem}}">{{$listItem}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-3 ">
                    <label class="control-label">
                        Rok
                    </label>
                    <select name="dzial" id="search-rok" class="form-control">
                        <option value="">Wybierz rok</option>
                        @foreach ($years as $listItem)
                            <option value="{{$listItem}}">{{$listItem}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-3 ">
                    <label class="control-label">
                        Status
                    </label>
                    <select name="dzial" id="search-status" class="form-control">
                        <option value="">Wybierz status</option>
                        @foreach ($statuses as $listItem)
                            <option value="{{$listItem}}">{{$listItem}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @if(count($poolsList) > 0)
            <div class="table-responsive">
                <table id="dataTable2" class="table table-hover dataTable2 no-footer">
                    <thead>
                        <tr>
                            <th>Nazwa ankiety</th>
                            <th>Dział</th>
                            <th>Laboratoria</th>
                            <th>Użytkownik</th>
                            <th>Rok</th>
                            <th>Status</th>
                            <th>Wynik #</th>
                            <th>Wynik %</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($poolsList as $poolName => $poolData)
                        <?php
                        $year = \App\Models\SupplierPoolStatus::getPoolFilledUserYear($poolData['pool']->id, $supplier->id, $poolData['user']['id']);
//                        $filled = \App\Models\SupplierPoolQuestion::getFilledData($pool->id, $supplier->id);
                        $status = \App\Models\SupplierPoolStatus::getPoolFilledStatus($poolData['user']['id'], $poolData['pool']->id, $supplier->id );
                        $color = 'transparent';
                        $statusText = '';
                        $accepted = false;
                        $textColor = 'white';
                        if ($status == 'unfilled') {
                            $color = 'yellow';
                            $statusText = 'Niewypełniona';
                            $textColor = 'black';
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
                            <td>
                                @if(!empty($year) && $year != '-' && $status != 'unfilled')
                                    <a href="{{route('suppliers.pools.filled.single', ['id' => $supplier->id, 'poolId' => $poolData['pool']->id, 'userId' => $poolData['user']['id']])}}">
                                        {{$poolData['name']}}
                                    </a>
                                @else
                                {{$poolData['name']}}
                                @endif
                            </td>
                            <td>
                                <div>
                                    <?php
                                    $departments = [];
                                    foreach ($supplier->departments as $department) {
                                        $departments[] = $department->name;
                                    }
                                    if (!empty($departments)) {
                                        echo implode(", ", $departments);
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </div>
                            </td>
                            <td>
                                {{$poolData['laboratory']->name}}
                            </td>
                            <td>
                                {{$poolData['user']['name']}}
                            </td>
                            <td>
                                {{$year}}
                            </td>
                            <td style="background-color: {{$color}}; color: {{$textColor}};">
                                <div>
                                    {{$statusText}}
                                </div>
                            </td>
                            <td>

                            </td>
                            <td>

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <h3>Brak dodanych dostawców</h3>
            @endif
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script>
    $(document).ready(function () {
        var table2 = $('#dataTable2').DataTable({!! json_encode(
                    array_merge([
                        'buttons' => ['pageLength', 'pdfHtml5', 'excelHtml5', 'csvHtml5'],
                        'dom' => 'Bfrtip',
                        'lengthMenu' => [
                            [ 10, 25, 50, -1 ],
                            [ '10 wierszy', '25 wierszy', '50 wierszy', 'Pokaż wszystko' ]
                        ],
                        "language" => __('voyager::datatable'),
                        "columnDefs" => [
                            ['targets' => 'dt-not-orderable', 'searchable' =>  false, 'orderable' => false],
                        ],
                    ],
                    config('voyager.dashboard.data_tables', []))
                , true) !!});


        $('.select_all').on('click', function(e) {
            $('input[name="row_id"]').prop('checked', $(this).prop('checked')).trigger('change');
        });


        $('#search-dzial').change(function() {
            let val = $(this).val();
            table2
                .column(1)
                .search(val)
                .draw();
        });
        $('#search-laboratorium').change(function() {
            let val = $(this).val();
            table2
                .column(2)
                .search(val)
                .draw();
        })
        $('#search-rok').change(function() {
            let val = $(this).val();
            table2
                .column(4)
                .search(val)
                .draw();
        });
        $('#search-status').change(function() {
            let val = $(this).val();
            table2
                .column(5)
                .search(val)
                .draw();
        });
    });

</script>
@endsection
