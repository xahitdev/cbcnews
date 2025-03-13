<?php
require_once '../settings.php';

session_start();


if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Query to select all rows from "kategoriler" table
$sqlKategoriler = "SELECT * FROM kategoriler";
$resultKategoriler = mysqli_query($conn, $sqlKategoriler);

// Fetch all rows as an associative array
$kategoriler = [];
if ($resultKategoriler && mysqli_num_rows($resultKategoriler) > 0) {
    while ($row = mysqli_fetch_object($resultKategoriler)) {
        $kategoriler[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <header class="bg-dark">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <a class="navbar-brand fw-bold" href="#">
                    <img src="../media/logo.png" alt="cbcnews" href="" style="height: 50px">
                    <span style="font-size: 20px; text-decoration: none; color: inherit;">CBC News</span>
                </a>
                <div class="ms-auto">
                    <span class="text-white" style="font-size: 20px;">Admin Paneli</span>
                </div>
            </nav>
        </div>
    </header>

    <div class="container-fluid my-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="bg-dark text-white p-4 rounded-3 shadow-sm">
                    <h5 class="mb-4">Menü</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3"><a href="admin.php?page=genelayar"
                                class="text-white text-decoration-none">Genel Ayarlar</a></li>
                        <li class="mb-3"><a href="admin.php?page=navbaredit"
                                class="text-white text-decoration-none">Navbar Düzenle</a></li>
                        <li class="mb-3"><a href="admin.php?page=haberedit"
                                class="text-white text-decoration-none">Haber Ekle</a></li>
                        <li class="mb-3"><a href="admin.php?page=haberduzenle"
                                class="text-white text-decoration-none">Haber Düzenle</a></li>
                        <li class="mb-3"><a href="logout.php" class="text-danger text-decoration-none">Çıkış Yap</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-9">
                <div id="main-content" class="p-4 bg-light rounded-3 shadow-sm">
                    <?php
                    // Parametreyi al
                    $page = isset($_GET['page']) ? $_GET['page'] : 'genelayar';

                    // Dinamik içerik yükleme
                    switch ($page) {
                        case 'genelayar':
                            include 'genelayar.php';
                            break;
                        case 'navbaredit':
                            include 'navbaredit.php';
                            break;
                        case 'haberedit':
                            include 'haberedit.php';
                            break;
                        case 'haberduzenle':
                            include 'haberduzenle.php';
                            break;
                        case 'settings':
                            include 'settings.php';
                            break;
                        case 'users':
                            include 'users.php';
                            break;
                        case 'reports':
                            include 'reports.php';
                            break;
                        default:
                            echo '<h5>Sayfa Bulunamadı</h5>';
                            break;
                    }
                    ?>
                </div>
            </div>

        </div>

    </div>

    <style>
        body {
            background: #f8f9fa;
        }

        .bg-dark {
            background-color: #343a40 !important;
        }

        .sidebar ul li a:hover {
            text-decoration: underline;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>