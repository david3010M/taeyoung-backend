@php use Carbon\Carbon; @endphp
    <!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repuestos</title>
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
            letter-spacing: 2px;
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
            border-top: 2px solid #040780;
            border-bottom: 2px solid #040780;
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

        .tableData {
            border-collapse: collapse;
            position: fixed;
            padding-left: 56px;
            padding-right: 56px;
            top: 210px;
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
                        <h2 class="m0 text-end font30">CATÁLOGO DE REPUESTOS</h2>
                    </td>
                </tr>
                <tr>
                    <td class="text-end interletter bold">
                        {{ Carbon::now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                    </td>
                </tr>
            </table>
    </tr>
</table>


@php
    $currentPage = 1;
@endphp

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
    <table class="w100 tableData">
        <tr class="text-center font14">
            <th style="width: 20%">CÓDIGO</th>
            <th style="width: 40%">NOMBRE</th>
            <th style="width: 20%">PRECIO COMPRA</th>
            <th style="width: 20%">PRECIO VENTA</th>
        </tr>


        @foreach ($repuestos as $index => $repuesto)
            @if ($index > 0 && $index % 18 == 0)
    </table>

    {{-- FOOTER PARA CADA PÁGINA --}}
    <table class="px64 page w100">
        <tr class="w100 font12">
            <td>
                <p class="m0 gray text-end">Página {{ $currentPage }}</p>
            </td>
        </tr>
    </table>

    {{-- Salto de página --}}
    <div class="saltopagina"></div>


    <table class="w100 mb40 absolute">
        <tr class="w100">
            <td class="wContent">
                <div class="headerBlock"></div>
            </td>
            <td class="black">
                <img class="headerImage" src="{{ asset('img/taeyoung.png') }}" alt="logo">
            </td>
    </table>

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

    @php
        $currentPage++; // Incrementa el número de página
    @endphp

    {{-- Nueva tabla con el encabezado para la siguiente página --}}
    <table class="w100 tableData">
        <tr class="text-center font14">
            <th style="width: 20%">CÓDIGO</th>
            <th style="width: 40%">NOMBRE</th>
            <th style="width: 20%">PRECIO COMPRA</th>
            <th style="width: 20%">PRECIO VENTA</th>
        </tr>
        @endif
        <tr class="text-center font14">
            <td>{{ $repuesto->code }}</td>
            <td>{{ $repuesto->name }}</td>
            <td>$ {{ $repuesto->purchasePrice }}</td>
            <td>$ {{ $repuesto->salePrice }}</td>
        </tr>
        @endforeach
    </table>

    {{-- Footer final con la última página --}}
    <table class="px64 page w100">
        <tr class="w100 font12">
            <td>
                <p class="m0 gray text-end">Página {{ $currentPage }}</p>
            </td>
        </tr>
    </table>

</div>

</body>

</html>
