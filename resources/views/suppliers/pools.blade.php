@extends('voyager::master')
@section('content')
    <h1 class="page-title">
        Przeglądaj ankiety dla dostawcy
    </h1>
    <div class="page-content container-fluid">
        @if (isset($pools) && !empty($pools))
            <div class="panel panel-bordered">
                <div class="panel-body">
                    <h2>Ankiety dla dostawcy</h2>
                    <table class="table table-hover dataTable no-footer">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nazwa ankiety</th>
                            <th>Wypełniona</th>
                            <th>Opcje</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($pools as $pool)
                            <tr>
                                <td style="vertical-align: middle">
                                    {{$pool['id']}}
                                </td>
                                <td style="vertical-align: middle">
                                    {{$pool['name']}}
                                </td>
                                <td>
                                    <?php
                                    $check = \App\Models\SupplierPoolQuestion::checkPoolEntered($supplier_id, $pool->id);
                                    if ($check) {
                                        echo 'Tak';
                                    } else {
                                        echo 'Nie';
                                    }

                                    ?>
                                </td>
                                <td style="vertical-align: middle">
                                    @if (canAcceptPool())
                                    <a href="{{route('suppliers.pools.filled', ['id' => $supplier_id, 'poolId' => $pool->id])}}" class="btn btn-sm btn-primary ">Sprawdź uzupełnienia</a>
                                    @endif
                                    @if (canFillPool() && !$check)
                                        <a href="{{route('suppliers.pools.fill', ['id' => $supplier_id, 'poolId' => $pool->id])}}" class="btn btn-sm btn-primary ">Uzupełnij ankietę</a>
                                    @else
                                        Uzupełniłeś już tą ankietę!
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <h5>Brak ankiet dla dostawcy</h5>
        @endif
            <br />
            <a href="/admin/suppliers" class="btn btn-sm btn-secondary " style="background-color: #cccccc">Powrót</a>
    </div>
@stop
