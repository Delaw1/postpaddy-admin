
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
        <div style="max-width: 500px; padding-bottom: 5rem; background-color: #ffffff; font-family: 'Poppins', sans-serif; font-weight: 300;">
            <div style="width: 100%; height: 1rem; background-color: #042250; "></div>
            <div style="width: 100%;">
                <div style="width: 40%; margin: 2.5rem auto;">
                    <img style="width: 100%; height: auto;" src="https://postslate.com/api/images/Postslate_Full_Logo@4x.png" alt="Postslate" />
                </div>
                <div style="width: 90%; margin: auto;">
                    <p>Hey <b>{{NAME}}</b>,</p>
                    <p>Your subscription to the <b>{{PLAN}}</b> plan was successful, which means you have access to the <b>{{PLAN}}</b> features for 30 days.</p>
                    <br>
                    <a href="#">
                        <button style="padding: 0.5rem 1.5rem; background-color: #01a3fa; color: #ffffff; border: none; border-radius: 0.3rem;">Start your plan</button>
                    </a>
                    
                    <br>
                    <br>
                    <p>Happy Posting!<br />Postslate Team</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>