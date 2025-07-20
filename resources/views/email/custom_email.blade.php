<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        /* Add custom classes and styles that you want inlined here */
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        .card {
            margin-top: 10px;
            border: none;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 20px;
        }

        /* Your custom styles */
        .logo {
            width: 100px;
            height: auto;
        }

        .btn-verify {
            background-color: #663366;
            border-radius: 3px;
            color: #fff;
            font-size: 18px;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            width: 100%;
            padding: 19px 0;
            box-sizing: border-box;
        }

        .btn-verify:hover {
            background-color: #492949;
        }

        hr {
            background-color: #484848;
            margin-top: 30px;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="card my-10">
        <div class="card-body">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td align="center"><img alt="marshallGroup" src="{{asset('images/logos/mg-logo.png')}}" class="logo" /></td>
                </tr>
            </table>

            <h1 class="h3 mb-2 text-center">The Marshall Group Verification</h1>
            <h5 class="text-teal-700 text-center mb-3">Dear {{$user->first_name}} {{$user->last_name}}!</h5>


            <div class="space-y-3">
                <p class="text-gray-700">Thank you for signing up with The Marshall Group! To ensure the security of your account and to keep you informed about our latest updates, we need to verify your email address.</p>
                <p class="text-gray-700">Once you verify your email, please login to the app and complete creating your profile.</p>
                <p class="text-gray-700">If you didn't sign up for The Marshall Group account, or if you believe this email was sent in error, please disregard this message.</p>
{{--                <a href="{{$verificationUrl}}" class="btn-verify mb-3" target="_blank">Verify</a>--}}

                <p class="text-gray-700">If you're having trouble clicking the "Verify" button, copy and paste the URL below into your web browser: <a href="{{$verificationUrl}}">{{$verificationUrl}}</a></p>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js (optional) -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>
