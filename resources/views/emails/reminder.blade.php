
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

    <div style="width: 100%; height: 100%; display: flex; justify-content: center; background-color: #f5f5f5; padding: 4rem 0;">
        <div style="max-width: 600px; margin: auto; padding-bottom: 4rem; background-color: #ffffff; font-family: 'Poppins', sans-serif; font-weight: 300;">
            <div style="width: 100%; height: 0.7rem; background-color: #042250; "></div>
            <div style="width: 100%;">
                <div style="width: fit-content; margin: 2rem auto;">
                    <img style="width: 3rem; height: auto;" src="https://postslate.com/api/images/logo.png" alt="Postslate" />
                </div>
                <div style="width: fit-content; margin: 2rem auto;">
                    <img style="width: 10rem; height: auto;" src="https://postslate.com/api/images/reminder.png" alt="Postslate" />
                </div>
                <div style="width: 90%; margin: auto;">
                    <p style="text-align: center; font-weight: 600; font-size: 150%;">Reminder!</p>
                    <p style="text-align: center; width: 80%; margin: auto;">Hi {{NAME}} , your subscription to your monthly plan will expire soon. {{PLAN}} plan at N{{PRICE}}/month starting {{DATE}}. To renew, please review your subscription here.</p>
                    <br>
                    <div style="width: 100%;">
                        <a href="https://postslate.com/upgrade" style="display: block; width: fit-content; margin: auto;">
                            <button style="padding: 0.7rem 2.5rem; font-size: 1.3rem; background-color: #01a3fa; color: #ffffff; border: none; border-radius: 0.3rem; margin-bottom: 2rem; cursor: pointer;">Renew</button>
                        </a>
                    </div>
                    <div style="height: 0.05rem; background-color: #B1AFAF; width: 55%; margin: auto; margin-bottom: 0.5rem;"></div>
                    <p style="text-align: center; width: 80%; margin: auto;">Thanks for choosing Postslate,<br>The postslate Team.</p>
                </div>
            </div>
            <div style="width: 100%; border-top: 0.05rem solid #B1AFAF; margin-top: 1.5rem; padding-top: 1.5rem;">
                <div style="width: fit-content; margin: auto; margin-bottom: 1rem;">
                    <a>
                        <img src='https://postslate.com/api/images/facebook_light.png' alt='' style="margin-right: 1.5rem;" />
                    </a>
                    <a>
                        <img src='https://postslate.com/api/images/instagram_light.png' alt='' style="margin-right: 1.5rem;" />
                    </a>
                    <a>
                        <img src='https://postslate.com/api/images/twitter_light.png' alt='' />
                    </a>
                </div>
                <p style="text-align: center; width: 80%; margin: auto; font-size: 70%; line-height: 150%; color: #B1AFAF">
                    If you need any help, please contact us <a href="mailto:info@postslate.com" style="color: #01a3fa;">info@postslate.com</a><br>Ikolaba street, Ibadan, Oyo state
                </p>
            </div>
        </div>
    </div>

</body>
</html>