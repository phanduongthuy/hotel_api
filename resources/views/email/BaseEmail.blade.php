<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Notification Email</title>
    <!-- Main Quill library -->
    <style type="text/css">
        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        .color-main {
            color: #1761b0 !important;
        }

        a {
            text-decoration: none;
        }
        .ql-editor {
            line-height: 1.35 !important;
        }
        .ql-editor p {
            margin: 0!important;
        }

    </style>
    <script src="https://cdn.quilljs.com/1.0.0/quill.min.js"></script>

    <!-- Theme included stylesheets -->
    <link href="https://cdn.quilljs.com/1.0.0/quill.snow.css" rel="stylesheet" />
    <link href="https://cdn.quilljs.com/1.0.0/quill.bubble.css" rel="stylesheet" />

    <!-- Core build with no theme, formatting, non-essential modules -->
    <link href="https://cdn.quilljs.com/1.0.0/quill.core.css" rel="stylesheet" />
    <script src="https://cdn.quilljs.com/1.0.0/quill.core.js"></script>
</head>
<body>
    <div class="ql-editor" style="white-space: normal !important;">
        {!!  $data['content'] !!}
    </div>
    <span>--</span>
    <br>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background: #fff;">
        <tr>
            <td style="padding-right: 7px; vertical-align: top; border-right: 3px solid rgb(255,102,0);">
                <div style="max-width: 200px"></div>
            </td>
            <td style="padding: 5px 10px; color: #000000">
                <h4 style="text-transform: uppercase; margin: 0">Văn phòng dịch vụ xử lý tài liệu Trans-Flash, Công ty Cổ phần V-Kookmin</h4>
                <p style="margin: 0">
                    <b>Số điện thoại:</b>
                    <span class="color-main"><a class="color-main" href="tel:035 938 3919">035.938.3919</a> | <a class="color-main" href="tal:(024) 7102 8989">(024) 7102 8989</a> </span>
                </p>
                <p style="margin: 0">
                    <b>Fax:</b>
                    <a href="" class="color-main">(024) 3795 9911 </a>
                </p>
                <p style="margin: 2px 0;"><b>Messenger:</b> <a href="http://m.me/transformflash.service" class="color-main">m.me/transformflash.service</a></p>
                <p style="margin: 2px 0;"><b>Address:</b> Công ty cổ phần V-Kookmin, Tầng 6, 131 Trần Phú, Hà Nội</p>
            </td>
        </tr>
    </table>
</body>
</html>
