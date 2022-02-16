@extends('voyager::master')

@section('page_title', __('voyager::generic.view').' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> {{ __('voyager::generic.viewing') }} {{ ucfirst($dataType->getTranslatedAttribute('display_name_singular')) }}
        &nbsp;

        @can('edit', $dataTypeContent)
            <a href="{{ route('voyager.'.$dataType->slug.'.edit', $dataTypeContent->getKey()) }}" class="btn btn-info">
                <i class="glyphicon glyphicon-pencil"></i> <span
                    class="hidden-xs hidden-sm">{{ __('voyager::generic.edit') }}</span>
            </a>
        @endcan

        @can('browse', $dataTypeContent)
            <a href="{{ route('voyager.'.$dataType->slug.'.index') }}" class="btn btn-warning">
                <i class="glyphicon glyphicon-list"></i> <span
                    class="hidden-xs hidden-sm">{{ __('voyager::generic.return_to_list') }}</span>
            </a>
        @endcan
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content read container-fluid">
        <?php
        $laboratoriumId = $dataTypeContent->id;
        $laboratorium = \App\Models\Department::where('id', $laboratoriumId)->first();
        ?>


        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered" style="padding:15px;">
                    <!-- form start -->
                    <h3>{{$laboratorium->name}}</h3><br/>
                    <div class="row">
                        <div class="col-12 col-lg-8 ">
                            @foreach($dataType->readRows as $row)
                                @php
                                    if ($dataTypeContent->{$row->field.'_read'}) {
                                        $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_read'};
                                    }
                                @endphp
                                <div class="row">
                                    <div class="col-12 col-lg-5">
                                        {{ $row->getTranslatedAttribute('display_name') }}
                                    </div>

                                    <div class="col-12 col-lg-7">
                                        @if (isset($row->details->view))
                                            @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => 'read', 'view' => 'read', 'options' => $row->details])
                                        @elseif($row->type == "image")
                                            <img class="img-responsive"
                                                 src="{{ filter_var($dataTypeContent->{$row->field}, FILTER_VALIDATE_URL) ? $dataTypeContent->{$row->field} : Voyager::image($dataTypeContent->{$row->field}) }}">
                                        @elseif($row->type == 'multiple_images')
                                            @if(json_decode($dataTypeContent->{$row->field}))
                                                @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
                                                    <img class="img-responsive"
                                                         src="{{ filter_var($file, FILTER_VALIDATE_URL) ? $file : Voyager::image($file) }}">
                                                @endforeach
                                            @else
                                                <img class="img-responsive"
                                                     src="{{ filter_var($dataTypeContent->{$row->field}, FILTER_VALIDATE_URL) ? $dataTypeContent->{$row->field} : Voyager::image($dataTypeContent->{$row->field}) }}">
                                            @endif
                                        @elseif($row->type == 'relationship')
                                            @include('voyager::formfields.relationship', ['view' => 'read', 'options' => $row->details])
                                        @elseif($row->type == 'select_dropdown' && property_exists($row->details, 'options') &&
                                                !empty($row->details->options->{$dataTypeContent->{$row->field}})
                                        )
                                            <?php echo $row->details->options->{$dataTypeContent->{$row->field}};?>
                                        @elseif($row->type == 'select_multiple')
                                            @if(property_exists($row->details, 'relationship'))

                                                @foreach(json_decode($dataTypeContent->{$row->field}) as $item)
                                                    {{ $item->{$row->field}  }}
                                                @endforeach

                                            @elseif(property_exists($row->details, 'options'))
                                                @if (!empty(json_decode($dataTypeContent->{$row->field})))
                                                    @foreach(json_decode($dataTypeContent->{$row->field}) as $item)
                                                        @if (@$row->details->options->{$item})
                                                            {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                                        @endif
                                                    @endforeach
                                                @else
                                                    {{ __('voyager::generic.none') }}
                                                @endif
                                            @endif
                                        @elseif($row->type == 'date' || $row->type == 'timestamp')
                                            @if ( property_exists($row->details, 'format') && !is_null($dataTypeContent->{$row->field}) )
                                                {{ \Carbon\Carbon::parse($dataTypeContent->{$row->field})->formatLocalized($row->details->format) }}
                                            @else
                                                {{ $dataTypeContent->{$row->field} }}
                                            @endif
                                        @elseif($row->type == 'checkbox')
                                            @if(property_exists($row->details, 'on') && property_exists($row->details, 'off'))
                                                @if($dataTypeContent->{$row->field})
                                                    <span class="label label-info">{{ $row->details->on }}</span>
                                                @else
                                                    <span class="label label-primary">{{ $row->details->off }}</span>
                                                @endif
                                            @else
                                                {{ $dataTypeContent->{$row->field} }}
                                            @endif
                                        @elseif($row->type == 'color')
                                            <span class="badge badge-lg"
                                                  style="background-color: {{ $dataTypeContent->{$row->field} }}">{{ $dataTypeContent->{$row->field} }}</span>
                                        @elseif($row->type == 'coordinates')
                                            @include('voyager::partials.coordinates')
                                        @elseif($row->type == 'rich_text_box')
                                            @include('voyager::multilingual.input-hidden-bread-read')
                                            {!! $dataTypeContent->{$row->field} !!}
                                        @elseif($row->type == 'file')
                                            @if(json_decode($dataTypeContent->{$row->field}))
                                                @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
                                                    <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}">
                                                        {{ $file->original_name ?: '' }}
                                                    </a>
                                                    <br/>
                                                @endforeach
                                            @else
                                                <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($row->field) ?: '' }}">
                                                    {{ __('voyager::generic.download') }}
                                                </a>
                                            @endif
                                        @else
                                            @include('voyager::multilingual.input-hidden-bread-read')
                                            <p>{{ $dataTypeContent->{$row->field} }}</p>
                                        @endif
                                    </div>
                                </div><!-- panel-body -->
                                @if(!$loop->last)

                                @endif
                            @endforeach
                        </div>
                    </div>
                    <h4>Ankiety dla działu</h4>
                    <?php $pools = \App\Models\Pool::getPoolsByDepartment($dataTypeContent->id);?>
                    @if (empty($pools))
                        <h5>Brak ankiet</h5>
                    @else
                        <div class="row">
                            <div class="col-12 col-lg-12">
                                <table class="table table-ordered">
                                    <thead>
                                        <tr>
                                            <th>Nazwa</th>
                                            <th>Numer procedury</th>
                                            <th>Dostawca</th>
                                            <th>Rok</th>
                                            <th>Wynik #</th>
                                            <th>Wynik %</th>
                                            <th>Zmiana r/r</th>
                                            <th>Ocenione</th>
                                            <th>Zaakceptowane</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($pools as $pool)
                                        @foreach ($pool->suppliers as $supplier)
                                            <tr>
                                                <td style="vertical-align: middle">
                                                    <a href="{{route('suppliers.displayPools', ['id' => $pool->id, 'supplierId' => $supplier->id])}}">
                                                    {{$pool->name}}
                                                    </a>
                                                </td>
                                                <td style="vertical-align: middle">
                                                    {{trim($pool->numer_procedury .'_'.\App\Models\Supplier::getSupplierShortcode($supplier->id))}}
                                                </td>
                                                <td style="vertical-align: middle">
                                                    {{$supplier->name}}
                                                </td>
                                                <td style="vertical-align: middle">
                                                    {{date('Y', strtotime($pool->data_wydania_ankiety))}}
                                                </td>
                                                <td style="vertical-align: middle">
                                                    33
                                                </td>
                                                <td style="vertical-align: middle">
                                                    33
                                                </td>
                                                <td style="vertical-align: middle">
                                                    +2%
                                                </td>
                                                <td style="vertical-align: middle">
                                                    1/3
                                                </td>
                                                <td style="vertical-align: middle">
                                                    2/3
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                    <h4>Dostawcy</h4>
                    @if (count($laboratorium->suppliers) == 0)
                        <h5>Brak dostawców</h5>
                    @else
                    <table class="table table-ordered">
                        <thead>
                        <tr>
                            <th>Nazwa</th>
                            <th>Miasto</th>
                            <th>Ocena {{$laboratorium->name}}</th>
                            <th>Ocena ogólna</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($laboratorium->suppliers as $supplier)
                            <tr>
                                <td style="vertical-align: middle">
                                    <a href="{{url('/admin/suppliers/'.$supplier->id)}}">
                                    {{$supplier->name}}
                                    </a>
                                </td>
                                <td style="vertical-align: middle">
                                    {{$supplier->city}}
                                </td>
                                <td style="vertical-align: middle">
                                    99%
                                </td>
                                <td style="vertical-align: middle">
                                    97%
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Single delete modal --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><i
                            class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} {{ strtolower($dataType->getTranslatedAttribute('display_name_singular')) }}
                        ?</h4>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('voyager.'.$dataType->slug.'.index') }}" id="delete_form" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm"
                               value="{{ __('voyager::generic.delete_confirm') }} {{ strtolower($dataType->getTranslatedAttribute('display_name_singular')) }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right"
                            data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop

@section('javascript')
    @if ($isModelTranslatable)
        <script>
            $(document).ready(function () {
                $('.side-body').multilingual();
            });
        </script>
    @endif
    <script>
        var deleteFormAction;
        $('.delete').on('click', function (e) {
            var form = $('#delete_form')[0];

            if (!deleteFormAction) {
                // Save form action initial value
                deleteFormAction = form.action;
            }

            form.action = deleteFormAction.match(/\/[0-9]+$/)
                ? deleteFormAction.replace(/([0-9]+$)/, $(this).data('id'))
                : deleteFormAction + '/' + $(this).data('id');

            $('#delete_modal').modal('show');
        });

    </script>
@stop
