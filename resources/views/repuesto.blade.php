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
            height: 75px;
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
            border-top: 2px solid #FE8700;
            border-bottom: 2px solid #FE8700;
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

        .tableSparePart {
            border-collapse: collapse;
        }

        .tableSparePart th {
            padding: 16px;
            color: white;
            background-color: #FE8700;
            border-top: 1px solid #FE8700;
            border-bottom: 2px solid #FE8700;
        }

        .tableSparePart td {
            padding: 8px;
            border-top: 1px solid #FE8700;
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


    </style>

</head>


<body>

{{--PAGE--}}
<table class="px64 page w100">
    <tr class="w100 font12">
        <td>
            <p class="m0 gray text-start">Mostrando {{$per_page}} de {{$total}} resultados del {{$from}}
                al {{$to}}</p>
        </td>
        <td>
            <p class="m0 gray text-end">Página {{$page}}</p>
        </td>
    </tr>
</table>

{{--LOGO--}}
<table class="w100">
    <tr class="w100">
        <td class="wContent">
            <div class="headerBlock"></div>
        </td>
        <td><img class="headerImage" src="{{ asset('img/logo.png') }}" alt="logo"></td>
    </tr>
</table>

{{--CONTENT--}}
<table class="p32 px64 w100">
    <tr>
        <td>
            <h2 class="m0 text-end font30">CATÁLOGO DE REPUESTOS</h2>
        </td>
    </tr>
    <tr>
        <td>
            @if($name && $code)
                <p class="gray text-end">Búsqueda por Nombre: "{{ $name }}" y Código: "{{ $code }}"</p>
            @elseif($name)
                <p class="gray text-end">Búsqueda por Nombre: "{{ $name }}"</p>
            @elseif($code)
                <p class="gray text-end">Búsqueda por Código: "{{ $code }}"</p>
            @endif

        </td>
    </tr>
</table>

<table class="w100 px64">
    <tr>
        <td class="text-end interletter bold">
            {{ Carbon::now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
        </td>
    </tr>
</table>

<div class="mx64">
    <table class="w100 tableSparePart">
        <tr class="text-center">
            <th>CÓDIGO</th>
            <th>NOMBRE</th>
            <th>PRECIO COMPRA</th>
            <th>PRECIO VENTA</th>
        </tr>

        @foreach ($repuestos as $repuesto)
            <tr class="text-center">
                <td>{{ $repuesto->code }}</td>
                <td>{{ $repuesto->name }}</td>
                <td>$ {{ $repuesto->purchasePrice }}</td>
                <td>$ {{ $repuesto->salePrice }}</td>
            </tr>
        @endforeach
    </table>
</div>


{{--FOOTER--}}
<div class="w100 footer black">
    <div class="px64">
        <table class="w100 h100 tableFooter">
            <tr class="w100">
                <td class="text-center w50">
                    <img class="footerImage" src="{{ asset('img/logo.png') }}"
                         alt="logo">
                </td>
                <td class="text-start w50">
                    <div>20607921238</div>
                    <div>TAEYOUNG INTERNACIONAL E.I.R.L.</div>
                    <div>Calle Santa Adela Mza. C Lote. 1 Dpto. 401</div>
                </td>
            </tr>
        </table>
    </div>
</div>

</body>

</html>
