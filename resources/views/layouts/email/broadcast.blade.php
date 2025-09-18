<!doctype html>
<html>
    <head>
        <meta content="width=device-width" name="viewport">
        <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
        <title>Turbo Charged Athletic</title>

        <style>
            p{
                margin:0;
            }
        </style>
    </head>
    <body bgcolor="#f7f7f7">
    <table align="center" style="border-collapse:collapse;padding:50px;font-family:Tahoma, Geneva, sans-serif;background-color:#ffffff;font-size:16px;border:1px solid #dee2e6;width:800px;">
        <tbody>
            <!-- Header -->
            <tr style="background-color:#a4adc1">
                <td align="center" style="padding:12px 10px;">
                    <img width="120" src="{{ url('assets/images/logo.png') }}" alt="Logo"/>
                </td>
            </tr>
        </tbody>

        <tbody>
            <!-- Greeting -->
            <tr>
                <td style="padding-top:20px;padding-bottom: 20px;">
                    <h2 style="color:#333;font-size:18px;padding-left:25px;margin:0;">
                        Hello {{ $data['first_name'] ?? 'User' }},
                    </h2>
                </td>
            </tr>

            <!-- Custom Message -->
            <tr>
                <td style="padding:5px 0 5px 25px;">
                    <div style="font-size:15px;color:#000;">
                        {!! $data['message'] ?? '' !!}
                    </div>
                </td>
            </tr>

            <!-- User Details Section -->
            <tr>
                <td style="padding:15px 0 5px 25px;">
                    <h3 style="color:#555;margin:0 0 10px 0;">User Details:</h3>
                    <p><strong>Username:</strong> {{ $data['user_name'] ?? '' }} </p>
                    <p><strong>Email Address:</strong> {{ $data['email'] ?? '' }}</p>
                    <p><strong>First Name:</strong> {{ $data['first_name'] ?? ''}}</p>
                    <p><strong>Last Name:</strong> {{ $data['last_name'] ?? ''}}</p>
                    <p><strong>Last Login Date:</strong> {{ $data['last_login_date'] ?? ''}}</p>
                </td>
            </tr>

            <!-- Subscription Details Section -->
            <tr>
                <td style="padding:15px 0 5px 25px;">
                    <h3 style="color:#555;margin:0 0 10px 0;">Subscription Details:</h3>

                    <p><strong>Signup Date:</strong> {{ $data['signup_date'] ?? ''}}</p>
                    <p><strong>Renewal Date:</strong> {{ $data['renewal_date'] ?? '' }}</p>
                    <p><strong>Next Billing Date:</strong> {{ $data['next_billing_date'] ?? ''}}</p>
                    <p><strong>Next Billing Amount:</strong> {{ $data['next_billing_amount'] ?? ''}}</p>
                    <p><strong>Last Billing Date:</strong> {{ $data['last_billing_date'] ?? ''}}</p>
                    <p><strong>Last Billing Amount:</strong> {{ $data['last_billing_amount'] ?? ''}}</p>
                </td>
            </tr>

            <!-- Workout Activity Section -->
            <tr>
                <td style="padding:15px 0 5px 25px;">
                    <h3 style="color:#555;margin:0 0 10px 0;">Workout Activity:</h3>
                    <p><strong>Last Workout Date:</strong> {{ $data['last_workout_date'] ?? ''}}</p>
                    <p><strong>Next Workout Date:</strong> {{ $data['next_workout_date'] ?? ''}}</p>
                </td>
            </tr>

            <!-- Reset Password -->
            <tr>
                <td style="padding:15px 0 5px 25px;">
                    <h3 style="color:#555;margin:0 0 10px 0;">Account Security:</h3>
                    <p>
                        <strong>Reset Password:</strong>
                        @isset($data['reset_password_url'])
                        <a href="{{ $data['reset_password_url'] }}" style="color:#007bff;">Click Here</a>
                        @endisset
                    </p>
                </td>
            </tr>


            <!-- Footer Message -->
            <tr>
                <td style="padding:15px 0 15px 25px;">
                    <p style="font-size:17px;line-height:30px;color:#000;">
                        Warm Regards,<br>
                        <strong>Turbo Charged Athletics Team</strong>
                    </p>
                </td>
            </tr>
        </tbody>

        <!-- Footer -->
        <tbody>
            <tr>
                <td style="padding:10px;background-color:#2D3342;">
                    <p style="font-size:14px;color:#fff;text-align:center;margin:0;">
                        © {{ date('Y') }} Turbo Charged Athletics.
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
