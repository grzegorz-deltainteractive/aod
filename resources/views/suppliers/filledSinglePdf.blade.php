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
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/css/pdf.css">
    <style>
        *{ font-family: DejaVu Sans !important; font-size: 12px;}

        .table-responsive table {
            border-collapse: collapse;
            width: 100%;
        }
        .table-responsive table tr th {
            border: 1px solid black;
            padding: 2px 5px;
            text-align: center;
        }
        .table-responsive table tr td {
            border: 1px solid black;
            padding: 2px 5px;
        }
        /*# sourceMappingURL=pdf.css.map */
    </style>
</head>
<body>
    <h1 class="page-title">
        Ankieta "{{$pool->name}}" - wyniki
    </h1>
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; font-weight: bold; font-size: 16px;">
                ALAB laboratoria Sp. z o.o.
            </td>
            <td style="text-align: right; font-weight: bold; font-size: 16px;">
                "{{$pool->name}}" {{$pool->numer_procedury}}_{{date('Y', strtotime($pool->data_wydania_ankiety))}}
        </tr>
        <tr>
            <td colspan="2" style="border-bottom: 1px solid black;">
                ANKIETA OCENY DOSTAWCY USŁUG LABORATORYJNYCH, Średnia z {{date('Y', strtotime($pool->data_wydania_ankiety))}}
            </td>
        </tr>
    </table>
    @foreach($pool->categories as $category)
        <h4>Kategoria: <strong>{{$category->name}}</strong></h4>
        <div class="table-responsive">
            <table class="table table-hover">
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
        </div>
    @endforeach
    <br /><br /><br />
    <div class="table-responsive">
        <table class="table table-hover">
            <tr>
                <th>Uzupełnił</th>
                <th>Data uzupełnienia</th>
                <th>Zaakceptował</th>
                <th>Data zaakceptowania</th>
            </tr>
            <tr>
                <td>{{$userNameFilled}}</td>
                <td>{{$status->filled_date}}</td>
                <td>{{$userNameAccepted}}</td>
                <td>{{$status->accepted_date ?? '-'}}</td>
            </tr>
        </table>
    </div>

</body>
</html>
