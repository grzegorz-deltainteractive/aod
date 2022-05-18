<?php
/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 25/01/2022
 * Time: 23:28
 */

$supplierId = $dataTypeContent->id;
$supplier = \App\Models\Supplier::where('id', $supplierId)->first();
if ($supplier) {
    $supplierPools = \App\Models\Supplier::pools($supplier);
} else {
    $supplierPools = [];
}
//dd($supplierPools);
$suppliersPoolsResult = [];
if (!empty($supplierPools)) {
    $suppliersPoolsResult = \App\Models\SupplierPoolQuestion::getResults($supplierPools, $supplier);
//    dd($suppliersPoolsResult);
}
$departmens = \App\Models\Department::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
?>
@extends('voyager::master')

@section('page_title', 'Karta dostawcy: '.$supplier->name)

@section('page_header')
    <h1 class="page-title">
        <img src="/images/gray_dostawcy.png" alt="" class="header-icon-img" />  {{ ucfirst($dataType->getTranslatedAttribute('display_name_singular')) }} &nbsp;

        @can('edit', $dataTypeContent)
            <a href="{{ route('voyager.'.$dataType->slug.'.edit', $dataTypeContent->getKey()) }}" class="btn btn-info">
                <i class="glyphicon glyphicon-pencil"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.edit') }}</span>
            </a>
        @endcan
        @can('delete', $dataTypeContent)
            @if($isSoftDeleted)
                <a href="{{ route('voyager.'.$dataType->slug.'.restore', $dataTypeContent->getKey()) }}" title="{{ __('voyager::generic.restore') }}" class="btn btn-default restore" data-id="{{ $dataTypeContent->getKey() }}" id="restore-{{ $dataTypeContent->getKey() }}">
                    <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.restore') }}</span>
                </a>
            @else
                <a href="javascript:;" title="{{ __('voyager::generic.delete') }}" class="btn btn-danger delete" data-id="{{ $dataTypeContent->getKey() }}" id="delete-{{ $dataTypeContent->getKey() }}">
                    <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.delete') }}</span>
                </a>
            @endif
        @endcan
        @can('browse', $dataTypeContent)
            <a href="{{ route('voyager.'.$dataType->slug.'.index') }}" class="btn btn-warning">
                <i class="glyphicon glyphicon-list"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.return_to_list') }}</span>
            </a>
        @endcan
        <a href="{{route('suppliers.contact.add', ['id' => $dataTypeContent->getKey()])}}" class="btn btn-info">
            Dodaj kontakt
        </a>
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
<div class="page-content read container-fluid">
    <div class="panel panel-bordered">
        <div class="panel-body">
            <h3>Ankieta dostawcy: {{$supplier->name}}</h3>
            <div class="row">
                <div class="col-12 col-lg-8 ">
                    <fieldset>
                        <legend>
                            Dane adresowe i działy oceniające
                        </legend>
                        <div class="row">
                            <div class="col-12 col-md-4">
                                Ulica
                            </div>
                            <div class="col-12col-md-8">
                                {{$supplier->street ?? ''}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-4">
                                Kod pocztowy i miasto
                            </div>
                            <div class="col-12col-md-8">
                                {{$supplier->city ?? ''}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-4">
                                NIP
                            </div>
                            <div class="col-12col-md-8">
                                {{$supplier->nip ?? ''}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-4">
                                Działy
                            </div>
                            <div class="col-12col-md-8">
                                {{$supplier->departmentRelation->name ?? '' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-4">
                                Laboratorium
                            </div>
                            <div class="col-12col-md-8">
                                {{$supplier->laboratoryRelation->name ?? '' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-4">
                                Status
                            </div>
                            <div class="col-12col-md-8">
                                {{\App\Models\Supplier::getStatusName($supplier->status) }}
                            </div>
                        </div>
                    </fieldset>

                </div>
                <div class="col-12 col-lg-4">
                    <h4 class="center-text align-text-center">
                        Ocena
                    </h4>
                    <?php
                        $supplierData = \App\Models\Supplier::getSupplierPointsYear($supplier->id);
                    ?>
                    @if (empty($supplierData))
                        <h6>Brak ocen</h6>
                    @else
                        <table style="width: 100%;" class="table">
                            <thead>
                                <tr>
                                    <th>Rok</th>
                                    <th>Ocena</th>
                                    <th></th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($supplierData['points'] as $pointYear => $points)
                                <tr>
                                    <td>{{$pointYear}}</td>
                                    <td>
                                        {{$points}} / {{$supplierData['maxPoints']}}
                                    </td>
                                    <td>
                                        <?php
                                            $percent = $points / $supplierData['maxPoints'];
                                            $percent = $percent * 100;
                                            echo sprintf("%.2f", $percent).' %';
                                        ?>
                                    </td>
                                    <td> - </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
            <hr />
            @if(count($supplier->contacts) == 0)
                <h4>Brak dodanych kontaktów dla danego dostawcy. Kliknij na górze przycisk <strong>Dodaj kontakt</strong> aby dodać kontakt dla dostawcy</h4>
                <br />
            @else
                <div class="row">
                @foreach ($supplier->contacts as $sc)
                    <div class="col-12 col-lg-6">
                        <fieldset>
                            <legend>
                                Kontakt do działu <strong>{{$departmens[$sc->department_id]}}</strong>&nbsp;
                                <a href="{{route('suppliers.contact.remove', ['id' => $supplierId, 'contactId' => $sc->id])}}" class="btn btn-danger" onclick="return confirm('Czy na pewno usunąć kontakt?')"><i class="voyager-trash"></i> Usuń kontakt</a>
                            </legend>
                            <div class="row">
                                <div class="col-12 col-md-4 ">
                                    Mail:
                                </div>
                                <div class="col-12col-md-8">
                                    <p>{{$sc->email ?? ''}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-4 ">
                                    Telefon:
                                </div>
                                <div class="col-12col-md-8">
                                    <p>{{$sc->phone ?? ''}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-4 ">
                                    Imię i nazwisko:
                                </div>
                                <div class="col-12col-md-8">
                                    <p>{{$sc->name ?? ''}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-4 ">
                                    Stanowisko:
                                </div>
                                <div class="col-12col-md-8">
                                    <p>{{$sc->stanowisko ?? ''}}</p>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                @endforeach
                </div>
            @endif

            <div class="row">
                <div class="col-12 col-lg-12">
                    <fieldset>
                        <legend>Ankiety dostawcy</legend>
                        @if(count($supplierPools) == 0)
                            <h3>Brak ankiet</h3>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>
                                            Nazwa
                                        </th>
                                        <th>
                                            Numer procedury
                                        </th>
                                        <th>Rok</th>
                                        <th>
                                            Wynik #
                                        </th>
                                        <th>Wynik %</th>
                                        <th>Zmiana r/r</th>
                                        <th>Ocenione</th>
                                        <th>Zaakceptowane</th>
                                        <th>Liczba ankiet</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($supplierPools as $year=>$pools)
                                        @foreach($pools as $pool)
                                            @if (isset($suppliersPoolsResult['poolsSummary'][$pool->id]))
                                            <?php
                                            $poolData = \App\Models\SupplierPoolQuestion::calculatePoolResult($pool->id, $supplier);
                                            ?>
                                            @endif
                                            <tr>
                                                <td>
                                                    <a href="{{route('suppliers.listPools', ['id' => $pool->id, 'supplierId' => $supplier->id])}}">
                                                    {{$pool->name ?? ''}}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{$pool->numer_procedury}}
                                                </td>
                                                <td>{{$year}}</td>
                                                <td>
                                                    @if (isset($suppliersPoolsResult['poolsSummary'][$pool->id]))
                                                    {{$suppliersPoolsResult['poolsSummary'][$pool->id]['total'] .' / '.$suppliersPoolsResult['poolsSummary'][$pool->id]['max'] }}
                                                    @else
                                                    -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (isset($suppliersPoolsResult['poolsSummary'][$pool->id]))
                                                    {{sprintf("%.2f", ($suppliersPoolsResult['poolsSummary'][$pool->id]['total'] / $suppliersPoolsResult['poolsSummary'][$pool->id]['max'] )*100 ) ?? '-'}}%
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>-</td>
                                                <td>
                                                    @if (isset($suppliersPoolsResult['poolsSummary'][$pool->id]))
                                                    {{count($suppliersPoolsResult['poolsCount'][$pool->id]) ?? '-'}}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (isset($suppliersPoolsResult['poolsSummary'][$pool->id]))
                                                    {{count($suppliersPoolsResult['poolsCount'][$pool->id]) ?? '-'}}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (isset($suppliersPoolsResult['poolsSummary'][$pool->id]))
                                                    <a href="{{route('suppliers.displayPools', ['id' => $pool->id, 'supplierId' => $supplier->id])}}">
                                                    {{count($suppliersPoolsResult['poolsCount'][$pool->id])}}
                                                    </a>
                                                    @else
                                                    -
                                                    @endif
                                                </td>
                                            </tr>

                                        @endforeach
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
