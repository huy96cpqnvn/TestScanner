<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="msapplication-TileImage" content="images/logofavicon/logo144.png" />
    <meta name="msapplication-TileColor" content="#B4DFF6" />

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link href='../../../public/images/favicon.png' rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" href="../../../public/css/flexslider.css" type="text/css" media="screen" />
    <script type="text/javascript" src="../../../public/js/jquery-3.2.1.slim.min.js"></script>
    <link rel="stylesheet" href="../../../public/css/bootstrap.min.css" />
    <script type="text/javascript" src="../../../public/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../../../public/js/jquery.js"></script>
    <link rel="stylesheet" type="text/css" href="../../../public/css/style.css" />
    <link rel="stylesheet" type="text/css" href="../../../public/css/component.css" />

</head>
<style>
    * {
        font-family: dejavu serif;
        font-size: 15px;
    }
</style>
<body>
<div style="max-width: 900px;margin: auto; padding: 10px;" >
        <div style="border: 3px solid #666; position: relative;height: 980px;">
            <div style="width:100%;padding: 10px 0; height: 885px">
                <div style="text-align: right; padding-right: 60px; padding-top: 15px"><img style="width: 120px;" src="{{ base_path() }}/public/images/ticket_logo.png" alt=""></div>
                <div style="font-size: 25px; line-height: 150%; font-weight: bold; padding:10px 20px ; ">{{ $ticket_type_name }}  </div>
                <div style="background: #666;padding:20px; color: #ffffff; font-size: 20px; line-height: 100%; letter-spacing: 2px; font-weight: bold; text-align: center">
                    <div><img style="width: 50%" src="{{$qr_src}}" alt=""></div>
                    <div style="margin-top: 20px; font-size: 10px">{{$code}}</div>
                </div>
                <ul style="padding: 10px;">
                    <li style="display: flex; padding-bottom: 10px; padding-left: 15px">
                        <div style="width: 10px; height: 10px; background: #b6201d; margin-top: 4px"></div>
                        <div style="padding-left: 20px;"> Sử dụng vé này một lần duy nhất</div>
                    </li>
                    <li style="display: flex; padding-bottom: 10px;  padding-left: 15px">
                        <div style="width: 10px; height: 10px; background: #b6201d;margin-top: 4px"></div>
                        <div style="padding-left: 20px;"> Áp dụng cho tất cả các sân bay của Việt Nam(cả nội địa lẫn quốc tế)</div>
                    </li>
                    <li style="display: flex; padding-bottom: 10px;  padding-left: 15px">
                        <div style="width: 10px; height: 10px; background: #b6201d;margin-top: 4px"></div>
                        <div style="padding-left: 20px;">Hotline:<b>18008899</b> <br>Email: <b>support@consotio.com.vn</b></div>
                    </li>
                </ul>
            </div>
            <div>
                <div style="background: #b6201d; padding: 5px 15px; color: #ffffff; margin: 0 30px; font-style: italic"> Thời hạn sử dụng: {{ $limited_at }}</div>
                <div style="padding: 12px 50px 0 30px; text-align: center"> <b>Nơi cấp:</b> Công Ty Cổ Phần Consortio Services Việt Nam</div>
            </div>
        </div>
        <!--<div style="position: absolute; top: 0; left: 0; width: 25px; height: 100%; background: url('images/ticket__left.png')"></div>
        <div style="position: absolute; top: 0; right: 0; width: 25px; height: 100%; background: url('images/ticket__right.png');"></div> -->
        <div style="width: 58px; height: 54px; background: url('{{ base_path() }}/public/images/ticket_top_left.png'); position: absolute; top: 10px; left: 10px"></div>
        <div style="width: 58px; height: 54px; background: url('{{ base_path() }}/public/images/ticket_top_right.png'); position: absolute; top: 10px; right: 10px"></div>
        <div style="width: 58px; height: 50px; background: url('{{ base_path() }}/public/images/ticket_bottom_left.png'); position: absolute; bottom: 16px; left: 10px"></div>
        <div style="width: 58px; height: 50px; background: url('{{ base_path() }}/public/images/ticket_bottom_right.png'); position: absolute; bottom: 16px; right: 10px"></div>
    </div>
</div>
</body>
</html>
