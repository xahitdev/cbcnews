<?php
require_once '../settings.php';

// Veritabanından menü öğelerini sıraya göre çekiyoruz
$sql = "SELECT * FROM kategoriler ORDER BY `order` ASC";
$result = $conn->query($sql);

// Menü öğelerini bir diziye aktarıyoruz
$kategoriler = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $kategoriler[] = $row;
    }
}

if (isset($_POST['add_menu'])) {
    $baslik = $_POST['baslik'];
    $link = $_POST['link'];

    // Mevcut en büyük `order` değerini bul ve yenisine +1 ekle
    $result = $conn->query("SELECT MAX(`order`) AS max_order FROM kategoriler");
    $maxOrderRow = $result->fetch_assoc();
    $newOrder = $maxOrderRow['max_order'] + 1;

    // Yeni menü öğesini ekle
    $stmt = $conn->prepare("INSERT INTO kategoriler (baslik, link, `order`) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $baslik, $link, $newOrder);

    if ($stmt->execute()) {
        echo "<script>alert('Yeni menü öğesi başarıyla eklendi.'); window.location.reload();</script>";
    } else {
        echo "<script>alert('Bir hata oluştu: " . $conn->error . "');</script>";
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=navbaredit");
}


if (isset($_POST['delete'])) {
    $deleteId = $_POST['delete_id'];

    // Silinecek öğenin mevcut `order` değerini al
    $sql = "SELECT `order` FROM kategoriler WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $result = $stmt->get_result();
    $deletedOrder = $result->fetch_assoc()['order'];

    // Öğeyi sil
    $sql = "DELETE FROM kategoriler WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();

    // Silinen öğeden sonraki tüm öğelerin `order` değerlerini güncelle
    $sql = "UPDATE kategoriler SET `order` = `order` - 1 WHERE `order` > ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $deletedOrder);
    $stmt->execute();

    echo "<script>alert('Menü öğesi başarıyla silindi ve sıralama güncellendi.'); window.location.reload();</script>";
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=navbaredit");
}


if (isset($_POST['move_up'])) {
    $moveUpId = $_POST['move_up_id'];

    // Mevcut öğeyi al
    $sql = "SELECT id, `order` FROM kategoriler WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $moveUpId);
    $stmt->execute();
    $result = $stmt->get_result();
    $currentItem = $result->fetch_assoc();

    // Bir üstteki öğeyi al
    $sql = "SELECT id, `order` FROM kategoriler WHERE `order` < ? ORDER BY `order` DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $currentItem['order']);
    $stmt->execute();
    $result = $stmt->get_result();
    $previousItem = $result->fetch_assoc();

    // Eğer bir üstteki öğe varsa
    if ($previousItem) {
        // Order değerlerini değiştir
        $sql = "UPDATE kategoriler SET `order` = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        // Şu anki öğe yeni sıraya geçiyor
        $stmt->bind_param("ii", $previousItem['order'], $currentItem['id']);
        $stmt->execute();

        // Bir üstteki öğe eski sıraya geçiyor
        $stmt->bind_param("ii", $currentItem['order'], $previousItem['id']);
        $stmt->execute();

        echo "<script>alert('Öğe yukarı taşındı.'); window.location.reload();</script>";
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=navbaredit");
}

if (isset($_POST['move_down'])) {
    $moveDownId = $_POST['move_down_id'];

    // Mevcut öğeyi al
    $sql = "SELECT id, `order` FROM kategoriler WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $moveDownId);
    $stmt->execute();
    $result = $stmt->get_result();
    $currentItem = $result->fetch_assoc();

    // Bir alttaki öğeyi al
    $sql = "SELECT id, `order` FROM kategoriler WHERE `order` > ? ORDER BY `order` ASC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $currentItem['order']);
    $stmt->execute();
    $result = $stmt->get_result();
    $nextItem = $result->fetch_assoc();

    // Eğer bir alttaki öğe varsa
    if ($nextItem) {
        // Order değerlerini değiştir
        $sql = "UPDATE kategoriler SET `order` = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        // Şu anki öğe yeni sıraya geçiyor
        $stmt->bind_param("ii", $nextItem['order'], $currentItem['id']);
        $stmt->execute();

        // Bir alttaki öğe eski sıraya geçiyor
        $stmt->bind_param("ii", $currentItem['order'], $nextItem['id']);
        $stmt->execute();

        echo "<script>alert('Öğe aşağı taşındı.'); window.location.reload();</script>";
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?page=navbaredit");
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
    <div class="container my-5">
        <h2 class="text-center mb-4">Navbar Düzenleme Ekranı</h2>
        <table class="table table-bordered">
            <thead>
                <tr class="bg-dark text-white">
                    <th scope="col">Sıra</th>
                    <th scope="col">Başlık</th>
                    <th scope="col">Link</th>
                    <th scope="col">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kategoriler as $index => $item): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($item['baslik']) ?></td>
                        <td><?= htmlspecialchars($item['link']) ?></td>
                        <td>
                            <form method="post" action="" class="d-inline">
                                <input type="hidden" name="delete_id" value="<?= $item['id'] ?>">
                                <button type="submit" name="delete" class="btn btn-danger btn-sm">Sil</button>
                            </form>
                            <form method="post" action="" class="d-inline">
                                <input type="hidden" name="move_up_id" value="<?= $item['id'] ?>">
                                <button type="submit" name="move_up" class="btn btn-secondary btn-sm">Yukarı</button>
                            </form>
                            <form method="post" action="" class="d-inline">
                                <input type="hidden" name="move_down_id" value="<?= $item['id'] ?>">
                                <button type="submit" name="move_down" class="btn btn-secondary btn-sm">Aşağı</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Yeni Menü Öğesi Ekleme -->
        <h4 class="mt-5">Yeni Menü Öğesi Ekle</h4>
        <form method="post" action="">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" name="baslik" class="form-control" placeholder="Başlık" required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="link" class="form-control" placeholder="Link" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" name="add_menu" class="btn btn-success w-100">Ekle</button>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>