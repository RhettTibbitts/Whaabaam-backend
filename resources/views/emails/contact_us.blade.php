<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Contact Us</title>
        <link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400,500,600,700" rel="stylesheet">
        <style>
            *{margin: 0px; padding: 0px;}
            body{font-family: 'Montserrat', sans-serif; margin: 0; padding: 0; font-size: 18px; color: #555; vertical-align: baseline;}
            table tr td{padding: 15px;}
        </style>
        <table cellpadding="0" cellspacing="0" style="width: 750px; border-radius: 5px 5px 0 0px; overflow: hidden; margin: 30px auto; box-shadow: 0 0 10px 5px rgba(0,0,0,0.05);">
            <tbody>
                <tr>
                    <td align="center" style="background-color: #e06329; line-height: 1;">
                        <img src="{{ asset('public/images/system/logo-white.png') }}" alt=""/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="color: #000; padding-left: 15px; padding-top: 35px; font-size: 24px; font-weight: 700;">You have received a new contact request.</p>
                    </td>
                </tr>
                <tr>
                    <td>
                    	{!! $content !!}
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