<?php
require_once '../settings.php';

$stmt = $conn->prepare("SELECT footer_text, social_links FROM footer WHERE id = 1");
$stmt->execute();
$result = $stmt->get_result();
$footer = $result->fetch_assoc();
$socialLinks = json_decode($footer['social_links'], true);

if (isset($_POST['updateFooter'])) {
    $footerText = htmlspecialchars($_POST['footer_text']);
    $socialMediaLinks = json_encode([
        'instagram' => $_POST['instagram'],
        'facebook' => $_POST['facebook'],
        'youtube' => $_POST['youtube']
    ]);

    $stmt = $conn->prepare("UPDATE footer SET footer_text = ?, social_links = ? WHERE id = 1");
    $stmt->bind_param("ss", $footerText, $socialMediaLinks);

    if ($stmt->execute()) {
        echo "<p>Footer başarıyla güncellendi.</p>";
    } else {
        echo "<p>Bir hata oluştu: " . $conn->error . "</p>";
    }
}

?>
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <form action="" method="post">
        <div class="mb-3">
            <label for="footer_text">Footer Metni</label>
            <textarea name="footer_text" id="footer_text" class="form-control" rows="3"
                required><?= htmlspecialchars($footer['footer_text']) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="instagram">Instagram Linki</label>
            <input type="url" name="instagram" id="instagram" class="form-control"
                value="<?= htmlspecialchars($socialLinks['instagram']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="facebook">Facebook Linki</label>
            <input type="url" name="facebook" id="facebook" class="form-control"
                value="<?= htmlspecialchars($socialLinks['facebook']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="youtube">YouTube Linki</label>
            <input type="url" name="youtube" id="youtube" class="form-control"
                value="<?= htmlspecialchars($socialLinks['youtube']) ?>" required>
        </div>
        <button type="submit" name="updateFooter" class="btn btn-primary">Kaydet</button>
    </form>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>