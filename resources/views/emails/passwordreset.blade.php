<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <title>Welcome</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        h1,h2,h3,h4,h5,h6,p {
            margin: 0px;
        }
        a {
            text-decoration: none;
            color: #354052;
        }
        a:hover {
            text-decoration: none;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #354052;
        }
        .wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 933px;
            width: 600px;
            background-color: #F5F5F5;
        }
        .inner {
            background-color: #FFF;
            height: 546px;
            width: 557px;
            margin-top: 60px;
            margin-bottom: 52px;
        }
        .logo {
            width: 214px;
            margin-top: 28px;
            margin-left: 48px;
            margin-bottom: 28px;
        }
        .key {
            display: block;
            height: 82.13px;
            margin-left: 48px;
            margin-bottom: 28px;
        }
        .greeting {
            font-size: 36px;
            line-height: 56px;
            font-weight: 500;
            margin-left: 48px;
        }
        .description {
            width: 420px;
            font-size: 19px;
            line-height: 29px;
            margin-left: 48px;
        }
        .verify {
            box-shadow: none;
            border: none;
            border-radius: 5px;
            font-size: 28px;
            line-height: 25px;
            font-weight: 600;
            color: #FFF;
            background-color: #01A3FA;
            width: 456px;
            height: 85px;
            margin-left: 48px;
            margin-top: 20px;
            cursor: pointer;
        }
        .disregard {
            color: #868686;
            font-size: 12px;
            line-height: 18px;
            margin-top: 15px;
            margin-left: 48px;
        }
        .feedback-prompt {
            font-size: 31px;
            line-height: 51px;;
        }
        .text-with-link {
            font-size: 13.6px;
            text-align: center;
        }
        .bottom {
            font-size: 13.6px;
            line-height: 22px;
        }
        .social-icon-wrapper {
            margin: 30px 0px;
        }
        .social-icon {
            margin: 5px;
            height: 24.7px;
        }
        @media (max-width: 768px) {
            .mb {
                display: block;
            }
            .lg {
                display: none;
            }
            .wrapper {
                background-color: transparent;
                height: 420px;
            }
            .inner {
                margin-top: 0px;
                margin-bottom: 0px;
                height: 411px;
                width: 360px;
                border: 1px solid #8D8D8D;
            }
            .logo {
                height: 43px;
                width: auto;
                margin-left: 28px;
            }
            .key {
                height: 34.19px;
                margin-left: 28px;
            }
            .greeting {
                font-size: 25px;
                line-height: 35px;
                margin-left: 28px;
                margin-bottom: 15px
            }
            .description {
                font-size: 16px;
                line-height: 25px;
                margin-left: 28px;
                width: 320px;
            }
            .verify {
                width: 308px;
                height: 48px;
                font-size: 17px;
                line-height: 15px;
                margin-left: 28px;
                margin-top: 15px;
            }
            .disregard {
                font-size: 8px;
                line-height: 12px;
                margin-left: 28px;
                margin-top: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="inner">
            <img src="https://digifigs.com/postslate-emails/images/Logo.png" alt="Postslate" class="logo" width="200px" height="70px">
            <!--<img src="https://digifigs.com/postslate-emails/images/key@2x.png" alt="" class="key" >-->
            <p class="greeting">Forgot your password?</p>
            <p class="description">Not to worry, we got you! Let’s get you a new password.</p>
            <a href="https://postslate.com/password-reset/{{TOKEN}}"><button class="verify">Reset Password</button></a>
            <p class="disregard">If you didn’t request to change your Postslate. password, you don’t have to do anything. So that’s easy. :) </p>
        </div>
        <h2 class="feedback-prompt lg">We'd love to hear from you!</h2>
        <p class="text-with-link lg">Help us improve by sharing your feedback in this short <u><a href="#">survey</a></u></p>
        <div class="social-icon-wrapper lg">
            <img src="https://digifigs.com/postslate-emails/images/facebook-2@2x.png" alt="" class="social-icon" >
            <img src="https://digifigs.com/postslate-emails/images/twitter@2x.png" alt="" class="social-icon" >
            <img src="https://digifigs.com/postslate-emails/images/instagram-glyph-1@2x.png" alt="" class="social-icon" >
        </div>
        <p class="bottom lg">Copyright © 2020 <a href="#"><b>Postslate</b></a> All Rights Reserved. </p>
        <p class="bottom lg"><a href="mailto:help@Postslate.com"><b>help@Postslate.com</b></a> | +234 90 1908 9009</p>
    </div>
</body>
</html>