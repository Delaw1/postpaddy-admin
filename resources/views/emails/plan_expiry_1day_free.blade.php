<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Display:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        button:focus {
            outline: none;
        }
    </style>
</head>
<body>

    <div style="width: 100%; height: 100%; display: flex; justify-content: center; background-color: #f5f5f5; padding: 4rem 0;">
        <div style="max-width: 600px; margin: auto; padding-bottom: 4rem; background-color: #ffffff; font-family: 'Red Hat Display', sans-serif; font-weight: 300;">
            <div style="width: 100%; height: 0.3rem; background-color: #042250; "></div>
            <div style="width: 100%;">
                <div style="width: 80%; margin: 2rem auto;">
                    <img style="width: auto; height: 2rem;" src="https://postpaddy.com/api/images/logo_postpaddy.png" alt="PostPaddy" />
                </div>

                <div style="width: 80%; margin: auto;">
                    <p>Hi <span style="font-weight: 500;">{{NAME}}</span>,</p>
                    <p style="font-weight: 500; font-size: 110%;">Your free trial to PostSlate ends tomorrow!</p>
                    <p>
                        Now’s just the right time to upgrade to one of our 
                        value-priced plans if you want to keep enjoying full 
                        access to PostPaddy’s premium features.                        
                    </p>
                    <br>
                    <div style="width: 100%;">
                        <!-- take user to sign in page with query string upgrade=true -->
                        <a href="https://postpaddy.com/sign-in?upgrade=true" style="display: block; width: 100%; margin: auto;">
                            <button style="width: 100%; padding: 0.7rem 0; font-size: 100%; background-color: #01a3fa; color: #ffffff; border: none; border-radius: 0.3rem; margin-bottom: 1rem; cursor: pointer;">Subscribe to a Plan</button>
                        </a>
                    </div>
                    <p>If you have extra inquiries about any of our pricing plans or
                        using PostPaddy, hit the <span style="font-weight: 500;">“reply”</span> button and send us a 
                        message, we’re available to help.
                    </p>
                    <br />
                    <p>Best Regards,<br />The PostPaddy Team.</p>
                </div>
            </div>
            <div style="width: 80%; margin: auto; border-top: 0.05rem solid #e8e8e8; margin-top: 2rem; padding-top: 2.5rem;">
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
                <p style="text-align: center; width: 80%; margin: auto; font-size: 70%; line-height: 150%; color: #8d8d8d">
                    If you need any help, please contact us <a href="mailto:info@postpaddy.com" style="color: #01a3fa;">info@postpaddy.com</a><br>No. 24, Ibikunle Avenue, Off Upper Adeyi, Old Bodija.
                </p>
            </div>
        </div>
    </div>

</body>
</html>