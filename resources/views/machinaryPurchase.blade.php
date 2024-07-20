<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoja de Servicio</title>
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
            /*margin: 0;*/
            /*padding: 0;*/
            /*background-color: #2d3748;*/
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
            text-align: start;
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

        .tableFooter {
            border-top: 1px solid #FE8700;
            border-bottom: 1px solid #FE8700;
        }

        .tableFooter td {
            padding-top: 15px;
        }

        .black {
            background-color: black;
        }

        .p64 {
            padding-left: 64px;
            padding-right: 64px;
        }

        .w50 {
            width: 50%;
        }

    </style>

</head>


<body>

<table class="w100">
    <tr class="w100">
        <td class="wContent">
            <div class="headerBlock"></div>
        </td>
        <td><img class="headerImage" src="{{ asset('img/logo.png') }}" alt="logo"></td>
    </tr>

</table>

<div class="w100 footer black">
    <div class="p64">
        <table class="w100 h100 tableFooter">
            <tr class="w100">
                <td class="text-center w50"><img class="footerImage" src="{{ asset('img/logo.png') }}" alt="logo"></td>
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
