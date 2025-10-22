<!DOCTYPE html>
<html>
<head>
    <title>Шаблон точки контроля: {{$branch->title}}</title>
    <style>
        body {
            font-family: "akitfo", sans-serif;
            text-align: center;
            position: relative;
            margin: 0;
            padding: 40px;
        }
        .top {
            position: absolute;
            bottom: 70%;
            height: 1450px;
            width: 100%;
            background: linear-gradient(270deg, {{$color}} 0%, #FFFFFF 100%);
            z-index: -1;
        }
        .bottom {
            position: absolute;
            top: 70%;
            height: 1650px;
            width: 100%;
            background: linear-gradient(90deg, {{$color}} 0%, #FFFFFF 100%);
            z-index: -1;
        }
        .header {
            z-index: 10;
            padding: 70px 0 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo {
            max-width: 575px;
            max-height: 200px;
        }
        .qr-place {
            padding: 60px 0 30px 0;
            margin-top: 25px;
            z-index: 10;
        }
        .qr-code {
            height: 400px;
            object-fit: contain;
        }
        .title {
            padding-left: 50px;
            padding-right: 50px;
            z-index: 10;
            font-size: 28px;
            font-weight: bold;
        }
        .text {
            z-index: 10;
        }
        p {
            font-size: 20px;
            font-weight: bold;
        }
        .bottom-part {
            width: 100%;
            z-index: 10;
            text-align: left;
            margin-top: 35px;
        }
        .tt {
            width: 100px;
            height: 40px;
        }
        .store-icon {
            width: 130px;
            height: 40px;
        }
        .little-p {
            font-size: 16px;
            font-style: italic;
            text-align: center;
        }
        .store-icon-first {
            padding-left: 70px;
        }
        .store-icon-second {
            padding-left: 20px;
            padding-right: 5px;
        }
    </style>
</head>
<body>
<div class="top">
</div>
<div class="bottom"></div>
<div class="header">
    <!-- Путь к логотипу компании -->
    <img src="{{$logo}}" alt="Логотип компании" class="logo">
</div>
<div class="title">
    {{$branch->title}}
</div>
<div class="qr-place">
    <!-- Путь к изображению QR-кода -->
    <img src="{{storage_path('app/files/'.$branch->qrSrc)}}" alt="QR Code" class="qr-code">
</div>
<div class="text">
    <p>Кіруді/шығуды тіркеу үшін QR кодын сканерлеңіз</p>
    <p>Сканируйте QR-код для фиксации входа/выхода</p>
    <p>Scan the QR code to record entry/exit</p>
</div>
<div class="bottom-part">
    <table>
        <tr>
            <td class="store-icon-first">
                <img class="tt" src="{{public_path('TT_logo.png')}}" alt="">
            </td>
            <td class="store-icon-second">
                <p class="little-p">доступно в</p>
            </td>
            <td>
                <img class="store-icon" src="{{public_path('AppStore.png')}}" alt="">
            </td>
            <td>
                <img class="store-icon" src="{{public_path('GooglePlay.png')}}" alt="">
            </td>
        </tr>
    </table>
</div>
</body>
</html>
