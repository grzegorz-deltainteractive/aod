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
        footer #pagenumber:after {
            content: counter(page);
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
    <table style="width:100%;">
        <tr>
            <td style="width: 50%;">
                Wydruk raportu
            </td>
            <td style="widows: 50%; text-align: right">
                Strona <span id="pagenumber"></span>
            </td>
        </tr>

    </table>
</footer>
<main>
    <h1 class="page-title">
        Raport - wyniki
    </h1>
    <div class="table-responsive">
        @foreach ($selectedSuppliers as $supplierId)
            <h2>Ankiety dla dostawcy {{$suppliersList[$supplierId]}}</h2>
            <hr />
            <?php
                $supplier = \App\Models\Supplier::where('id', $supplierId)->first();
                $pools = [];
                if (!empty($supplier)) {
                    $pools = $supplier->poolsRelation;

                }
            ?>
            @if (!empty($pools))
                @foreach ($pools as $pool)
                    <h4>Definicja ankiety {{$pool->name}} {{$pool->numer_procedury}}</h4>
                    @foreach($pool->categories as $category)
                        <h5>Kategoria: <strong>{{$category->name}}</strong></h5>
                        <table  style="width: 100%;">
                            <thead>
                            <tr>
                                <th style="width: 30%; vertical-align: middle; text-align: center;">Parametr</th>
                                @foreach ($selectedYears as $year)
                                    <th style="vertical-align: middle; text-align: center;">{{$year}}</th>
                                @endforeach
{{--                                        <th>Maks</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($category->categoriesParameters as $sp)
                                <tr>
                                    <td style="text-align: center; vertical-align: middle">
                                        {{$sp->name}}
                                    </td>
                                    @foreach ($selectedYears as $year)
                                        <td style="text-align: center; vertical-align: middle">
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
                        <br />
                    @endforeach
                    <hr />
                @endforeach
            @endif
        @endforeach
    </div>
</main>
