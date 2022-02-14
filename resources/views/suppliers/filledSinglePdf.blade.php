<?php
/**
 * Created by Grzegorz Możdżeń
 * Date: 19/01/2022
 * Time: 01:33
 */
$status = \App\Models\SupplierPoolStatus::getStatus($user_id, $pool->id, $supplier_id);
if (!empty($status)) {
    $userName = \App\User::where('id', $status->user_id)->first();
    $userNameFilled = $userName->name;
    $userName = \App\User::where('id', $status->accepted_user_id)->first();
    $userNameAccepted = $userName->name ?? '-';
} else {
    $userName = \App\User::where('id', $user_id)->first();
    $userNameFilled = $userName->name;
    $userNameAccepted = '';
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="http://aod.develop/admin/voyager-assets?path=css%2Fapp.css">
</head>
<body>
    <h1 class="page-title">
        Ankieta "{{$pool->name}}" - wyniki
    </h1>
    <div class="page-content container-fluid">
        <div class="panel panel-bordered">
            <div class="panel-body">
                @foreach($pool->categories as $category)
                    <h4>Kategoria: <strong>{{$category->name}}</strong></h4>
                    <table class="table" style="width: 100%;">
                        <thead>
                        <tr>
                            <th style="width: 30%;">Parametr</th>
                            <th style="width: 10%;">Min</th>
                            <th style="width: 10%;">Max</th>
                            <th style="width: 20%;">Odpowiedź</th>
                            <th>Uwagi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($category->categoriesParameters as $sp)
                            <tr>
                                <td>
                                    {{$sp->name}}
                                </td>
                                <td>
                                    {{$sp->rating_min}}
                                </td>
                                <td>
                                    {{$sp->rating_max}}
                                </td>
                                <td>
                                    {{\App\Models\SupplierPoolQuestion::getValue($sp->id, $data, $supplier_id, $pool_id, $user_id)}}
                                </td>
                                <td>
                                    {{\App\Models\SupplierPoolQuestion::getNotices($sp->id, $data, $supplier_id, $pool_id, $user_id)}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <br />
                @endforeach
                <br />
                    <div class="row">
                        <div class="col-12 col-md-5">
                            Uzupełnił:
                        </div>
                        <div class="col-12 col-md-7">
                            {{$userNameFilled}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-5">
                            Data uzupełnienia:
                        </div>
                        <div class="col-12 col-md-7">
                            {{$status->filled_date}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-5">
                            Zaakceptował:
                        </div>
                        <div class="col-12 col-md-7">
                            {{$userNameAccepted}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-5">
                            Data zaakceptowania:
                        </div>
                        <div class="col-12 col-md-7">
                            {{$status->accepted_date ?? '-'}}
                        </div>
                    </div>
            </div>
        </div>
    </div>
</body>
</html>
