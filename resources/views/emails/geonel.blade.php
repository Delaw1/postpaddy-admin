<!DOCTYPE html>
<html>
<head>
    <style>
        .content-wrapper {
            display: flex;
            justify-content: center;
            width: 100%;
            padding-top: 5rem;
        }
        table {
            display: flex;
            justify-content: center;
            font-family: arial, sans-serif;
            /* border-collapse: collapse; */
            width: 60%;
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
</head>
<body>

<h2></h2>
    <div class="content-wrapper">
        <table>
            <tr>
                <td>Name</td>
                <td>{{ $details['name'] }}</td>
            </tr>
            <tr>
                <td>Email Address</td>
                <td><a href="mailto:{{ $details['email_from'] }}">{{ $details['email_from'] }}</a></td>
            </tr>
            <tr>
                <td>Message</td>
                <td>{{ $details['message'] }}</td>
            </tr>
        </table>
    </div>
</body>
</html>