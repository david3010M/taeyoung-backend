@php use Carbon\Carbon; @endphp
    <!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <style>
        @page {
            margin: 0;
        }

        body {
            padding-top: 5rem;
            width: 100vw;
            height: 100vh;
            font-family: Inter, sans-serif;
        }

        .headerBlock {
            background-color: black;
            height: 75px;
            width: 40px;
            margin-right: 20px;
        }

        .headerImage {
            width: auto;
            height: 60px;
            padding-left: 20px;
            padding-right: 20px;
        }

        .footerImage {
            width: auto;
            height: 75px;
            opacity: 0.5;
        }


        /*    WIDTH*/
        .w100 {
            width: 100%;
        }

        .h100 {
            height: 100%;
        }

        /*    MARGIN*/

        /*    STYLES*/
        .wContent {
            width: 60px;
            vertical-align: baseline;
        }

        .text-center {
            text-align: center;
        }

        .text-start {
            text-align: left;
        }

        .text-end {
            text-align: right;
        }

        .interletter {
            color: #4e413e;
            letter-spacing: 1.5px;
        }

        /*    FOOTER*/
        .footer {
            color: white;
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding-top: 16px;
            padding-bottom: 20px;
            height: 120px;
            font-size: 14px;
        }

        .page {
            position: absolute;
            bottom: 190px;
            right: 0;
        }

        .tableFooter {
            /*border-top: 2px solid #040780;*/
            /*border-bottom: 2px solid #040780;*/
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        .tableFooter td {
            padding-top: 15px;
        }

        .black {
            background-color: black;
        }

        .p64 {
            padding: 64px;
        }

        .pe64 {
            padding-right: 64px;
        }

        .mb40 {
            margin-bottom: 40px;
        }

        .p32 {
            padding: 32px 32px 24px;
        }

        .px64 {
            padding-left: 64px;
            padding-right: 64px;
        }

        .w50 {
            width: 50%;
        }

        .m0 {
            margin: 0;
        }

        .font20 {
            font-size: 20px;
        }

        .font30 {
            font-size: 30px;
        }

        .mx64 {
            margin-left: 64px;
            margin-right: 64px;
        }

        .mx56 {
            margin-left: 56px;
            margin-right: 56px;
        }

        .pl56 {
            padding-left: 56px;
        }

        .tableData {
            border-collapse: collapse;
        }

        .tableData th {
            padding: 8px;
            color: white;
            background-color: #040780;
            border-top: 1px solid #040780;
            border-bottom: 2px solid #040780;
        }

        .tableData td {
            padding: 8px;
            border-bottom: 1px solid rgba(30, 41, 59, 0.5);
        }

        .bold {
            font-weight: bold;
        }

        .gray {
            color: #4d4d4d;
        }

        .font10 {
            font-size: 10px;
        }

        .font12 {
            font-size: 12px;
        }

        .font14 {
            font-size: 14px;
        }

        .saltopagina {
            page-break-after: always;
        }

        .absolute {
            position: fixed;
            top: 40px;
            left: 0;
            width: 100px;
        }

        .normal {
            font-weight: normal;
        }


    </style>
</head>


<body>

{{--LOGO--}}
<table class="w100 mb40">
    <tr class="w100">
        <td class="wContent">
            <div class="headerBlock"></div>
        </td>
        <td class="black">
            <img class="headerImage" src="{{ asset('img/taeyoung.png') }}" alt="logo">
        </td>
        <td class="w100">
            <table class="w100 pe64">
                <tr>
                    <td>
                        <h2 class="m0 text-end normal font20">COTIZACIÓN <strong
                                class="">N° {{$quotation->number}}</strong>
                        </h2>
                    </td>
                </tr>
                <tr>
                    <td class="text-end interletter bold">
                        {{ Carbon::parse($quotation->date)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{--RAZON SOCIAL--}}
<table class="w100 mb40">
    <tr class="w100">
        <td class="w100">
            <table class="w100 pl56">
                <tr>
                    <td>
                        <h2 class="m0 text-start normal font20">
                            <strong>{{ $quotation->clientData->filterName }}</strong>
                        </h2>
                    </td>
                </tr>
                <tr>
                    <td class="text-start bold font14 gray">
                        {{ $quotation->clientData->filterDocument }}
                    </td>
                </tr>
            </table>
    </tr>
</table>


<div class="mx56">
    {{--FOOTER--}}
    <div class="w100 footer black">
        <div class="px64">
            <table class="w100 h100 tableFooter">
                <tr class="w100">
                    <td class="text-center w50">
                        <img class="footerImage" src="{{ asset('img/taeyoung.png') }}"
                             alt="logo">
                    </td>
                    <td class="text-start w50">
                        <div>20607921238</div>
                        <div>TAEYOUNG INTERNACIONAL E.I.R.L.</div>
                        <div>{{"Calle Santa Adela Mza. C Lote. 1 Dpto. 401"}}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <table class="w100 tableData mb40">
        <tr class="text-center font10">
            <th style="width: 55%">MAQUINARIA / REPUESTO</th>
            <th style="width: 15%">CANTIDAD</th>
            <th style="width: 15%">V. UNITARIO</th>
            <th style="width: 15%">V. VENTA</th>
        </tr>

        @foreach ($quotation->detailMachinery as $index => $machinery)
            <tr class="text-center font10">
                <td>{{ $machinery->description }}</td>
                <td>{{ $machinery->quantity }}</td>
                <td>{{$quotation->currencySymbol}} {{ $machinery->salePrice }}</td>
                <td>{{$quotation->currencySymbol}} {{ $machinery->saleValue }}</td>
            </tr>
        @endforeach
        @foreach ($quotation->detailSpareParts as $index => $sparePart)
            <tr class="text-center font10" style=background-color:#f4f4f4>
                <td>{{ $sparePart->sparePart->name }}</td>
                <td>{{ $sparePart->quantity }}</td>
                <td>{{$quotation->currencySymbol}} {{ $sparePart->salePrice }}</td>
                <td>{{$quotation->currencySymbol}} {{ $sparePart->saleValue }}</td>
            </tr>
        @endforeach
    </table>

    <table class="w100 mb40">
        <tr class="w100">
            <td class="w100">
                <table class="w100">
                    <tr>
                        <td>
                            <h2 class="m0 text-end normal font10">Total maquinaria:
                                <strong>
                                    {{$quotation->currencySymbol}} {{$quotation->totalMachinery}}
                                </strong>
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h2 class="m0 text-end normal font10">Total repuestos:
                                <strong>
                                    {{$quotation->currencySymbol}} {{$quotation->totalSpareParts}}
                                </strong>
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h2 class="m0 text-end normal font10">Subtotal:
                                <strong>
                                    {{$quotation->currencySymbol}} {{$quotation->subtotal}}
                                </strong>
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h2 class="m0 text-end normal font10">Descuento:
                                <strong>
                                    {{$quotation->discount}}
                                </strong>
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h2 class="m0 text-end normal font10">Total:
                                <strong>
                                    {{$quotation->currencySymbol}} {{$quotation->total}}
                                </strong>
                            </h2>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>


</div>

</body>

</html>
