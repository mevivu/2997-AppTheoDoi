<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>403 Access Denied</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            text-align: center;
            background-color: white;
            padding: 60px;
            max-width: 500px;
        }

        h1 {
            font-size: 120px;
            margin: 0;
            color: #206bc4;
            font-weight: 600;
        }

        p {
            font-size: 22px;
            color: #555;
            margin: 15px 0;
        }

        a {
            color: #206bc4;
            text-decoration: none;
            font-weight: 600;
        }

        .illustration {
            width: 100px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="container">
        <img class="illustration" src="{{ asset('assets/images/lock.png') }}" alt="Access Denied">
        <h1>403</h1>

        <p>
            Bạn không có quyền truy cập nội dung này.
        </p>
        <a href="{{ url()->previous() }}">Quay Lại</a>
    </div>

</body>

</html>
