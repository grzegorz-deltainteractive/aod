@extends('voyager::master')
@section('content')
    <h1 class="page-title">
        <img src="/images/gray_ankiety.png" alt="" class="header-icon-img" />  Raport
    </h1>
    <div class="page-content container-fluid">
        <div class="panel panel-bordered">
            <div class="panel-body">
                @if (isset($messasge) && !empty($messasge))
                    <h5>{{$messasge}}</h5>
                @endif
                <form action="{{route('raports.generate')}}" method="post">
                    <?php echo e(csrf_field()); ?>
                    <div class="row">
                        <div class="form-group col-12 col-lg-4">
                            <label for="suppliersIds">Wybierz dostawcę (można zaznaczyć kilku)</label>
                            <select name="suppliersIds[]" id="suppliersIds" class="form-control" multiple>
                                @foreach ($suppliersList as $supplierId => $supplierName)
                                    <option value="{{$supplierId}}">{{$supplierName}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-12 col-lg-4">
                            <label for="poolsIds">Wybierz szablon ankiety (można zaznaczyć kilka)</label>
                            <select name="poolsIds[]" id="poolsIds" class="form-control" multiple>

                            </select>
                        </div>
                        <div class="form-group col-12 col-lg-4">
                            <label for="years">Wybierz lata  (możesz zaznaczyć klika)</label>
                            <select name="years[]" class="form-control" multiple>
                                @foreach ($years as $supplierId => $supplierName)
                                    <option value="{{$supplierName}}">{{$supplierName}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-primary"  type="submit">Wygeneruj raport</button>
                </form>
            </div>
        </div>
    </div>
@stop
@section('javascript')
    <script>
        $(function() {
            var poolFormUrl = '{!! route("raports.getPools") !!}';
            $('#suppliersIds').change(function() {
                // console.log($(this).val());
                $('#poolsIds').find('option').remove();
                $('#poolsIds').append($('<option>', {
                    value: '',
                    text: 'Proszę czekać pobieram listę'
                }));
                $.post(poolFormUrl, {
                    'suppliersIds[]': $(this).val()
                },
                function(data) {
                    if (data.error !== '') {
                        $('#poolsIds').find('option').remove();
                        $('#poolsIds').append($('<option>', {
                            value: '',
                            text: data.error
                        }));
                    } else {
                        // mam ankiety więc je wypisze
                        $('#poolsIds').find('option').remove();
                        $.each(data.pools, function(val, i) {
                            $('#poolsIds').append($('<option>', {
                                value: val,
                                text: i
                            }));
                        })
                    }
                });
            });
        })
    </script>
@stop
