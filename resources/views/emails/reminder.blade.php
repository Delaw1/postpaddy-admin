
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        button:focus {
            outline: none;
        }
    </style>
</head>
<body>

    <!-- Note the texts wrapped with the <b> tag. -->
    <!-- You'll replace them with the appropriate ones.  -->
    <!-- For example, you would replace basic with whatever plan the user is on  -->
    <!-- Then of course you would replace Israel with the user's name. -->

    <div style="width: 100%; height: 100%; display: flex; justify-content: center; background-color: #f5f5f5; padding: 4rem 0;">
        <div style="max-width: 500px; margin: auto; padding-bottom: 5rem; background-color: #ffffff; font-family: 'Poppins', sans-serif; font-weight: 300;">
            <div style="width: 100%; height: 1rem; background-color: #042250; "></div>
            <div style="width: 100%;">
                <div style="width: 40%; margin: 2.5rem auto;">
                    <img style="width: 100%; height: auto;" src="https://postslate.com/api/images/Postslate_Full_Logo@4x.png" alt="Postslate" />
                </div>
                <div style="width: 90%; margin: auto;">
                    <p>Hey <b>{{NAME}}</b>,</p>
                    <p>Your subscription to the <b>{{PLAN}}</b> will expire soon, which means your access to the <b>{{PLAN}}</b> features would be cancelled soon. Only <b>{{DAYS}} days</b> left.</p>
                    <p>Please click the button below to renew your subscription. To enjoy more features, click <a href="https://postslate.com/upgrade" style="color: #01a3fa;">here</a> to upgrade your plan.</p>
                    <br>
                    @if(PLAN !== 'Freemium')
                    <a href="https://postslate.com/upgrade">
                        <button style="padding: 0.5rem 1.5rem; background-color: #01a3fa; color: #ffffff; border: none; border-radius: 0.3rem;">Renew subscription</button>
                    </a>
                    @endif
                    <br>
                    <br>
                    <p>Happy Posting!<br />Postslate Team</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>