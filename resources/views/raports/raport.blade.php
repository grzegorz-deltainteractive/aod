@extends('voyager::master')
@section('content')
    <h1 class="page-title" style="width: 100%">
        <img src="/images/gray_ankiety.png" alt="" class="header-icon-img" />  Raport - wyniki
        <img src="/images/export-pdf.png" alt="" class="float-right pull-right " style="cursor:pointer; margin-top: 15px;" id="export-pdf-image" />
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
                            <select name="suppliersIds[]" class="form-control" multiple>
                                @foreach ($suppliersList as $supplierId => $supplierName)
                                    <?php
                                    $selected = '';
                                    if (in_array($supplierId, $selectedSuppliers)) {
                                        $selected = 'selected="selected"';
                                    }
                                    ?>
                                    <option value="{{$supplierId}}" {{$selected}}>{{$supplierName}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-12 col-lg-4">
                            <label for="poolsIds">Wybierz szablon ankiety (można zaznaczyć kilka)</label>
                            <select name="poolsIds[]" id="poolsIds" class="form-control" multiple>
                                @foreach ($pools as $supplierId => $supplierName)
                                    <?php
                                    $selected = '';
                                    if (in_array($supplierId, $selectedPools)) {
                                        $selected = 'selected="selected"';
                                    }
                                    ?>
                                    <option value="{{$supplierId}}" {{$selected}}>{{$supplierName}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-12 col-lg-4">
                            <label for="years">Wybierz lata  (możesz zaznaczyć klika)</label>
                            <select name="years[]" class="form-control" multiple>
                                @foreach ($years as $supplierId => $supplierName)
                                    <?php
                                        $selected = '';
                                        if (in_array($supplierName, $selectedYears)) {
                                            $selected = 'selected="selected"';
                                        }
                                    ?>
                                    <option value="{{$supplierName}}" {{$selected}}>{{$supplierName}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-primary"  type="submit">Wygeneruj raport</button>
                    <button class="btn btn-sm btn-primary" id="generate-pdf-button"  formtarget="_blank"  type="submit" name="generatePDF" value="1">Zapisz do PDF</button>
                </form>
            </div>
            <div class="panel-body">
                @foreach ($selectedSuppliers as $supplierId)
                    <h2>Ankiety dla dostawcy {{$suppliersList[$supplierId]}}</h2>
                    <hr />
                    <?php
                        $supplier = \App\Models\Supplier::where('id', $supplierId)->first();
                        $pools = [];
                        $poolsExists = false;
                        if (!empty($supplier)) {
                            $pools = $supplier->poolsRelation;

                        }
                        if (isset($selectedPools) && !empty($selectedPools)) {
                            foreach ($pools as $i=>$pool) {

                                if (!in_array($pool->id, $selectedPools)) {
                                    unset($pools[$i]);
                                } else {
                                    $poolsExists = true;
                                }
                            }
                        } else {
                            $poolsExists = true;
                        }
//                        dd($pools);
//                        dd($selectedPools);
//                        dd($poolsExists);
                    ?>
                    @if (!empty($pools) && $poolsExists)
                        @foreach ($pools as $pool)
                            <h4>Definicja ankiety {{$pool->name}} {{$pool->numer_procedury}}</h4>
                            @foreach($pool->categories as $category)
                                <h5 style="color: black;">Kategoria: <strong>{{$category->name}}</strong></h5>
                                <table class="table" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 30%;">Parametr</th>
                                        @foreach ($selectedYears as $year)
                                            <th style="width: 30%;">{{$year}}</th>
                                        @endforeach
{{--                                        <th>Maks</th>--}}
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($category->categoriesParameters as $sp)
                                        <tr>
                                            <td>
                                                {{$sp->name}}
                                            </td>
                                            @foreach ($selectedYears as $year)
                                                <td>
                                                    <?php
                                                        $result = \App\Models\SupplierPoolQuestion::getAverageResult($pool->id, $category->id, $sp->id, $supplierId, $year, $sp->rating_max);
                                                        echo $result;
                                                    ?>
                                                </td>
                                            @endforeach
{{--                                            <td>--}}
{{--                                                {{$sp->rating_max}}--}}
{{--                                            </td>--}}
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                            <br /><hr/><br />
                        @endforeach
                    @endif
                @endforeach
            </div>
        </div>
    </div>

@stop
@section('javascript')
    <script>
        jQuery(function() {
            jQuery('img#export-pdf-image').click(function() {
                jQuery('#generate-pdf-button').click();
            })
        })
    </script>
@stop
