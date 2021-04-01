<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml" style="height: 100%;">
<head>
<!--[if gte mso 9]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<meta content="width=device-width" name="viewport"/>
<!--[if !mso]><!-->
<meta content="IE=edge" http-equiv="X-UA-Compatible"/>
<!--<![endif]-->
    <title>Smart Publishers</title>
<!--[if !mso]><!-->
<link href="https://fonts.googleapis.com/css?family=Cormorant+Garamond|Roboto&display=swap" rel="stylesheet" type="text/css"/>
<!--<![endif]-->
<style type="text/css">

</style>
</head>

<body style="box-sizing: border-box; height: 100%; margin: 0; padding: 0;">
    <div style="width: 100%; height: 100%; position: relative;">
        <div style="max-width: 500px; margin: auto;">
            <div style="position: relative;">
                <div style="text-align: center;">
                    <img class="logo-img" src="{{ asset('assets/img/logo-complete-1.png') }}" style="width: 200px;">
                </div>
            </div>
            <div style="border: solid 1px #ccc; border-radius: 5px; padding: 20px; font-family: Arial, Helvetica, sans-serif;">
                <h2>{{ __('globals.mail.forgot_password_title') }}</h2>
                <p style="line-height: 24px;">{{ __('globals.mail.forgot_password_content') }}</p>
                <a href="{{ $link }}" rel="noopener" style="text-decoration: underline; color: #0F7173;font-size: 15px;" target="_blank">{{ __('globals.mail.validate_email') }}</a>
            </div>
            <div style="margin-top: 10px;text-align: center;">
                © Copyright 2020 <a href="{{ url('/') }}">Smart Publishers</a>
            </div>
        </div>
    </div>
</body>

</html>