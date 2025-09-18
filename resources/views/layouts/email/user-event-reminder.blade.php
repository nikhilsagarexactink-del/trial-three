<!doctype html>
<html>
    <head>
        <meta content="width=device-width" name="viewport">
        <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
        <title>Turbo Charged Athletic</title>
    </head>
    <body bgcolor="#f7f7f7">
        <table align="center"  style="border-collapse:collapse;padding:50px; font-family:Tahoma, Geneva, sans-serif;background-color:#ffffff;font-size:16px;border:1px solid #dee2e6;width:800px;" >
            <tbody>
                <tr style="background-color:#a4adc1">
                    <td colspan="1" align="center" style="padding:12px 10px;"  ><img width="120" src="{{ url('assets/images/logo.png') }}"/></td>
                </tr>
            </tbody>
            <tbody>
            <tr>
                <td style=" padding-top:20px;padding-bottom: 20px;">
                    <h2 style="color:#333;font-size:18px;font-family:Tahoma, Geneva, sans-serif;padding-left: 25px; padding-top:10px;padding-bottom: 0;margin-top:0;margin-bottom:0;">
                        Hello {{$emailData['name']}},
                    </h2>
                </td>
            </tr> 
            <tr>
                <td style="padding-top:5px;padding-bottom:5px;">
                   <div style="font-size:15px;color:#000;font-family:Tahoma, Geneva, sans-serif;padding-left: 25px;margin-top:0;margin-bottom:5px;">
                    <p>{{$emailData['message']}}</p>
                   </div> 
                </td>
            </tr>
             
            <tr>
                <td style="padding-top:15px;padding-bottom:15px;">
                    <p style="font-size:17px;color:#000;font-family:Tahoma, Geneva, sans-serif;line-height:30px;padding-left: 25px;margin-top:0; margin-bottom:0;">Warm Regards,<br>
                        Turbo Charged Athletics Team</p>
                </td>
            </tr>
            </tbody>
            <tbody>
                <tr>
                    <td style="padding-top:10px;padding-bottom:10px;background-color:#2D3342;">
                        <p style="font-size:14px;color:#fff;font-family:Tahoma, Geneva, sans-serif;text-align:center; margin: 0;">© {{date('Y')}} Turbo Charged Athletic POWERED BY.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
