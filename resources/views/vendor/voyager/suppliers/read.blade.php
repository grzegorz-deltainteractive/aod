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
?>
@extends('voyager::master')

@section('page_title', 'Karta dostawcy: '.$supplier->name)

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> {{ __('voyager::generic.viewing') }} {{ ucfirst($dataType->getTranslatedAttribute('display_name_singular')) }} &nbsp;

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
                            <div class="col-12 col-lg-5">
                                Ulica
                            </div>
                            <div class="col-12 col-lg-7">
                                {{$supplier->street ?? ''}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-5">
                                Kod pocztowy i miasto
                            </div>
                            <div class="col-12 col-lg-7">
                                {{$supplier->city ?? ''}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-5">
                                NIP
                            </div>
                            <div class="col-12 col-lg-7">
                                {{$supplier->nip ?? ''}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-5">
                                Działy
                            </div>
                            <div class="col-12 col-lg-7">
                                {{$supplier->departmentRelation->name ?? '' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-5">
                                Laboratorium
                            </div>
                            <div class="col-12 col-lg-7">
                                {{$supplier->laboratoryRelation->name ?? '' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-5">
                                Status
                            </div>
                            <div class="col-12 col-lg-7">
                                {{\App\Models\Supplier::getStatusName($supplier->status) }}
                            </div>
                        </div>
                    </fieldset>

                </div>
                <div class="col-12 col-lg-4">
                    <h4 class="center-text align-text-center">
                        Ankiety
                    </h4>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-12 col-lg-6">
                    <fieldset>
                        <legend>
                            Kontakt działu IT
                        </legend>
                        <div class="row">
                            <div class="col-12 col-lg-5 ">
                                Mail:
                            </div>
                            <div class="col-12 col-lg-7">
                                <p>{{$supplier->contact_it_mail ?? ''}}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-5 ">
                                Telefon:
                            </div>
                            <div class="col-12 col-lg-7">
                                <p>{{$supplier->contact_it_phone ?? ''}}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-5 ">
                                Imię i nazwisko:
                            </div>
                            <div class="col-12 col-lg-7">
                                <p>{{$supplier->contact_it_name ?? ''}}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-5 ">
                                Stanowisko:
                            </div>
                            <div class="col-12 col-lg-7">
                                <p>{{$supplier->contact_it_stanowisko ?? ''}}</p>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="col-12 col-lg-6">
                    <fieldset>
                        <legend>
                            Kontakt działu medycznego
                        </legend>
                        <div class="row">
                            <div class="col-12 col-lg-5 ">
                                Mail:
                            </div>
                            <div class="col-12 col-lg-7">
                                <p>{{$supplier->contact_medical_mail ?? ''}}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-5 ">
                                Telefon:
                            </div>
                            <div class="col-12 col-lg-7">
                                <p>{{$supplier->contact_medical_phone ?? ''}}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-5 ">
                                Imię i nazwisko:
                            </div>
                            <div class="col-12 col-lg-7">
                                <p>{{$supplier->contact_medical_name ?? ''}}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-5">
                                Stanowisko:
                            </div>
                            <div class="col-12 col-lg-7">
                                <p>{{$supplier->contact_medical_stanowisko ?? ''}}</p>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
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
                                            <tr>
                                                <td>{{$pool->name ?? ''}}</td>
                                                <td>{{$year}}</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>1</td>
                                                <td>1</td>
                                                <td>1</td>
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
