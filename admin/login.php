<?php
require '../settings.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare('SELECT id, isim, sifre FROM admin WHERE isim= ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        if ($password === $admin['sifre']) {
            session_start();
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['isim'];
            header('Location: admin.php'); // Redirect to the admin dashboard
            exit();
        } else {
            // echo 'Şifre veya kullanıcı adı hatalı. Lütfen tekrar deneyiniz.';
            echo "<script>alert('Kullanıcı adı veya şifre hatalı.');</script>";
        }
    } else {
        echo "<script>alert('Kullanıcı adı veya şifre hatalı.');</script>";

    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .form-control {
            border-radius: 5px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 5px;
        }
    </style>
    </head>

    <body>
        <div class="form-container">
            <form action="login.php" method="POST">
                <h2 class="text-center mb-4">CBC News Admin Giriş</h2>
                <div class="form-group mb-3">
                    <label for="username">Kullanıcı Adı:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group mb-3">
                    <label for="password">Kullanıcı Şifre:</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-block">Giriş</button>
                </div>
            </form>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
    </body>

</html>