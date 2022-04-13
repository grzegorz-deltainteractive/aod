<?php
/**
 * Created by Grzegorz Możdżeń
 * Date: 19/01/2022
 * Time: 01:33
 */
$status = \App\Models\SupplierPoolStatus::getStatus($user_id, $pool->id, $supplier_id);
if (!empty($status)) {
    $userName = \App\User::where('id', $status->user_id)->first();
    $userNameFilled = $userName->imie .' '. $userName->nazwisko;
    $userName = \App\User::where('id', $status->accepted_user_id)->first();
    if (!empty($userName)) {
        $userNameAccepted = $userName->imie .' '.$userName->nazwisko ?? '-';
    } else {
        $userNameAccepted = '-';
    }
    $userNameAdmin = '-';
    if (!empty($status->admin_edited_user)) {
        $userName = \App\User::where('id', $status->admin_edited_user)->first();
        $userNameAdmin = $userName->imie .' ' .$userName->nazwisko ?? '-';
    }
} else {
    $userName = \App\User::where('id', $user_id)->first();
    $userNameFilled = $userName->imie .' '.$userName->nazwisko;
    $userNameAccepted = '';
}

$pools1 = [];
$pools2 = [];
foreach ($pool->categories as $category) {
    if (isset($category->is_requested) && $category->is_requested == 1) {
        $pools2[] = $category;
    } else {
        $pools1[] = $category;
    }
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/css/pdf.css">
    <style>
        @font-face {
            font-family: 'arialn';
            src: url({{ storage_path('fonts/arialn.ttf') }}) format("truetype");
            font-weight: 400;
            font-style: normal; // use the matching font-style here
        }
        @font-face {
            font-family: 'arialn';
            src: url({{ storage_path('fonts/arialni.ttf') }}) format("truetype");
            font-weight: 400;
            font-style: italic; // use the matching font-style here
        }
        @font-face {
            font-family: 'arialn';
            src: url({{ storage_path('fonts/arialnb.ttf') }}) format("truetype");
            font-weight: 700;
            font-style: normal; // use the matching font-style here
        }
        *{ font-family: "arialn" !important; font-size: 12px;}

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
        footer {
            position: fixed;
            bottom: -50px;
            left: 0px;
            right: 0px;
            height: 50px;
            border-top: 1px solid black;
        }
        header {
            position: fixed;
            top: -30px;
            left: 0px;
            right: 0px;
            height: 50px;

        }
        .row {
            width: 100%;
        }
        .col-md-5 {
            width: 30%;
        }
        .col-md-7 {
            width: 60%;
        }
    </style>
</head>
<body>
<header>
    ALAB laboratoria Sp. z o.o.
</header>
<footer>
    {{$pool->numer_wydania_ankiety}} - {{$pool->data_wydania_ankiety}}
</footer>
<main>
    <table style="width: 100%;">
        <tr>
            <td style="width: 70%; font-weight: bold; font-size: 16px;">
                {{$pool->name}}
            </td>
            <td style="text-align: right; font-weight: bold; font-size: 16px;">
                {{$pool->numer_procedury}}_{{date('Y', strtotime($pool->data_wydania_ankiety))}}
        </tr>
    </table>
    @if(isset($supplier) && !empty($supplier))
    <br />
    <table style="width: 100%;">
        <tr>
            <td style="width: 20%; font-weight: strong;">
                <strong>Adres dostawcy:</strong>
            </td>
            <td>
                {{$supplier->city}}, NIP: {{$supplier->NIP}}, {{$supplier->skrot}}
            </td>
        </tr>
        <tr>
            <td style="width: 20%; font-weight: strong;">
                <strong>Nazwa dostawcy:</strong>
            </td>
            <td>
                {{$supplier->name}}
            </td>
        </tr>
    </table>
    <br />
    @endif
    <div class="table-responsive">
        <table style="width: 100%" class="table table-hover">
            <thead>
            <tr>
                <th style="width: 15%; vertical-align: middle; text-align: center;">
                    Ocena
                </th>
                <th style="width: 40%; vertical-align: middle; text-align: center;">
                    Oceniany parametr
                </th>
                <th style="width: 10%; vertical-align: middle; text-align: center;">
                    Punktacja
                </th>
                <th style="width: 10%; vertical-align: middle; text-align: center;">
                    Wynik
                </th>
                <th style="width: 25%; vertical-align: middle; text-align: center;">
                    Uwagi
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
                $ratingMinSum = 0;
                $ratingMaxSum = 0;
                $allSum = 0;
            ?>
            @foreach ($pools1 as $category)
            <?php $isFirstRow = true;?>
                @foreach ($category->categoriesParameters as $sp)
                    <?php
                    $ratingMinSum = $ratingMinSum + (int)$sp->rating_min;
                    $ratingMaxSum = $ratingMaxSum + (int)$sp->rating_max;
                    ?>
                    <tr>
                        <?php if ($isFirstRow):?>
                            <td rowspan="<?php echo count($category->categoriesParameters);?>" style="vertical-align: middle">
                                {{$category->name}}
                            </td>
                            <?php $isFirstRow = false;?>
                        <?php endif;?>
                        <td>
                            {{$sp->name}}
                        </td>
                        <td style="text-align: center; vertical-align: middle">
                            {{$sp->rating_min}}
                            -
                            {{$sp->rating_max}}
                        </td>
                        <td style="text-align: center; vertical-align: middle; font-weight: normal;">
                            <?php
                            $val = \App\Models\SupplierPoolQuestion::getValue($sp->id, $data, $supplier_id, $pool_id, $user_id);
                            $allSum = $allSum + (int)$val;
                            ?>
                            {{$val}}
                        </td>
                        <td style="text-align: center; vertical-align: middle">
                            {{\App\Models\SupplierPoolQuestion::getNotices($sp->id, $data, $supplier_id, $pool_id, $user_id)}}
                        </td>
                    </tr>
                @endforeach
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2">
                    Ocena dostawcy wyrobu / usługi
                </td>
                <td style="text-align: center; vertical-align: middle; font-weight: bold;">
                    {{$ratingMinSum}} - {{$ratingMaxSum}}
                </td>
                <td style="text-align: center; vertical-align: middle; font-weight: bold;">
                    {{$allSum}}
                </td>
                <td></td>
            </tr>
            </tfoot>
        </table>
        @if(isset($pool->punkty) && !empty($pool->punkty))
            <h5 style="margin-bottom: 0px; padding-bottom: 0px; margin-top: 0; ">Łączna ilość punktów</h5>
            <p style="padding-left: 25px; margin-top:0; padding-top: 0;">
                <?php echo nl2br($pool->punkty);?>
            </p>
        @endif
        @if (!empty($pools2))
        <?php $allSum = 0;?>
        <p style="font-weight: bold; text-transform: uppercase; margin-top: 20px;">
            Warunek bezwględny dla dostawcy
        </p>
        <table style="width: 100%" class="table table-hover">
            <tbody>
            @foreach ($pools2 as $category)
                <?php $isFirstRow = true;?>
                @foreach ($category->categoriesParameters as $sp)
                    <?php
                    $ratingMinSum = $ratingMinSum + (int)$sp->rating_min;
                    $ratingMaxSum = $ratingMaxSum + (int)$sp->rating_max;
                    ?>
                    <tr>
                        <?php if ($isFirstRow):?>
                        <td rowspan="<?php echo count($category->categoriesParameters);?>" style="vertical-align: middle; width: 20%;">
                            {{$category->name}}
                        </td>
                        <?php $isFirstRow = false;?>
                        <?php endif;?>
                        <td style="width: 35%;">
                            {{$sp->name}}
                        </td>
                        <td style="text-align: center; vertical-align: middle; width: 10%;">
                            0 - nie <br />
                            1 - tak
                        </td>
                        <td style="text-align: center; vertical-align: middle; font-weight: normal; width: 10%;">
                            <?php
                            $val = \App\Models\SupplierPoolQuestion::getValue($sp->id, $data, $supplier_id, $pool_id, $user_id);
                            $allSum = $allSum + (int)$val;
                            ?>
                            {{$val}}
                        </td>
                        <td style="text-align: center; vertical-align: middle">
                            {{\App\Models\SupplierPoolQuestion::getNotices($sp->id, $data, $supplier_id, $pool_id, $user_id)}}
                        </td>
                    </tr>
                @endforeach
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2" style="text-align: center; vertical-align: middle">
                    Suma
                </td>
                <td style="text-align: center; vertical-align: middle">
                    1 - spełnia warunki
                </td>
                <td  style="text-align: center; vertical-align: middle" >
                    <strong>{{$allSum}}</strong>
                </td>
                <td>

                </td>
            </tr>
            </tfoot>
        </table>
        @endif
    </div>

    <br /><br /><br />
    <div class="table-responsive">
        <table>
            <tr>
                <td>Uzupełnił:</td>
                <td>{{$userNameFilled}}</td>
            </tr>
            <tr>
                <td>Data uzupełnienia:</td>
                <td>{{$status->filled_date}}</td>
            </tr>
            <tr>
                <td>Zaakceptował:</td>
                <td>{{$userNameAccepted}}</td>
            </tr>
            <tr>
                <td>Data zaakceptowania:</td>
                <td>{{$status->accepted_date ?? '-'}}</td>
            </tr>
            @if (!empty($status->admin_edited_date))
                <tr>
                    <td>Edytowane przed administratora:</td>
                    <td>{{$userNameAdmin}}</td>
                </tr>
                <tr>
                    <td>Data edycji:</td>
                    <td>{{$status->admin_edited_date ?? '-'}}</td>
                </tr>
            @endif
        </table>

    </div>
</main>
</body>
</html>
