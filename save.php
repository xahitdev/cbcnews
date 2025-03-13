<?php
// Veritabanı bağlantı bilgileri
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cbcnews";

// Dosya yükleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['resim'])) {
    // Veritabanına bağlan
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Bağlantıyı kontrol et
    if (!$conn) {
        die("Bağlantı hatası: " . mysqli_connect_error());
    }

    // Dosya yükleme dizini
    $uploadDir = "media/";
    $filePath = $uploadDir . basename($_FILES['resim']['name']);

    // Dosyayı yükle
    if (move_uploaded_file($_FILES['resim']['tmp_name'], $filePath)) {
        // Dosya yolunu veritabanına kaydet
        $stmt = mysqli_prepare($conn, "INSERT INTO haberler (resim) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $filePath);

        if (mysqli_stmt_execute($stmt)) {
            echo "<p>Dosya başarıyla yüklendi ve veritabanına kaydedildi: $filePath</p>";
        } else {
            echo "<p>Dosya veritabanına kaydedilemedi.</p>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<p>Dosya yükleme başarısız.</p>";
    }

    // Veritabanı bağlantısını kapat
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dosya Yükle</title>
</head>

<body>
    <h2>Dosya Yükle</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="resim" required>
        <button type="submit">Yükle</button>
    </form>
</body>

</html>