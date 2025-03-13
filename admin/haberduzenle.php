<?php
require_once '../settings.php';

// Haber seçimi
if (!isset($_GET['post_id'])) {
    $stmt = $conn->prepare("SELECT id, baslik FROM haberler ORDER BY id DESC");
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h2>Düzenlenecek Haberi Seçin</h2>";
    echo "<form action='' method='get'>";
    echo "<div class='mb-3'>";
    echo "<label for='post_id' class='form-label'>Haber Seç</label>";
    echo "<select name='post_id' id='post_id' class='form-select'>";
    while ($haber = $result->fetch_assoc()) {
        echo "<option value='" . $haber['id'] . "'>" . nl2br($haber['baslik']) . "</option>";
    }
    echo "<input type='text' name='page' value='haberduzenle' hidden>";
    echo "</select>";
    echo "</div>";
    echo "<button type='submit' class='btn btn-primary'>Düzenle</button>";
    echo "</form>";
    exit;
}

// Seçilen haberin düzenleme formu
$post_id = intval($_GET['post_id']);
$stmt = $conn->prepare("SELECT * FROM haberler WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $haber = $result->fetch_assoc();
} else {
    echo "<p>Haber bulunamadı.</p>";
    exit;
}
if (isset($_POST['deletePost'])) {
    $post_id = intval($_GET['post_id']);

    $stmtCheck = $conn->prepare("SELECT id FROM haberler WHERE id = ?");
    $stmtCheck->bind_param("i", $post_id);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();

    if ($result->num_rows > 0) {
        $stmtDelete = $conn->prepare("DELETE FROM haberler WHERE id = ?");
        $stmtDelete->bind_param("i", $post_id);

        if ($stmtDelete->execute()) {
            echo "<p>Post başarıyla silindi.</p>";
        } else {
            echo "<p>Post silinirken bir hata oluştu.</p>";
        }
    } else {
        echo "<p>Post bulunamadı.</p>";
    }
}
if (isset($_POST['updatePost'])) {
    $baslik = htmlspecialchars($_POST['baslik']);
    $icerik = $_POST['icerik'];

    if (!empty($_FILES['resim']['name'])) {
        $uploadDir = "media/";
        $tmp_name = $_FILES["resim"]['tmp_name'];
        $name = $_FILES["resim"]['name'];
        $uz = substr($name, strrpos($name, '.'));
        $rand = rand(10000, 50000);
        $imageName = $rand . date("Ymdhms") . $uz;
        $uploadFilePath = $uploadDir . $imageName;

        if (move_uploaded_file($tmp_name, '../'.$uploadFilePath)) {
            if (!empty($haber['resim']) && file_exists($haber['resim'])) {
                unlink($haber['resim']);
            }

            $stmt = $conn->prepare("UPDATE haberler SET baslik = ?, icerik = ?, resim = ? WHERE id = ?");
            $stmt->bind_param("sssi", $baslik, $icerik, $uploadFilePath, $post_id);
        } else {
            echo "<p>Resim yüklenirken bir hata oluştu.</p>";
            exit;
        }
    } else {
        $stmt = $conn->prepare("UPDATE haberler SET baslik = ?, icerik = ? WHERE id = ?");
        $stmt->bind_param("ssi", $baslik, $icerik, $post_id);
    }

    if ($stmt->execute()) {
        echo "<p>Haber başarıyla güncellendi.</p>";
    } else {
        echo "<p>Güncelleme sırasında bir hata oluştu.</p>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <form action="" method="post" enctype="multipart/form-data" style="padding-bottom: 50px!important;">
        <div class="mb-3">
            <label for="baslik">Başlık</label>
            <input type="text" name="baslik" id="baslik" class="form-control"
                value="<?= nl2br($haber['baslik']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="icerik">İçerik</label>
            <textarea name="icerik" id="icerik" class="form-control" rows="5"
                required><?= htmlspecialchars($haber['icerik']) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="resim">Resim</label>
            <input type="file" name="resim" id="resim" class="form-control">
            <?php if (!empty($haber['resim'])): ?>
                <img src="<?= '../'.htmlspecialchars($haber['resim']) ?>" alt="Haber Resmi" class="img-fluid mt-2"
                    style="max-width: 200px;">
            <?php endif; ?>
        </div>
        <button style="float:right; margin-left: 20px;" type="submit" name="updatePost" class="btn btn-primary">Güncelle</button>
        <button style="float:right;" type="submit" name="deletePost" class="btn btn-danger">Postu Sil</button>


    </form>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>