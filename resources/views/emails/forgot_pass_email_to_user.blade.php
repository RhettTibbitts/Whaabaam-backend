<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Forgot Password</title>
        <link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400,500,600,700" rel="stylesheet">
        <style>
            *{margin: 0px; padding: 0px;}
            body{font-family: 'Montserrat', sans-serif; margin: 0; padding: 0; font-size: 18px; color: #555; vertical-align: baseline;}
            table tr td{padding: 15px; text-align: center;}
        </style>
        <table cellpadding="0" cellspacing="0" style="width: 750px; border-radius: 5px 5px 0 0px; overflow: hidden; margin: 30px auto; box-shadow: 0 0 10px 5px rgba(0,0,0,0.05);">
            <tbody>
                <tr>
                    <td style="background-color: #e06329; line-height: 1;">
                        <img src="{{ asset('public/images/system/logo-white.png') }}" alt=""/>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 30px;">
                        <img src="{{ asset('public/images/system/reset.png') }}" width="150" alt="" />
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 0;">
                        <h1 style="line-height: 1; color: #333; font-weight: 400; font-size: 35px;">Reset Your <span style="font-weight: 700;">Password</span></h1>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="color: #545065;"> {!! $content !!} </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="color: #545065;">
                        	If you have any question, please contact at support@whabaam.com
                    	</p>
                    </td>
                </tr>
                <tr>
                    <td align="left" style="text-align: left; padding: 40px;">
                        Thanks &amp; Regard,
                        <br>The Whaabhaam Team
                    </td>
                </tr>
            </tbody>
        </table>
    </head>
    <body>
    </body>
</html>