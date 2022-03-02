<?php
/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 25/01/2022
 * Time: 23:28
 */

$poolId = $dataTypeContent->id;

$pool = App\Models\Pool::where('id', $poolId)->first();
$poolStatuses = App\Models\Pool::getStatuses();
$departmentsList = \App\Models\Department::getAllDepartmentsList();
$years = [];
foreach ($pool->suppliers as $supplier) {
    $year = \App\Models\SupplierPoolStatus::getPoolFilledYear($pool->id, $supplier->id);
    if (!empty($year)) {
        if (!in_array($year, $years)) {
            $years[] = $year;
        }
    }
}
?>
@extends('voyager::master')

@section('page_title', 'Definicja ankiety: '.$pool->numer_procedury);

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> {{'Definicja ankiety: '.$pool->numer_procedury}} &nbsp;
    </h1>
@stop

@section('content')
<div class="page-content browse container-fluid">
    <div class="panel panel-bordered">
        <div class="panel-body">
            <div class="row">
                <div class="col-12 text-right pull-right" style="padding-right: 15px;">
                    <a href="#"  class="export-buttons-table button-export-csv" title="Eksportuj do CSV">
                        <img src="/images/export-csv.png" alt="" />
                    </a>
                    <a href="#" class="export-buttons-table  button-export-xls" title="Eksportuj do XLS">
                        <img src="/images/export-xls.png" alt="" />
                    </a>
                    <a href="#" class="export-buttons-table  button-export-pdf" title="Exportuj do PDF">
                        <img src="/images/export-pdf.png" alt="" />
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-12 col-md-4 ">
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
                <div class="form-group col-12 col-md-4 ">
                    <label class="control-label">
                        Rok
                    </label>
                    <select name="dzial" id="search-rok" class="form-control">
                        <option value="">Wybierz rok</option>
                        @foreach ($years as $year)
                            <option value="{{$year}}">{{$year}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-4 ">
                    <label class="control-label">
                        Status
                    </label>
                    <select name="dzial" id="search-status" class="form-control">
                        <option value="">Wybierz status</option>
                        @foreach (\App\Models\Pool::getStatuses() as $listItem)
                            <option value="{{$listItem}}">{{$listItem}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @if(count($pool->suppliers) > 0)
            <div class="table-responsive">
                <table id="dataTable2" class="table table-hover dataTable2 no-footer">
                    <thead>
                        <tr>
                            <th>Nazwa ankiety</th>
                            <th>Dział</th>
                            <th>Laboratoria</th>
                            <th>Rok</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pool->suppliers as $supplier)
                            <?php
                            $year = \App\Models\SupplierPoolStatus::getPoolFilledYear($pool->id, $supplier->id);
                            ?>
                            <tr>
                                <td>
                                    @if(!empty($year) && $year != '')
                                        <a href="{{route('suppliers.listSupplierPools', ['supplierId' => $supplier->id])}}">
                                            {{trim($pool->numer_procedury .'_'.\App\Models\Supplier::getSupplierShortcode($supplier->id))}}
                                        </a>
                                    @else
                                        {{trim($pool->numer_procedury .'_'.\App\Models\Supplier::getSupplierShortcode($supplier->id))}}
                                    @endif
                                </td>
                                <td>
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
                                </td>
                                <td>
                                    <?php
                                    $laboratories = [];
                                    foreach ($supplier->laboratories as $laboratory) {
                                        $laboratories[] = $laboratory->name;
                                    }
                                    if (!empty($laboratories)) {
                                        echo implode(", ", $laboratories);
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    {{$year}}
                                </td>
                                <td>
                                    {{$poolStatuses[$pool->status]}}
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
                .column(4)
                .search(val)
                .draw();
        });
        $('.button-export-csv').click(function(e) {
            e.preventDefault();
            table2.button( '.buttons-csv' ).trigger();
        });
        $('.button-export-xls').click(function(e) {
            e.preventDefault();
            table2.button( '.buttons-excel' ).trigger();
        });
        $('.button-export-pdf').click(function(e) {
            e.preventDefault();
            table2.button( '.buttons-pdf' ).trigger();
        });
    });

</script>
@endsection
