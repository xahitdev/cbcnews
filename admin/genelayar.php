<?php
require_once '../settings.php';

if (isset($_POST['logoDegis'])) {

    $uploadFilePath = "media";
    $tmp_name = $_FILES["logoResim"]['tmp_name'];
    $name = $_FILES["logoResim"]['name'];
    $uz = substr($name, -4, 4);
    $randOne = rand(10000, 50000);
    $randTwo = rand(10000, 50000);
    $date = date("Ymdhms");
    $imageName = $randOne . $date . $randTwo . $uz;

    $imgToDatabaseLocation = $uploadFilePath . "/" . $imageName;
    // echo $tmp_name . " - " . $uploadFilePath . "/" . $imageName;
    if (move_uploaded_file($tmp_name, "../$uploadFilePath/$imageName")) {
        // Update sorgusu düzeltildi
        $stmt = mysqli_prepare($conn, "UPDATE ayarlar SET logo = ? WHERE id = 1");
        mysqli_stmt_bind_param($stmt, "s", $imgToDatabaseLocation);

        if (mysqli_stmt_execute($stmt)) {
            echo "<p>Dosya başarıyla yüklendi ve veritabanına kaydedildi: $imgToDatabaseLocation</p>";
        } else {
            echo "<p>Dosya veritabanına kaydedilemedi.</p>";
        }

        echo "File Uploaded";
    } else {
        echo "Not Yet Bro xd";
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?page=genelayar");

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
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container-fluid my-4">
        <div class="row">
            <!-- Main Content -->
            <div class="col-md-12">
                <div class="row g-4">
                    <!-- Logo Değiştir Bölümü -->
                    <div class="col-md-4 d-flex align-items-stretch">
                        <div class="bg-light text-dark p-4 rounded-3 shadow-sm text-center w-100">
                            <h5 class="mb-4">LOGO Değiştir</h5>
                            <form action="" method="post" enctype="multipart/form-data">
                                <input name="logoResim" type="file" accept="image/*" class="form-control mb-3" required>
                                <input type="submit" name="logoDegis" value="Değiştir" class="btn btn-primary">
                            </form>
                        </div>
                    </div>

                    <!-- Profil Durumu -->
                    <div class="col-md-4 d-flex align-items-stretch">
                        <div class="bg-light text-dark p-4 rounded-3 shadow-sm text-center w-100">
                            <img src="../media/usericon.png" alt="User Icon" class="mb-3 rounded-circle"
                                style="width: 100px; height: 100px;">
                            <p class="mb-0 fw-bold">Aktif Profil:</p>
                            <p class="text-primary fs-5">
                                <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Çıkış Bölümü -->
                    <div class="col-md-4 d-flex align-items-stretch">
                        <div class="bg-light text-dark p-4 rounded-3 shadow-sm text-center w-100">
                            <h5 class="mb-4">Oturum Yönetimi</h5>
                            <a href="logout.php" class="btn btn-danger">Çıkış Yap</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        body {
            background: #ffffff;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

</body>

</html>