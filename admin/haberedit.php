<?php
require_once '../settings.php';

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

$sqlHaberler = "SELECT * FROM haberler ORDER BY id DESC";
$resultHaberler = mysqli_query($conn, $sqlHaberler);

//fetching rowsz
$haberler = [];
if ($resultHaberler && mysqli_num_rows($resultHaberler) > 0) {
    while ($row = mysqli_fetch_object($resultHaberler)) {
        $haberler[] = $row;
    }
}

//ONE CIKAN HABER SQL

if (isset($_POST["oneCikanHaber"])) {
    // echo "merhaba, dunya";
    $oneCikanHaberId = htmlspecialchars($_POST["oneCikan"]);

    $stmt = $conn->prepare("UPDATE ayarlar SET one_cikan_haber = ? WHERE id = 1"); // Ayar tablosunda öne çıkan haber ID'sini güncelleyin
    $stmt->bind_param("i", $oneCikanHaberId);

    if ($stmt->execute()) {
        echo "<script>alert('Öne çıkan haber başarıyla güncellendi.');</script>";
    } else {
        echo "<script>alert('Bir hata oluştu.');</script>";
    }

}

if (isset($_POST['haberekle'])) {
    // echo "<script>alert('alarm');</script>";

    $baslik = htmlspecialchars($_POST['baslik']);
    $kategori_id = intval($_POST['kategori']);
    $icerik = ($_POST['icerik']);
    $demoicerik = substr(strip_tags($_POST['icerik']), 0, 100);

    // $uploadDir = "media/";
    // $filePath = $uploadDir . basename($_FILES['resim']['name']);

    $uploadFilePath = "media";
    $tmp_name = $_FILES["haberResim"]['tmp_name'];
    $name = $_FILES["haberResim"]['name'];
    $uz = substr($name, -4, 4);
    $randOne = rand(10000, 50000);
    $randTwo = rand(10000, 50000);
    $date = date("Ymdhms");
    $imageName = $randOne . $date . $randTwo . $uz;

    $imgToDatabaseLocation = $uploadFilePath . "/" . $imageName;
    // echo $tmp_name . " - " . $uploadFilePath . "/" . $imageName;

    if (move_uploaded_file($tmp_name, "../$uploadFilePath/$imageName")) {
        $stmt = mysqli_prepare($conn, "INSERT INTO haberler(baslik, kategori_id, icerik, demoicerik, resim) VALUES(?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sisss", $baslik, $kategori_id, $icerik, $demoicerik, $imgToDatabaseLocation);
        if (mysqli_stmt_execute($stmt)) {
            echo "<p>Dosya başarıyla yüklendi ve veritabanına kaydedildi: $filePath</p>";
        } else {
            echo "<p>Dosya veritabanına kaydedilemedi.</p>";
        }
        echo "File Uploaded";
    } else {
        echo "Not Yet Bro xd";
    }

    // if (move_uploaded_file($_FILES['resim']['tmp_name'], $filePath)) {
    // } else {
    //     echo "<p>Dosya yükleme başarısız.</p>";
    // }
    unset($_POST['baslik']);
    unset($_POST['link']);
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=haberedit");
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
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="container">
        <div class="row my-5">
            <div class="col bg-light text-dark p-5 rounded-3">
                <form action="" method="post">
                    <label for="">Öne Çıkan Haberi Seç</label>
                    <div class="mb-3">
                        <select class="form-control" name="oneCikan" required>
                            <?php foreach ($haberler as $haber) { ?>
                                <option value="<?= $haber->id ?>"><?= $haber->baslik ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="submit" class="btn btn-dark" name="oneCikanHaber" value="Güncelle">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row my-5">
            <div class="col bg-light text-dark p-5 rounded-3">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="baslik" placeholder="Haber Başlığı" required>
                    </div>

                    <div class="mb-3">
                        <select class='form-control' name="kategori" required>
                            <?php foreach ($kategoriler as $kategori) { ?>
                                <option value="<?= $kategori->id ?>"><?= $kategori->baslik ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <input name="haberResim" type="file" accept="image/*" required>
                    </div>

                    <div class="mb-3">
                        <textarea name="icerik" class="form-control" id="icerik" cols="30" rows="10"
                            placeholder="Haber içeriğini buraya yazın..."></textarea>
                    </div>

                    <button class="btn btn-dark" name="haberekle">Haber Ekle</button>
                </form>
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
    <script src="https://cdn.tiny.cloud/1/cxkki2m3wa21uuwshug04h2zl9kvc01pi89z20has1z6j8iy/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#icerik', // Textarea'nın ID'si
            plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
            toolbar_mode: 'floating',
            branding: false, // "Powered by Tiny" yazısını kaldırır
            height: 300 // Editörün yüksekliğini ayarlar
        });
    </script>

</body>

</html>