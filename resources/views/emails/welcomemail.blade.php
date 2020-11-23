
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
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
        .mb {
            display: none;
        }
        .wrapper {
            padding: 3rem 0;
            width: 100%;
            background-color: #F5F5F5;
        }
        .inner {
            background-color: #FFF;
            height: auto;
            width: 60%;
            margin: auto;
            padding-bottom: 2rem;
        }
        .logo {
            width: 30%;
            margin-top: 2rem;
            margin-left: 3rem;
            margin-bottom: 1rem;
        }
        .greeting {
            font-size: 1.5rem;
            line-height: 2rem;
            margin-bottom: 2rem;
            margin-left: 3rem;
        }
        .description {
            width: 70%;
            font-size: 1rem;
            line-height: 1.5rem;
            margin-left: 3rem;
        }
        .verify {
            box-shadow: none;
            border: none;
            border-radius: 5px;
            font-size: 1.2rem;
            font-weight: 600;
            color: #FFF;
            background-color: #01A3FA;
            width: 15rem;
            height: 3rem;
            margin-left: 3rem;
            margin-top: 4rem;
            margin-bottom: 5rem;
            cursor: pointer;
        }
        .feedback-prompt {
            font-size: 1.7rem;
            line-height: 3rem;
        }
        .text-with-link {
            font-size: 0.7rem;
            text-align: center;
        }
        .bottom {
            font-size: 0.7rem;
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
                width: 100%;
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
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="inner">
            <img src="https://postpaddy.com/api/images/postpaddy_Full_Logo@4x.png" alt="postpaddy" class="logo" >
            <p class="greeting">Hey {{NAME}},<br>Welcome to <b>PostPaddy!</b></p>
            <p class="description">We are glad you decided to use our product. Before we get started, we’ll need to verify your email.</p>
            <a href="{{VERIFY_LINK}}"><button class="verify">Verify your email</button></a>
        </div>
        <div style="width: 100%;">
            <div style="width: fit-content; margin: auto; margin-bottom: 1rem;">
                <a>
                    <img src='https://postpaddy.com/api/images/facebook_light.png' alt='' style="margin-right: 1.5rem;" />
                </a>
                <a>
                    <img src='https://postpaddy.com/api/images/instagram_light.png' alt='' style="margin-right: 1.5rem;" />
                </a>
                <a>
                    <img src='https://postpaddy.com/api/images/twitter_light.png' alt='' />
                </a>
            </div>
            <p style="text-align: center; width: 80%; margin: auto; font-size: 70%; line-height: 150%; color: #B1AFAF">
                If you need any help, please contact us <a href="mailto:info@postpaddy.com" style="color: #01a3fa;">info@postpaddy.com</a><br>Ikolaba street, Ibadan, Oyo state
            </p>
        </div>
    </div>
</body>
</html>