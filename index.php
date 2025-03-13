<?php
require_once 'settings.php';

// KATEGORILER - NAVBAR - MENU
// sayfaya gelecek icerigi limitleme


$sqlLogo = 'SELECT logo from ayarlar WHERE id=1';
$resultLogo = mysqli_query($conn, $sqlLogo);
if ($resultLogo->num_rows > 0) {
	$ayar = $resultLogo->fetch_assoc();
	$logo = $ayar['logo']; // Logo dosya yolunu alıyoruz
} else {
	$logo = 'media/logo.png'; // Varsayılan bir logo kullanılabilir
}

$sqlKategoriler = "SELECT * FROM kategoriler ORDER BY 'order' ASC";
$resultKategoriler = $conn->query($sqlKategoriler);

$kategoriler = [];

if ($resultKategoriler->num_rows > 0) {
	while ($row = $resultKategoriler->fetch_assoc()) {
		$kategoriler[] = $row;
	}
}

// HABERLER


////////////////////////////////////////////////////////////

$targetCategory = []; // isset($_GET["kategori_id"])
$targetBaslik = "";
if(isset($_GET["kategori_id"])){
	$sqlTargetCategory = "SELECT * FROM kategoriler WHERE id = ?";
	$stmt = $conn->prepare($sqlTargetCategory);
	$stmt->bind_param("i", $_GET["kategori_id"]);
	$stmt->execute();
	$resultCat = $stmt->get_result();


	if ($resultCat->num_rows > 0) {
		while ($row = $resultCat->fetch_assoc()) {
			$targetCategory[] = $row;
		}
	}

	foreach($targetCategory as $item){
		$targetBaslik = $item["baslik"];
	}
}




$haberlerNew = [];
$limit = 6; // Sayfa başına 6 haber gösterilecek
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Geçerli sayfa numarası
$offset = ($page - 1) * $limit; // Hangi kayıttan başlayacağımızı hesaplama


if(is_numeric($page) && is_numeric($limit)){
	$starting_limit = ($page-1)*$limit;
}
                        
if(!is_numeric($page)){
	$starting_limit = 1;
}

$sqlVar = "SELECT * FROM haberler ORDER BY id DESC LIMIT ?, ?";
$catVar = false;

if(isset($_GET["kategori_id"])){
	$sqlVar = "SELECT * FROM haberler WHERE kategori_id = ? ORDER BY id DESC LIMIT ?, ?";
	$catVar = true;
}

$sqlHaberlerNew = $sqlVar;
$stmt = $conn->prepare($sqlHaberlerNew);
if($catVar){
	$stmt->bind_param("iii", $_GET["kategori_id"],$starting_limit, $limit);
}else{
	$stmt->bind_param("ii", $starting_limit, $limit);
}
$stmt->execute();
$resultHaberler = $stmt->get_result();


if ($resultHaberler->num_rows > 0) {
	while ($row = $resultHaberler->fetch_assoc()) {
		$haberlerNew[] = $row;
	}
}


$sqlVarCount = "SELECT * FROM haberler";
if($catVar){
	$sqlVarCount = "SELECT * FROM haberler WHERE kategori_id = ?";
}
$stmt = $conn->prepare($sqlVarCount);
if($catVar) $stmt->bind_param("i", $_GET['kategori_id']);
$stmt->execute();
$totalHaberler = $stmt->get_result()->num_rows;
$totalPages = ceil($totalHaberler / $limit);


// Random
$randomHaberler = [];
$sqlHaberlerRandoms = "SELECT * FROM haberler ORDER BY RAND() LIMIT 4";
$stmt = $conn->prepare($sqlHaberlerRandoms);
$stmt->execute();
$resultRandom = $stmt->get_result();


if ($resultRandom->num_rows > 0) {
	while ($row = $resultRandom->fetch_assoc()) {
		$randomHaberler[] = $row;
	}
}


////////////////////////////////////////////


//ONE CIKAN HABER
$sqlOneCikan = "SELECT one_cikan_haber FROM ayarlar WHERE id=1";
$oneCikanResult = $conn->query($sqlOneCikan);

if ($oneCikanResult->num_rows > 0) {
	$oneCikanHaberId = $oneCikanResult->fetch_assoc()['one_cikan_haber'];
} else {
	$oneCikanHaberId = null;
}

if ($oneCikanHaberId) {
	$sqlHaber = "SELECT * FROM haberler WHERE id=?";
	$stmt = $conn->prepare($sqlHaber);
	$stmt->bind_param("i", $oneCikanHaberId);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0) {
		$oneCikanHaber = $result->fetch_assoc();
	} else {
		$oneCikanHaber = null; // Eğer haber bulunamazsa
	}
} else {
	$oneCikanHaber = null; // Eğer ayarlarda öne çıkan haber ID'si yoksa
}

if (isset($_GET['kategori_id']) && is_numeric($_GET['kategori_id'])) {
	$kategori_id = $_GET['kategori_id'];

	$sqlHaberler = "SELECT * FROM haberler WHERE kategori_id = ? ORDER BY id DESC";
	$stmt = $conn->prepare($sqlHaberler);
	$stmt->bind_param("i", $kategori_id);
	$stmt->execute();
	$result = $stmt->get_result();

	$haberler = [];
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$haberler[] = $row;
		}
	} else {
		$haberler = [];
	}
} else {
	// Varsayılan tüm haberler
	$sqlHaberler = "SELECT * FROM haberler ORDER BY id DESC";
	$result = $conn->query($sqlHaberler);

	$haberler = [];
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$haberler[] = $row;
		}
	}
}
if (isset($_GET['haber_id']) && is_numeric($_GET['haber_id'])) {
	$haber_id = $_GET['haber_id'];

	// Haber detayını çek
	$sql = "SELECT * FROM haberler WHERE id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("i", $haber_id);
	$stmt->execute();
	$result = $stmt->get_result();

	// Haber bilgilerini al
	if ($result->num_rows > 0) {
		$haber = $result->fetch_assoc();
		$showDetail = true; // Haber detayını göstermek için işaret
	} else {
		$showDetail = false; // Haber bulunamazsa
	}
} else {
	if (isset($_GET['kategori_id']) && is_numeric($_GET['kategori_id'])) {
		$kategori_id = $_GET['kategori_id'];

		$sqlHaberler = "SELECT * FROM haberler WHERE kategori_id = ? ORDER BY id DESC";
		$stmt = $conn->prepare($sqlHaberler);
		$stmt->bind_param("i", $kategori_id);
		$stmt->execute();
		$result = $stmt->get_result();

		$haberler = [];
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$haberler[] = $row;
			}
		} else {
			$haberler = [];
		}
	} else {
		// Varsayılan tüm haberler
		$sqlHaberler = "SELECT * FROM haberler ORDER BY id DESC";
		$result = $conn->query($sqlHaberler);

		$haberler = [];
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$haberler[] = $row;
			}
		}
	}
	$showDetail = false;
}
//footer bilgileri
$stmt = $conn->prepare("SELECT footer_text, social_links FROM footer WHERE id = 1");
$stmt->execute();
$result = $stmt->get_result();
$footer = $result->fetch_assoc();
$socialLinks = json_decode($footer['social_links'], true);

?>
<!DOCTYPE html>
<html lang="tr">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Cahit Broadcast News</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="style.css">
</head>

<body style="background-color:#f5f5f5!important;">
	<header class="bg-dark navbar-custom">
		<div class="container">
			<nav class="navbar navbar-expand-lg navbar-dark bg-dark navbar-custom">
				<a class="navbar-brand fw-bold" href="index.php">
					<img src="<?= htmlspecialchars($logo) ?>" alt="CBC Türkçe" href="index.php" style="height: 40px">
					<a href="index.php" class="d-inline-block align-text-top"
						style="font-size: 20px; text-decoration: none; color: inherit;"></a>
				</a>
				
			</nav>
		</div>
	</header>

	<header class="bg-dark navbar-custom" style="background-color:#fff!important;">
		<div class="container">
			<nav class="navbar navbar-expand-lg navbar-dark bg-dark navbar-custom" style="background-color:#fff!important;">
				<button style="width:100%; background-color: #b70000;" class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
					aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarNav">
					<ul class="navbar-nav ms-auto"
						style="list-style: none; display: flex; gap: 15px; padding: 0; margin: 0; align-items: center;">
						<?php
						$counter = 0;
						// Loop through navbar items
						foreach ($kategoriler as $item) {
							if ($counter >= 6) {
								break;
							}
							echo '<li class="nav-item" style="position: relative;">';
							echo '<a class="nav-link text-black general-font hover-text" href="?kategori_id=' . htmlspecialchars($item['id']) . '" style="text-decoration: none; font-family: Arial, sans-serif; font-size: 18px; font-weight: 500; transition: color 0.3s, text-decoration 0.3s;">';
							echo htmlspecialchars($item['baslik']);
							echo '</a>';
							echo '</li>';
							$counter++;
						}
						?>
					</ul>
				</div>
			</nav>
		</div>
	</header>

<?php

function getAyAdi($ayNumarasi) {
    $aylar = [
        "01" => "Ocak",
        "02" => "Şubat",
        "03" => "Mart",
        "04" => "Nisan",
        "05" => "Mayıs",
        "06" => "Haziran",
        "07" => "Temmuz",
        "08" => "Ağustos",
        "09" => "Eylül",
        "10" => "Ekim",
        "11" => "Kasım",
        "12" => "Aralık"
    ];

    if (isset($aylar[$ayNumarasi])) {
        return $aylar[$ayNumarasi];
    } else {
        return "";
    }
}


?>

<div class="row main-page-pd">
		
	

	<main class="container my-4 col-lg-9" style="margin-top: 0px!important;">

		<section class="container my-5">
			<?php if ($showDetail && !empty($haber)): ?>
				<!-- Haber Detay -->
				<div class="card ">
					<!-- Haber Başlığı -->
					<div class="card-header" style="padding-left: 20px">
						<h2 style="font-weight: 600;"><?= nl2br($haber['baslik']) ?></h2>
						<?php
						if(!empty($haber)){
							$ver = substr($haber['tarih'], 0, 10);
							$tarihexp = explode("-", $ver);
						}
						?>
						<label>Güncellenme Tarihi <?= "$tarihexp[2] ".getAyAdi($tarihexp[1])." $tarihexp[0]" ?></label>
					</div>
					<!-- Haber Resmi -->
					<?php if (!empty($haber['resim'])): ?>
						<img style="padding: 18px; height:700px; object-fit:cover;" src="<?= htmlspecialchars($haber['resim']) ?>" alt="<?= htmlspecialchars($haber['baslik']) ?>"
							class="img-fluid">
					<?php endif; ?>
					<!-- Haber İçeriği -->
					<div class="card-body">
						<p><?= nl2br($haber['icerik']) ?></p>
						<div class="col">
							<center>
						<div class="row special-button-view">
							<a href="index.php" class="btn btn-secondary">Geri Dön</a>
						</div>
						<div class="row special-button-view">
							<a href="?kategori_id=<?= htmlspecialchars($haber['kategori_id']) ?>"
								class="btn btn-info mt-3">İlgili Kategorideki Haberleri Gör</a>
						</div>
						</center>
					</div>
					</div>
					
				</div>
			<?php else: ?>




				<section class="md-4 mb-4">

					<h2 style="font-weight: 600;">Öne Çıkan Haber</h2>

<a style="text-decoration: none;" href="?haber_id=<?= $oneCikanHaber['id'] ?>">
						<div style="flex-direction:inherit;" class="row card border-0 rounded card-body-stext">
	
	<div class="col-md-8">
		<?php if (!empty($oneCikanHaber['resim'])): ?>
		      <img src="<?= htmlspecialchars($oneCikanHaber['resim']) ?>" class="card-img-top"
		           alt="<?= htmlspecialchars($oneCikanHaber['baslik']) ?>" style="height: 300px; object-fit: cover;">
		    <?php endif; ?>
	</div>

	<div class="col-md-4 ">
		<div class="card-body card-body-stext">
			      <h5 class="card-title" style="font-size: 23px; font-weight:700"><?= htmlspecialchars($oneCikanHaber['baslik']) ?></h5>
			      <p class="card-text"><?= nl2br(substr($oneCikanHaber['demoicerik'], 0, 300)) ?>...</p>
			    </div>
	</div>

</div>
</a>
					

				</section>
				<!-- Haber Listesi -->

				<div class="row">
					<h2 style="font-weight: 600;">
						<?php echo $targetBaslik == "" ? "Son Haberler" : $targetBaslik." Haberleri" ?>
					</h2>
					<?php if (!empty($haberlerNew)): ?>
						<?php foreach ($haberlerNew as $haber): ?>


<div class="col-md-4 mb-4">
	<a style="text-decoration: none;" href="?haber_id=<?= $haber['id'] ?>">
  <div class="card border-0 rounded card-new">
    <?php if (!empty($haber['resim'])): ?>
      <img src="<?= htmlspecialchars($haber['resim']) ?>" class="card-img-top"
           alt="<?= htmlspecialchars($haber['baslik']) ?>" style="height: 300px; object-fit: cover;">
    <?php endif; ?>
    
	    <div class="card-body card-body-new">
	      <h5 class="card-title"><?= nl2br($haber['baslik']) ?></h5>
	      <p class="card-text"><?= ($haber['demoicerik']) ?>...</p>
	    </div>
	
  </div>
  </a>
</div>

						<?php endforeach; ?>
					<?php else: ?>
						<p>Bu kategoriye ait haber bulunamadı.</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</section>
		<?php
			if(!isset($haber_id)){
		?>
		<nav>
			<ul class="pagination" style="justify-content: center;">
				<?php
				if($catVar)
					$xis = "&kategori_id=".$_GET['kategori_id'];
				?>
				<?php for ($i = 1; $i <= $totalPages; $i++): ?>
					<li class="page-item <?= $i == $page ? 'active' : '' ?>">
						<a class="page-link" href="?page=<?= $i ?><?php echo $xis ?>"><?= $i ?></a>
					</li>
				<?php endfor; ?>
			</ul>
		</nav>

	<?php } ?>


	</main>


	<main class="container my-4 col-lg-3"> <!-- YAN TARAF -->

		<section class="container my-5">

				<!-- Haber Listesi -->
				<div class="row">
					<h2 style="font-weight: 600;">Ilginizi çekebilecekler</h2>
					<?php if (!empty($randomHaberler)): ?>
						<?php foreach ($randomHaberler as $haber): ?>
							<div class="col-md-12 mb-4">
								<a style="text-decoration: none;" href="?haber_id=<?= $haber['id'] ?>">
									<div class="card border-0 rounded card-body-stext">
										<?php if (!empty($haber['resim'])): ?>
											<img src="<?= htmlspecialchars($haber['resim']) ?>" class="card-img-top"
												alt="<?= htmlspecialchars($haber['baslik']) ?>" style="height: 170px; object-fit: cover;">
										<?php endif; ?>
										<div class="card-body card-body-stext">
											<h5 class="card-title card-body-stext"><?= nl2br($haber['baslik']) ?></h5>
											<p class="card-text card-body-stext"><?= ($haber['demoicerik']) ?>...</p>
											
										</div>
									</div>
								</a>
							</div>
						<?php endforeach; ?>
					<?php else: ?>
						<p>Bu kategoriye ait haber bulunamadı.</p>
					<?php endif; ?>
				</div>
		
		</section>


	</main>
	</div>

	<!-- <footer class="bg-dark text-white py-3">
		<div class="container">
			<div class="row text-center mb-3">
				<p class="mb-0">&copy; 2024 BBC Türkçe. Tüm hakları saklıdır.</p>
			</div>
			<div class="row">
				<div class="d-flex justify-content-center align-items-center gap-3">
					<img src="media/instagram.png" alt="Instagram" style="height: 30px;">
					<img src="media/facebook.png" alt="Facebook" style="height: 30px;">
					<img src="media/youtube.png" alt="YouTube" style="height: 30px;">
				</div>
			</div>
		</div>
	</footer> -->

	<footer class="bg-dark text-white py-3">
		<div class="container">
			<div class="row text-center mb-3">
				<p class="mb-0"><?= htmlspecialchars($footer['footer_text']) ?></p>
			</div>
			<div class="row">
				<div class="d-flex justify-content-center align-items-center gap-3">
					<a href="<?= htmlspecialchars($socialLinks['instagram']) ?>" target="_blank">
						<img src="media/instagram.png" alt="Instagram" style="height: 30px; filter: invert(100%);">
					</a>
					<a href="<?= htmlspecialchars($socialLinks['facebook']) ?>" target="_blank">
						<img src="media/facebook.png" alt="Facebook" style="height: 30px; filter: invert(100%);">
					</a>
					<a href="<?= htmlspecialchars($socialLinks['youtube']) ?>" target="_blank">
						<img src="media/youtube.png" alt="YouTube" style="height: 30px; filter: invert(100%);">
					</a>
				</div>
			</div>
		</div>
	</footer>


	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>