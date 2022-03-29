@extends('voyager::master')

@section('content')
    <div class="page-content">
        @include('voyager::alerts')
        @include('voyager::dimmers')
        <div class="container container-fluid">

            <?php
            $userId = Auth::user()->id;
            $user = \App\User::where('id', $userId)->first();
            ?>
            <br /><br />
            <h4>Przypisane ankiety</h4>
            <br /><br />
            <div class="row">
                <div class="col-12 col-lg-10">
                    <?php
                    $pools = \App\User::getPoolsForUser($userId);
                    ?>
                    @if(empty($pools))
                        <h5>Użytkownik nie posiada ankiet</h5>
                    @else
                        <table class="table table-ordered">
                            <thead>
                            <tr>
                                <th>Nazwa ankiety</th>
                                <th>KOD</th>
                                <th>Dostawca</th>
                                <th>Rok</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($pools as $pool)
                                @foreach ($pool->suppliers as $supplier)
                                    <tr>
                                        <td style="vertical-align: middle">
                                            {{$pool->name}}
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
                                            <?php
                                            $status = \App\Models\SupplierPoolStatus::getPoolFilledStatus($userId, $pool->id, $supplier->id);
                                            $check = \App\Models\SupplierPoolQuestion::checkPoolEntered($supplier->id, $pool->id);
                                            $authUser = \Illuminate\Support\Facades\Auth::user()->id;
                                            ?>
                                            @if($status == 'unfilled')
                                                Nieuzupełniona&nbsp;
                                                @if (canFillPool() && !$check && $authUser == $userId)
                                                    <a href="{{route('suppliers.pools.fill', ['id' => $supplier->id, 'poolId' => $pool->id])}}" class="btn btn-sm btn-primary ">Uzupełnij ankietę</a>
                                                @endif
                                            @elseif ($status == 'unaceppted')
                                                Niezaakceptowana
                                            @else
                                                {{$status}}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

            </div>
        </div>
    </div>
@stop

@section('javascript')

    @if(isset($google_analytics_client_id) && !empty($google_analytics_client_id))


    @endif

@stop
