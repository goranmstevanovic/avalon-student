<?php  
			include_once ( "config/core.php");
?>
<!doctype html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--favicon-->
	<link rel="icon" href="images/kalen.png" type="image/png"/>
	<!--plugins-->
	<link href="assets/plugins/vectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet"/>
	<link href="assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
	<link href="assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
	<link href="assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet"/>
	<!-- loader-->
	<link href="assets/css/pace.min.css" rel="stylesheet"/>
	<script src="assets/js/pace.min.js"></script>
	<!-- Bootstrap CSS -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/css/bootstrap-extended.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
	<link href="assets/css/app.css" rel="stylesheet">
	<link href="assets/css/icons.css" rel="stylesheet">
	<!-- Theme Style CSS -->
	<link rel="stylesheet" href="assets/css/dark-theme.css"/>
	<link rel="stylesheet" href="assets/css/semi-dark.css"/>
	<link rel="stylesheet" href="assets/css/header-colors.css"/>
	<title>SMS</title>
</head>

<body>
	<!--wrapper-->
	<div class="wrapper" >
		<!--sidebar wrapper -->
		
        <?php
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);  
		
 		include_once "login_checker.php"; 
		
			// 	$site = $_SERVER['DOCUMENT_ROOT'] . "/sms/";
			// include_once ($site."/config/core.php");
			// include_once ($site."/config/database.php");

			// include_once ($site."/config/autoload.php");
			// $slika_put = $home_url."/assets/images/sms1.png";
			// $database = new Database();
			// $db = $database->getConnection();
			// $lokacija = new lokacija($db);
		//	include_once ( "config/core.php");

        include_once  $site.'/app/sidebar.php'; 
          
        
        
        
        ?>
		<!--end sidebar wrapper -->
		<!--start header -->
      
		<header>
		<?php   include __DIR__ . '/app/header.php'; ?>
		</header>
				<!--end header -->
		
		
				<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<?php
					function formatSize($bytes) {
						if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
						if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
						if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
						return $bytes . ' B';
					}

					error_reporting(E_ALL);
					ini_set('display_errors', 1);

					include_once "config/core.php";
					include_once "login_checker.php";
					include_once 'config/database.php';
					include_once 'config/autoload.php'; // za kviz/pitanje/odgovor/kviz_pokusaj/kviz_odgovor objekte
					include_once 'config/funkcije.php'; // video_embed_url() - za iframe prikaz video materijala

					$database = new Database();
					$db = $database->getConnection();

					$moj_id = $_SESSION['user_id'];

					// ===================== KVIZ - logika =====================
					$kviz_obj = new kviz($db);
					$pitanje_obj = new pitanje($db);
					$odgovor_obj = new odgovor($db);
					$kviz_pokusaj_obj = new kviz_pokusaj($db);
					$kviz_odgovor_obj = new kviz_odgovor($db);

					// Boduje poslate odgovore i zavrsava pokusaj (koristi se i kod predaje i kod automatske predaje po isteku vremena)
					function kviz_oceni_i_zavrsi($pitanje_obj, $odgovor_obj, $kviz_odgovor_obj, $kviz_pokusaj_obj, $pokusaj_id, $fk_kviz, $odgovori_post, $dopuna_post = [], $spajanje_post = [])
					{
						$pitanja_lista = $pitanje_obj->read_by_kviz($fk_kviz)->fetchAll(PDO::FETCH_ASSOC);

						$bodovi = 0;
						$max_bodovi = 0;

						foreach ($pitanja_lista as $p) {
							$max_bodovi += (int)$p['poeni'];

							if ($p['tip_pitanja'] === 'dopuna') {

								// dopuna - svaka praznina se ocenjuje posebno, poredjenje sa varijantama razdvojenim sa '|'
								foreach ($odgovor_obj->read_by_pitanje($p['id'])->fetchAll(PDO::FETCH_ASSOC) as $o) {
									$unet = isset($dopuna_post[$p['id']][$o['redosled']]) ? trim($dopuna_post[$p['id']][$o['redosled']]) : '';

									$tacno = 0;
									if ($unet !== '') {
										foreach (explode('|', $o['tekst_odgovora']) as $varijanta) {
											if (mb_strtolower(trim($varijanta)) === mb_strtolower($unet)) {
												$tacno = 1;
												break;
											}
										}
									}

									if ($tacno) {
										$bodovi += (int)$o['poeni'];
									}

									$kviz_odgovor_obj->fk_pokusaj = $pokusaj_id;
									$kviz_odgovor_obj->fk_pitanje = $p['id'];
									$kviz_odgovor_obj->fk_odgovor = $o['id'];
									$kviz_odgovor_obj->unet_tekst = ($unet !== '') ? $unet : null;
									$kviz_odgovor_obj->tacan = $tacno;
									$kviz_odgovor_obj->create();
								}

								continue;
							}

							if ($p['tip_pitanja'] === 'spajanje') {

								// spajanje - svaka stavka banke sa tacan_broj se ocenjuje posebno (poredi se sa brojem praznine koji je djak izabrao)
								foreach ($odgovor_obj->read_by_pitanje($p['id'])->fetchAll(PDO::FETCH_ASSOC) as $o) {
									$unet = isset($spajanje_post[$p['id']][$o['id']]) ? trim($spajanje_post[$p['id']][$o['id']]) : '';
									$unet_broj = ($unet !== '') ? (int)$unet : null;

									if ($o['tacan_broj'] !== null) {
										$tacno = ($unet_broj === (int)$o['tacan_broj']) ? 1 : 0;
										if ($tacno) {
											$bodovi += (int)$o['poeni'];
										}
									} else {
										// mamac - ne ocenjuje se
										$tacno = null;
									}

									$kviz_odgovor_obj->fk_pokusaj = $pokusaj_id;
									$kviz_odgovor_obj->fk_pitanje = $p['id'];
									$kviz_odgovor_obj->fk_odgovor = $o['id'];
									$kviz_odgovor_obj->unet_tekst = ($unet !== '') ? $unet : null;
									$kviz_odgovor_obj->tacan = $tacno;
									$kviz_odgovor_obj->create();
								}

								continue;
							}

							$odabrani_id = isset($odgovori_post[$p['id']]) ? (int)$odgovori_post[$p['id']] : null;

							$tacan_id = null;
							foreach ($odgovor_obj->read_by_pitanje($p['id'])->fetchAll(PDO::FETCH_ASSOC) as $o) {
								if ($o['tacan']) {
									$tacan_id = (int)$o['id'];
									break;
								}
							}

							$tacno = ($odabrani_id !== null && $odabrani_id === $tacan_id) ? 1 : 0;
							if ($tacno) {
								$bodovi += (int)$p['poeni'];
							}

							$kviz_odgovor_obj->fk_pokusaj = $pokusaj_id;
							$kviz_odgovor_obj->fk_pitanje = $p['id'];
							$kviz_odgovor_obj->fk_odgovor = $odabrani_id ?: null;
							$kviz_odgovor_obj->unet_tekst = null;
							$kviz_odgovor_obj->tacan = $tacno;
							$kviz_odgovor_obj->create();
						}

						$procenat = $max_bodovi > 0 ? round($bodovi / $max_bodovi * 100, 2) : 0;
						$kviz_pokusaj_obj->finish($pokusaj_id, $bodovi, $max_bodovi, $procenat);
					}

					// Provera da li tekst pitanja sadrzi oznake [1]-[5] za mesto praznine (dopuna i spajanje)
					function kviz_ima_oznake_praznina($tekst)
					{
						return (bool)preg_match('/\[[1-5]\]/', $tekst);
					}

					// Ubacuje HTML (npr. input polje ili prikaz odgovora) na mesto oznaka [1]-[5] u tekstu pitanja,
					// ostatak teksta escape-uje kao i obicno. $blanks je niz indeksiran po redosledu (0-4).
					function kviz_render_tekst_sa_oznakama($tekst, $blanks)
					{
						$delovi = preg_split('/\[([1-5])\]/', $tekst, -1, PREG_SPLIT_DELIM_CAPTURE);

						$html = '';
						foreach ($delovi as $i => $deo) {
							if ($i % 2 === 0) {
								$html .= nl2br(htmlspecialchars($deo));
							} else {
								$redosled = (int)$deo - 1;
								$html .= $blanks[$redosled] ?? '[' . $deo . ']';
							}
						}

						return $html;
					}

					// Akcija: pokretanje novog pokusaja resavanja kviza
					if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kviz_action']) && $_POST['kviz_action'] === 'pokreni') {

						$fk_kviz = (int)$_POST['kviz_id'];

						$vidljivi_ids = array_column($kviz_obj->read_visible_for_djak($moj_id)->fetchAll(PDO::FETCH_ASSOC), 'id');

						if (in_array($fk_kviz, $vidljivi_ids)) {

							$kv = $kviz_obj->read_one($fk_kviz)->fetch(PDO::FETCH_ASSOC);
							$rok_istekao = !empty($kv['rok']) && strtotime($kv['rok']) < time();

							$u_toku = null;
							$zavrseni_count = 0;
							foreach ($kviz_pokusaj_obj->read_by_kviz_djak($fk_kviz, $moj_id)->fetchAll(PDO::FETCH_ASSOC) as $p) {
								if ($p['finished_at'] === null) {
									$u_toku = $p;
								} else {
									$zavrseni_count++;
								}
							}

							if (!$u_toku && !$rok_istekao && $zavrseni_count < (int)$kv['broj_pokusaja']) {
								$kviz_pokusaj_obj->fk_kviz = $fk_kviz;
								$kviz_pokusaj_obj->fk_djak = $moj_id;
								$kviz_pokusaj_obj->create();
							}
						}

						header("Location: index.php?tab=kviz&kviz=" . $fk_kviz . "#tab-kviz");
						exit;
					}

					// Akcija: predaja odgovora na kviz
					if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kviz_action']) && $_POST['kviz_action'] === 'predaj') {

						$fk_kviz = (int)$_POST['kviz_id'];
						$pokusaj_id = (int)$_POST['pokusaj_id'];

						$pokusaj_data = $kviz_pokusaj_obj->read_one($pokusaj_id)->fetch(PDO::FETCH_ASSOC);

						if ($pokusaj_data
							&& (int)$pokusaj_data['fk_djak'] === (int)$moj_id
							&& (int)$pokusaj_data['fk_kviz'] === $fk_kviz
							&& $pokusaj_data['finished_at'] === null) {

							$odgovori_post = isset($_POST['odgovor']) && is_array($_POST['odgovor']) ? $_POST['odgovor'] : [];
							$dopuna_post = isset($_POST['dopuna']) && is_array($_POST['dopuna']) ? $_POST['dopuna'] : [];
							$spajanje_post = isset($_POST['spajanje']) && is_array($_POST['spajanje']) ? $_POST['spajanje'] : [];
							kviz_oceni_i_zavrsi($pitanje_obj, $odgovor_obj, $kviz_odgovor_obj, $kviz_pokusaj_obj, $pokusaj_id, $fk_kviz, $odgovori_post, $dopuna_post, $spajanje_post);
						}

						header("Location: index.php?tab=kviz&kviz=" . $fk_kviz . "#tab-kviz");
						exit;
					}

					$aktivni_tab = (isset($_GET['tab']) && in_array($_GET['tab'], ['kviz', 'domaci'])) ? 'kviz' : 'dokumenti';
					$otvoreni_kviz_id = isset($_GET['kviz']) ? (int)$_GET['kviz'] : 0;

					// ===================== DOMAĆI AUDIO - logika =====================
					$domaci_audio_obj = new domaci_audio($db);
					$domaci_audio_lista = $domaci_audio_obj->read_visible_for_djak($moj_id)->fetchAll(PDO::FETCH_ASSOC);
					// ===================== KRAJ DOMAĆI AUDIO logike =====================

					// Lista kvizova vidljivih djaku, sa statusom (u toku / zavrsen / istekao rok / moze pokusaj)
					$kvizovi_lista = $kviz_obj->read_visible_for_djak($moj_id)->fetchAll(PDO::FETCH_ASSOC);

					foreach ($kvizovi_lista as &$kv) {
						$kv['broj_pitanja'] = $pitanje_obj->read_by_kviz($kv['id'])->rowCount();

						$kv['u_toku'] = null;
						$kv['zavrseni'] = [];

						foreach ($kviz_pokusaj_obj->read_by_kviz_djak($kv['id'], $moj_id)->fetchAll(PDO::FETCH_ASSOC) as $p) {
							if ($p['finished_at'] === null) {
								$kv['u_toku'] = $p;
							} else {
								$kv['zavrseni'][] = $p;
							}
						}

						$kv['rok_istekao'] = !empty($kv['rok']) && strtotime($kv['rok']) < time();
						$kv['moze_pokusaj'] = !$kv['rok_istekao'] && !$kv['u_toku'] && count($kv['zavrseni']) < (int)$kv['broj_pokusaja'];
					}
					unset($kv);

					// Sortiranje: prvo kvizovi koji se jos mogu resavati (u toku ili ima preostalih pokusaja, a rok nije istekao),
					// zatim oni koji se vise ne mogu resavati (rok istekao ili iskoriscen broj pokusaja);
					// u okviru obe grupe - po roku opadajuce (bez roka = najdalje u buducnosti) i id-u opadajuce
					usort($kvizovi_lista, function ($a, $b) {
						$a_moze = !$a['rok_istekao'] && ($a['u_toku'] || count($a['zavrseni']) < (int)$a['broj_pokusaja']);
						$b_moze = !$b['rok_istekao'] && ($b['u_toku'] || count($b['zavrseni']) < (int)$b['broj_pokusaja']);

						if ($a_moze !== $b_moze) {
							return $a_moze ? -1 : 1;
						}

						$a_rok = $a['rok'] ?: '9999-12-31 23:59:59';
						$b_rok = $b['rok'] ?: '9999-12-31 23:59:59';

						if ($a_rok !== $b_rok) {
							return $a_rok < $b_rok ? 1 : -1;
						}

						return $b['id'] - $a['id'];
					});

					// Detalji otvorenog kviza - forma za resavanje (ako je pokusaj u toku) ili rezultat poslednjeg zavrsenog pokusaja
					$otvoreni_kviz = null;
					$otvoreni_pitanja = [];
					$otvoreni_pokusaj = null;
					$otvoreni_rezultat = null;
					$otvoreni_rezultat_grupisano = null;

					if ($otvoreni_kviz_id > 0) {

						foreach ($kvizovi_lista as $kv) {
							if ((int)$kv['id'] === $otvoreni_kviz_id) {
								$otvoreni_kviz = $kv;
								break;
							}
						}

						if ($otvoreni_kviz) {

							if ($otvoreni_kviz['u_toku']) {

								$pokusaj = $otvoreni_kviz['u_toku'];

								// ako je vremensko ogranicenje isteklo, automatski predaj (neodgovoreno = netacno) i prikazi rezultat
								if (!empty($otvoreni_kviz['vreme_ogranicenje_min'])) {
									$istek = strtotime($pokusaj['started_at']) + ((int)$otvoreni_kviz['vreme_ogranicenje_min'] * 60);

									if (time() >= $istek) {
										kviz_oceni_i_zavrsi($pitanje_obj, $odgovor_obj, $kviz_odgovor_obj, $kviz_pokusaj_obj, $pokusaj['id'], $otvoreni_kviz_id, []);
										header("Location: index.php?tab=kviz&kviz=" . $otvoreni_kviz_id . "#tab-kviz");
										exit;
									}
								}

								$otvoreni_pokusaj = $pokusaj;
								$otvoreni_pitanja = $pitanje_obj->read_by_kviz($otvoreni_kviz_id)->fetchAll(PDO::FETCH_ASSOC);

								foreach ($otvoreni_pitanja as &$op) {
									$op['odgovori'] = $odgovor_obj->read_by_pitanje($op['id'])->fetchAll(PDO::FETCH_ASSOC);
								}
								unset($op);

							} elseif (!empty($otvoreni_kviz['zavrseni'])) {

								$otvoreni_pokusaj = $otvoreni_kviz['zavrseni'][0];
								$otvoreni_rezultat = $kviz_odgovor_obj->read_by_pokusaj($otvoreni_pokusaj['id'])->fetchAll(PDO::FETCH_ASSOC);

								if ($otvoreni_kviz['prikazi_odgovore']) {
									foreach ($otvoreni_rezultat as &$or) {
										$or['tacan_tekst'] = null;

										if (!in_array($or['tip_pitanja'], ['dopuna', 'spajanje'], true)) {
											foreach ($odgovor_obj->read_by_pitanje($or['fk_pitanje'])->fetchAll(PDO::FETCH_ASSOC) as $o) {
												if ($o['tacan']) {
													$or['tacan_tekst'] = $o['tekst_odgovora'];
													break;
												}
											}
										}
									}
									unset($or);

									// grupisanje po pitanju (po redosledu) - dopuna pitanja imaju do dve odvojene stavke, po jedna za svaku prazninu
									$otvoreni_rezultat_grupisano = [];
									$poslednji_pid = null;
									foreach ($otvoreni_rezultat as $or) {
										if ($or['fk_pitanje'] !== $poslednji_pid) {
											$otvoreni_rezultat_grupisano[] = [
												'tekst_pitanja' => $or['tekst_pitanja'],
												'tip_pitanja' => $or['tip_pitanja'],
												'poeni' => $or['poeni'],
												'stavke' => [],
											];
											$poslednji_pid = $or['fk_pitanje'];
										}
										$otvoreni_rezultat_grupisano[count($otvoreni_rezultat_grupisano) - 1]['stavke'][] = $or;
									}
								}
							}
						}
					}
					// ===================== KRAJ KVIZ logike =====================
					//echo $moj_id;
					//$moj_id = 3;
					// ✅ NOVI QUERY (ZA ĐAKA)
					$query = "
					SELECT
						d.id,
						d.naziv,
						d.created_at,
						d.datum_vazenja_do,
						d.velicina,
						d.ekstenzija,
						d.je_video,
						d.video_url,
						dp.pravo_pregled,
						dp.pravo_download

					FROM dokumenti d

					JOIN dokument_pristup dp
						ON dp.fk_dokument = d.id

					WHERE
						d.aktivan = 1
						AND dp.tip_pristupa = 'djak'
						AND dp.fk_entitet = :djak_id

						AND (d.datum_pocetka_prikaza IS NULL OR d.datum_pocetka_prikaza <= NOW())
						AND (d.datum_kraja_prikaza IS NULL OR d.datum_kraja_prikaza >= NOW())

					ORDER BY d.created_at DESC
					";

					$stmt = $db->prepare($query);
					$stmt->bindParam(":djak_id", $moj_id);
					$stmt->execute();

					// grupisanje po mesecima - fajlovi i video linkovi idu u odvojene tabove
					$dokumenti_po_mesecima = [];
					$video_po_mesecima = [];

					while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
						$mesec = date('Y-m', strtotime($row['created_at']));

						if (!empty($row['je_video'])) {
							$video_po_mesecima[$mesec][] = $row;
						} else {
							$dokumenti_po_mesecima[$mesec][] = $row;
						}
					}
					?>

					<div class="mt-3">

						<!-- <h5 class="mb-3"><u>Moji materijali</u></h5> -->

						<!-- TABOVI: Dokumenti / Video / Kviz - naslovi se vide na hover preko title atributa -->
						<ul class="nav nav-tabs mb-3" id="materijaliTab" role="tablist">
							<li class="nav-item" role="presentation">
								<button class="nav-link <?= $aktivni_tab === 'dokumenti' ? 'active' : '' ?>" id="tab-dokumenti-btn" data-bs-toggle="tab" data-bs-target="#tab-dokumenti" type="button" role="tab" title="Dokumenti">
									<i class='bx bx-file fs-5'></i> Dokumenti
								</button>
							</li>
							<li class="nav-item" role="presentation">
								<button class="nav-link" id="tab-video-btn" data-bs-toggle="tab" data-bs-target="#tab-video" type="button" role="tab" title="Video">
									<i class='bx bx-video fs-5'></i> Video
								</button>
							</li>
							<li class="nav-item" role="presentation">
								<button class="nav-link <?= $aktivni_tab === 'kviz' ? 'active' : '' ?>" id="tab-kviz-btn" data-bs-toggle="tab" data-bs-target="#tab-kviz" type="button" role="tab" title="Domaći zadaci">
									<i class='bx bx-task fs-5'></i> Domaći zadaci
								</button>
							</li>
						</ul>

						<div class="tab-content" id="materijaliTabContent">

							<!-- TAB: DOKUMENTI (postojeci prikaz, samo fajlovi - je_video = 0) -->
							<div class="tab-pane fade <?= $aktivni_tab === 'dokumenti' ? 'show active' : '' ?>" id="tab-dokumenti" role="tabpanel">

								<?php if (empty($dokumenti_po_mesecima)) { ?>
									<div class="alert alert-info">Nema dokumenata za prikaz.</div>
								<?php } ?>

								<?php foreach ($dokumenti_po_mesecima as $mesec => $dokumenti) {

									$timestamp = strtotime($mesec . "-01");
									$mesec_naziv = strftime('%B %Y', $timestamp);
									?>

									<div class="mb-2">
										<h6 class="text-muted"><?= ucfirst($mesec_naziv) ?></h6>
									</div>

									<div class="row">

										<?php foreach ($dokumenti as $d) { ?>

											<div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3">
												<div class="card shadow-sm border-0">
													<div class="card-body">

														<!-- NASLOV -->
														<div class="d-flex justify-content-between">
															<strong><?= htmlspecialchars($d['naziv']) ?></strong>
															<span><?= strtoupper($d['ekstenzija']) ?></span>
														</div>

														<!-- DATUM -->
														<div class="text-muted" style="font-size:13px;">
															📅 <?= date('d.m.Y', strtotime($d['created_at'])) ?>
														</div>

														<!-- VAŽENJE -->
														<?php if(!empty($d['datum_vazenja_do'])){ ?>
														<div style="font-size:13px; color:#dc3545;">
															⏳ Važi do: <?= date('d.m.Y', strtotime($d['datum_vazenja_do'])) ?>
														</div>
														<?php } ?>

														<!-- VELIČINA -->
														<div style="font-size:13px;">
															📄 <?= formatSize((int)$d['velicina']) ?>
														</div>

														<!-- AKCIJE -->
														<div class="mt-3 d-flex gap-2 flex-wrap">

															<?php if($d['pravo_pregled']){ ?>
																<a href="download_dokument.php?id=<?= $d['id'] ?>"
																class="btn btn-outline-primary btn-sm"><i class='bx bx-show'></i>
																	Vidi
																</a>
															<?php } ?>

															<?php if($d['pravo_download']){ ?>
																<a href="download_dokument.php?id=<?= $d['id'] ?>" style="display:none"
																class="btn btn-success btn-sm">
																	Download
																</a>
															<?php } ?>

														</div>

													</div>
												</div>

											</div>
										<?php } ?>

									</div>

									<?php
								} ?>

							</div>

							<!-- TAB: VIDEO (je_video = 1) -->
							<div class="tab-pane fade" id="tab-video" role="tabpanel">

								<?php if (empty($video_po_mesecima)) { ?>
									<div class="alert alert-info">Nema video materijala za prikaz.</div>
								<?php } ?>

								<?php foreach ($video_po_mesecima as $mesec => $videi) {

									$timestamp = strtotime($mesec . "-01");
									$mesec_naziv = strftime('%B %Y', $timestamp);
									?>

									<div class="mb-2">
										<h6 class="text-muted"><?= ucfirst($mesec_naziv) ?></h6>
									</div>

									<div class="row">

										<?php foreach ($videi as $d) { ?>

											<div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3">
												<div class="card shadow-sm border-0">
													<div class="card-body">

														<!-- NASLOV -->
														<div class="d-flex justify-content-between">
															<strong><?= htmlspecialchars($d['naziv']) ?></strong>
															<i class='bx bxs-video text-danger'></i>
														</div>

														<!-- DATUM -->
														<div class="text-muted" style="font-size:13px;">
															📅 <?= date('d.m.Y', strtotime($d['created_at'])) ?>
														</div>

														<!-- VAŽENJE -->
														<?php if(!empty($d['datum_vazenja_do'])){ ?>
														<div style="font-size:13px; color:#dc3545;">
															⏳ Važi do: <?= date('d.m.Y', strtotime($d['datum_vazenja_do'])) ?>
														</div>
														<?php } ?>

														<!-- AKCIJA: prikazi/sakrij video (lazy-load iframe) -->
														<?php if($d['pravo_pregled']){ ?>
															<div class="mt-3">
																<button class="btn btn-outline-primary btn-sm toggle-video" data-id="<?= $d['id'] ?>">
																	<i class='bx bx-play-circle'></i> Pogledaj video
																</button>
															</div>
														<?php } ?>

														<!-- skriveni iframe - src se postavlja na klik (lazy-load) -->
														<div class="ratio ratio-16x9 mt-2" id="video_wrap_<?= $d['id'] ?>" style="display:none;">
															<iframe src="" data-src="<?= htmlspecialchars(video_embed_url($d['video_url'])) ?>" allowfullscreen frameborder="0"></iframe>
														</div>

													</div>
												</div>

											</div>
										<?php } ?>

									</div>

									<?php
								} ?>

							</div>

							<!-- TAB: DOMAĆI ZADACI (audio + kvizovi) -->
							<div class="tab-pane fade <?= $aktivni_tab === 'kviz' ? 'show active' : '' ?>" id="tab-kviz" role="tabpanel">

								<!-- ===== AUDIO DOMAĆI ZADACI ===== -->
								<h5 class="mb-3"><i class='bx bx-microphone text-danger'></i> Audio domaći zadaci</h5>

								<?php if (empty($domaci_audio_lista)): ?>
									<div class="alert alert-info mb-4">Nema audio domaćih zadataka.</div>
								<?php else: ?>
									<div class="row mb-4">
									<?php foreach ($domaci_audio_lista as $da):
										$ima_odgovor  = !empty($da['odgovor_filename']);
										$je_slusao    = !empty($da['slusano_at']);
										$rok_istekao  = !empty($da['rok']) && strtotime($da['rok']) < time();
									?>
										<div class="col-12 col-md-6 col-lg-4 mb-3">
											<div class="card shadow-sm border-0 h-100">
												<div class="card-body d-flex flex-column">

													<div class="d-flex justify-content-between align-items-start mb-1">
														<strong><?= htmlspecialchars($da['naziv']) ?></strong>
														<?php if ($ima_odgovor): ?>
															<span class="badge bg-success ms-2" title="Odgovor poslan"><i class='bx bx-check-circle'></i></span>
														<?php elseif ($je_slusao): ?>
															<span class="badge bg-info text-dark ms-2" title="Poslušano"><i class='bx bx-headphone'></i></span>
														<?php else: ?>
															<span class="badge bg-secondary ms-2">Novo</span>
														<?php endif; ?>
													</div>

													<?php if (!empty($da['opis'])): ?>
														<div class="text-muted mb-2" style="font-size:13px;"><?= nl2br(htmlspecialchars($da['opis'])) ?></div>
													<?php endif; ?>

													<?php if (!empty($da['rok'])): ?>
														<div style="font-size:13px; <?= $rok_istekao ? 'color:#dc3545;' : '' ?>">
															⏳ Rok: <?= date('d.m.Y H:i', strtotime($da['rok'])) ?><?= $rok_istekao ? ' (istekao)' : '' ?>
														</div>
													<?php endif; ?>

													<!-- Profesorov audio snimak -->
													<?php if (!empty($da['audio_filename'])): ?>
														<div class="mt-2">
															<small class="text-muted d-block mb-1">Uputstvo profesora:</small>
															<audio controls class="w-100"
																   style="height:36px;"
																   data-domaci-id="<?= (int)$da['id'] ?>"
																   onplay="oznaci_slusano(this)">
																<source src="serve_domaci_audio.php?file=<?= urlencode($da['audio_filename']) ?>&tip=profesor"
																		type="<?= htmlspecialchars($da['mime_type']) ?>">
															</audio>
														</div>
													<?php endif; ?>

													<!-- Djakov odgovor (ako postoji) -->
													<?php if ($ima_odgovor): ?>
														<div class="mt-2">
															<small class="text-muted d-block mb-1">Vaš odgovor (poslan <?= date('d.m.Y', strtotime($da['poslato_at'])) ?>):</small>
															<audio controls class="w-100" style="height:36px;">
																<source src="serve_domaci_audio.php?file=<?= urlencode($da['odgovor_filename']) ?>&tip=djaci"
																		type="<?= htmlspecialchars($da['odgovor_mime']) ?>">
															</audio>
														</div>
													<?php endif; ?>

													<!-- Snimanje odgovora -->
													<?php if (!$rok_istekao): ?>
													<div class="mt-3" id="recorder_<?= (int)$da['id'] ?>">
														<small class="text-muted d-block mb-1">
															<?= $ima_odgovor ? 'Snimiti ponovo:' : 'Vaš odgovor:' ?>
														</small>
														<div class="d-flex align-items-center gap-2 flex-wrap">
															<button type="button" class="btn btn-sm btn-outline-danger djak-snimi-btn"
																	data-id="<?= (int)$da['id'] ?>">
																<i class='bx bx-microphone'></i> Snimi
															</button>
															<button type="button" class="btn btn-sm btn-secondary djak-zaustavi-btn"
																	data-id="<?= (int)$da['id'] ?>" style="display:none;" disabled>
																<i class='bx bx-stop'></i> Zaustavi
															</button>
															<span class="djak-timer text-danger fw-bold" data-id="<?= (int)$da['id'] ?>"></span>
														</div>
														<div class="djak-playback mt-2" data-id="<?= (int)$da['id'] ?>" style="display:none;">
															<audio controls class="w-100 djak-audio-preview" style="height:36px;" data-id="<?= (int)$da['id'] ?>"></audio>
															<div class="d-flex gap-2 mt-1">
																<button type="button" class="btn btn-sm btn-outline-secondary djak-reset-btn" data-id="<?= (int)$da['id'] ?>">
																	<i class='bx bx-refresh'></i> Ponovi
																</button>
																<button type="button" class="btn btn-sm btn-primary djak-posalji-btn" data-id="<?= (int)$da['id'] ?>">
																	<i class='bx bx-send'></i> Pošalji odgovor
																</button>
															</div>
															<div class="djak-upload-status mt-1" data-id="<?= (int)$da['id'] ?>"></div>
														</div>
													</div>
													<?php else: ?>
														<?php if (!$ima_odgovor): ?>
															<div class="text-muted mt-2" style="font-size:13px;">Rok za predaju je istekao.</div>
														<?php endif; ?>
													<?php endif; ?>

												</div>
											</div>
										</div>
									<?php endforeach; ?>
									</div>
								<?php endif; ?>

								<hr class="my-4">
								<h5 class="mb-3"><i class='bx bx-help-circle text-primary'></i> Kvizovi</h5>

								<?php if ($otvoreni_kviz && $otvoreni_pokusaj && $otvoreni_kviz['u_toku']) { ?>

									<!-- FORMA ZA RESAVANJE KVIZA - jedno pitanje na ekranu, navigacija strelicama -->
									<div class="card shadow-sm border-0 mb-3">
										<div class="card-body">
											<h5><?= htmlspecialchars($otvoreni_kviz['naziv']) ?></h5>

											<?php if (!empty($otvoreni_kviz['opis'])) { ?>
												<p class="text-muted"><?= nl2br(htmlspecialchars($otvoreni_kviz['opis'])) ?></p>
											<?php } ?>

											<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
												<strong>Pitanje <span id="kvizBrojTrenutni">1</span> od <?= count($otvoreni_pitanja) ?></strong>

												<?php if (!empty($otvoreni_kviz['vreme_ogranicenje_min'])) {
													$istek_ts = strtotime($otvoreni_pokusaj['started_at']) + ((int)$otvoreni_kviz['vreme_ogranicenje_min'] * 60);
													$preostalo_sek = max(0, $istek_ts - time());
												?>
													<div class="alert alert-warning mb-0 py-1 px-2" id="kvizOdbrojavanje" data-preostalo="<?= $preostalo_sek ?>">
														<i class='bx bx-time'></i> Preostalo vreme: <span id="kvizVreme"></span>
													</div>
												<?php } ?>
											</div>
										</div>
									</div>

									<form method="post" id="formaKviz">
										<input type="hidden" name="kviz_action" value="predaj">
										<input type="hidden" name="kviz_id" value="<?= (int)$otvoreni_kviz['id'] ?>">
										<input type="hidden" name="pokusaj_id" value="<?= (int)$otvoreni_pokusaj['id'] ?>">

										<?php foreach ($otvoreni_pitanja as $i => $p) { ?>
											<div class="card shadow-sm border-0 mb-3 kviz-pitanje-card" data-q="<?= $i ?>" <?= $i === 0 ? '' : 'style="display:none;"' ?>>
												<div class="card-body">
													<h6>Pitanje <?= $i + 1 ?>. <span class="badge bg-secondary"><?= (int)$p['poeni'] ?> <?= ((int)$p['poeni'] === 1) ? 'poen' : 'poena' ?></span></h6>

													<?php if ($p['tip_pitanja'] === 'dopuna' && kviz_ima_oznake_praznina($p['tekst_pitanja'])) {
														$dopuna_blanks = [];
														foreach ($p['odgovori'] as $o) {
															$dopuna_blanks[(int)$o['redosled']] = '<input type="text" class="form-control d-inline-block mx-1" style="width:160px;" name="dopuna[' . (int)$p['id'] . '][' . (int)$o['redosled'] . ']" required>';
														}
													?>
														<p class="mb-2"><?= kviz_render_tekst_sa_oznakama($p['tekst_pitanja'], $dopuna_blanks) ?></p>
													<?php } elseif ($p['tip_pitanja'] === 'spajanje' && kviz_ima_oznake_praznina($p['tekst_pitanja'])) {
														$spajanje_blanks = [];
														for ($n = 1; $n <= 5; $n++) {
															$spajanje_blanks[$n - 1] = '<span class="badge bg-secondary">' . $n . '</span><span class="text-decoration-underline">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
														}
													?>
														<p class="mb-2"><?= kviz_render_tekst_sa_oznakama($p['tekst_pitanja'], $spajanje_blanks) ?></p>
													<?php } else { ?>
														<p><?= nl2br(htmlspecialchars($p['tekst_pitanja'])) ?></p>
													<?php } ?>

													<?php if ($p['tip_pitanja'] === 'dopuna') { ?>
														<?php if (!kviz_ima_oznake_praznina($p['tekst_pitanja'])) { ?>
															<?php foreach ($p['odgovori'] as $o) { ?>
																<div class="mb-2">
																	<label class="form-label">Dopuna <?= (int)$o['redosled'] + 1 ?>:</label>
																	<input type="text" class="form-control" name="dopuna[<?= (int)$p['id'] ?>][<?= (int)$o['redosled'] ?>]" required>
																</div>
															<?php } ?>
														<?php } ?>
													<?php } elseif ($p['tip_pitanja'] === 'spajanje') {
														preg_match_all('/\[([1-5])\]/', $p['tekst_pitanja'], $sp_m);
														$sp_brojevi = array_values(array_unique(array_map('intval', $sp_m[1])));
														sort($sp_brojevi);
													?>
														<p class="text-muted small mb-2">Za svaku stavku ispod izaberi broj praznine kojoj odgovara (ili ostavi "-" ako se ne uklapa).</p>
														<?php foreach ($p['odgovori'] as $o) { ?>
															<div class="d-flex align-items-center gap-2 mb-1">
																<select class="form-select form-select-sm" style="max-width:90px;" name="spajanje[<?= (int)$p['id'] ?>][<?= (int)$o['id'] ?>]">
																	<option value="">-</option>
																	<?php foreach ($sp_brojevi as $b) { ?>
																		<option value="<?= $b ?>"><?= $b ?></option>
																	<?php } ?>
																</select>
																<div><?= htmlspecialchars($o['tekst_odgovora']) ?></div>
															</div>
														<?php } ?>
													<?php } else { ?>
														<?php foreach ($p['odgovori'] as $o) { ?>
															<div class="form-check">
																<input class="form-check-input" type="radio" name="odgovor[<?= (int)$p['id'] ?>]" value="<?= (int)$o['id'] ?>" id="odg_<?= (int)$o['id'] ?>" required>
																<label class="form-check-label" for="odg_<?= (int)$o['id'] ?>"><?= htmlspecialchars($o['tekst_odgovora']) ?></label>
															</div>
														<?php } ?>
													<?php } ?>
												</div>
											</div>
										<?php } ?>

										<div class="d-flex justify-content-between mt-3">
											<button type="button" id="kvizPrev" class="btn btn-outline-secondary" disabled><i class='bx bx-chevron-left'></i> Prethodno</button>
											<button type="button" id="kvizNext" class="btn btn-primary">Sledeće <i class='bx bx-chevron-right'></i></button>
											<button type="submit" id="kvizPredaj" class="btn btn-success" style="display:none;"><i class='bx bx-check-circle'></i> Predaj kviz</button>
										</div>
									</form>

								<?php } elseif ($otvoreni_kviz && $otvoreni_rezultat !== null) {

									$procenat = $otvoreni_pokusaj['procenat'];
									$boja_alert = $procenat >= 50 ? 'alert-success' : 'alert-danger';
								?>

									<!-- REZULTAT POSLEDNJEG POKUSAJA -->
									<div class="card shadow-sm border-0 mb-3">
										<div class="card-body">
											<h5><?= htmlspecialchars($otvoreni_kviz['naziv']) ?></h5>
											<div class="alert <?= $boja_alert ?> mt-2 mb-0">
												Rezultat: <strong><?= (int)$otvoreni_pokusaj['bodovi'] ?> / <?= (int)$otvoreni_pokusaj['max_bodovi'] ?></strong> poena
												(<?= $procenat ?>%)
											</div>
										</div>
									</div>

									<?php if ($otvoreni_kviz['prikazi_odgovore']) { ?>
										<?php foreach ($otvoreni_rezultat_grupisano as $i => $g) { ?>
											<?php if ($g['tip_pitanja'] === 'dopuna') {
												$svi_tacni = true;
												foreach ($g['stavke'] as $s) {
													if (!$s['tacan']) { $svi_tacni = false; break; }
												}
											?>
												<div class="card shadow-sm border-0 mb-2 <?= $svi_tacni ? 'border-start border-success border-3' : 'border-start border-danger border-3' ?>">
													<div class="card-body">
														<strong>Pitanje <?= $i + 1 ?>.</strong>

														<?php if (kviz_ima_oznake_praznina($g['tekst_pitanja'])) {
															$dopuna_prikaz_blanks = [];
															foreach ($g['stavke'] as $bi => $s) {
																$dopuna_prikaz_blanks[$bi] = '<span class="badge bg-secondary">' . ($bi + 1) . '</span> <span class="' . ($s['tacan'] ? 'text-success fw-bold' : 'text-danger fw-bold text-decoration-underline') . '">'
																	. (($s['unet_tekst'] !== null && trim($s['unet_tekst']) !== '') ? htmlspecialchars($s['unet_tekst']) : '—')
																	. '</span>';
															}
															echo kviz_render_tekst_sa_oznakama($g['tekst_pitanja'], $dopuna_prikaz_blanks);
														} else {
															echo nl2br(htmlspecialchars($g['tekst_pitanja']));
														} ?>

														<?php foreach ($g['stavke'] as $bi => $s) {
															$tacan_prikaz = trim(explode('|', (string)$s['tekst_odgovora'])[0]);
														?>
															<div class="mt-2">
																Dopuna <?= $bi + 1 ?>:
																<?php if ($s['unet_tekst'] !== null && trim($s['unet_tekst']) !== '') { ?>
																	<?= htmlspecialchars($s['unet_tekst']) ?>
																<?php } else { ?>
																	<em>(nije odgovoreno)</em>
																<?php } ?>
																<?= $s['tacan'] ? "<span class='text-success'>&#10003;</span>" : "<span class='text-danger'>&#10007;</span>" ?>
																<span class="badge bg-secondary"><?= $s['tacan'] ? (int)$s['odgovor_poeni'] : 0 ?> / <?= (int)$s['odgovor_poeni'] ?> <?= ((int)$s['odgovor_poeni'] === 1) ? 'poen' : 'poena' ?></span>
																<?php if (!$s['tacan']) { ?>
																	<div class="text-success">Tačan odgovor: <?= htmlspecialchars($tacan_prikaz) ?></div>
																<?php } ?>
															</div>
														<?php } ?>
													</div>
												</div>
											<?php } elseif ($g['tip_pitanja'] === 'spajanje') {
												$svi_tacni = true;
												foreach ($g['stavke'] as $s) {
													if ($s['odgovor_tacan_broj'] !== null && !$s['tacan']) { $svi_tacni = false; break; }
												}
											?>
												<div class="card shadow-sm border-0 mb-2 <?= $svi_tacni ? 'border-start border-success border-3' : 'border-start border-danger border-3' ?>">
													<div class="card-body">
														<strong>Pitanje <?= $i + 1 ?>.</strong>

														<?php if (kviz_ima_oznake_praznina($g['tekst_pitanja'])) {
															$spajanje_prikaz_blanks = [];
															for ($n = 1; $n <= 5; $n++) {
																$spajanje_prikaz_blanks[$n - 1] = '<span class="badge bg-secondary">' . $n . '</span><span class="text-decoration-underline">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
															}
															echo kviz_render_tekst_sa_oznakama($g['tekst_pitanja'], $spajanje_prikaz_blanks);
														} else {
															echo nl2br(htmlspecialchars($g['tekst_pitanja']));
														} ?>

														<?php foreach ($g['stavke'] as $s) { ?>
															<div class="mt-2">
																<?= htmlspecialchars($s['tekst_odgovora']) ?> &rarr;
																<?php if ($s['unet_tekst'] !== null && trim($s['unet_tekst']) !== '') { ?>
																	<strong><?= htmlspecialchars($s['unet_tekst']) ?></strong>
																<?php } else { ?>
																	<em>(nije povezano)</em>
																<?php } ?>
																<?php if ($s['odgovor_tacan_broj'] !== null) { ?>
																	<?= $s['tacan'] ? "<span class='text-success'>&#10003;</span>" : "<span class='text-danger'>&#10007;</span>" ?>
																	<span class="badge bg-secondary"><?= $s['tacan'] ? (int)$s['odgovor_poeni'] : 0 ?> / <?= (int)$s['odgovor_poeni'] ?> <?= ((int)$s['odgovor_poeni'] === 1) ? 'poen' : 'poena' ?></span>
																	<?php if (!$s['tacan']) { ?>
																		<span class="text-success">Tačan broj: <?= (int)$s['odgovor_tacan_broj'] ?></span>
																	<?php } ?>
																<?php } else { ?>
																	<span class="text-muted">(mamac)</span>
																<?php } ?>
															</div>
														<?php } ?>
													</div>
												</div>
											<?php } else {
												$r = $g['stavke'][0];
											?>
												<div class="card shadow-sm border-0 mb-2 <?= $r['tacan'] ? 'border-start border-success border-3' : 'border-start border-danger border-3' ?>">
													<div class="card-body">
														<strong>Pitanje <?= $i + 1 ?>.</strong> <?= nl2br(htmlspecialchars($r['tekst_pitanja'])) ?>
														<div class="mt-2">
															Tvoj odgovor:
															<?php if ($r['tekst_odgovora']) { ?>
																<?= htmlspecialchars($r['tekst_odgovora']) ?>
															<?php } else { ?>
																<em>(nije odgovoreno)</em>
															<?php } ?>
															<?= $r['tacan'] ? "<span class='text-success'>&#10003;</span>" : "<span class='text-danger'>&#10007;</span>" ?>
														</div>
														<?php if (!$r['tacan']) { ?>
															<div class="text-success">Tačan odgovor: <?= htmlspecialchars($r['tacan_tekst']) ?></div>
														<?php } ?>
													</div>
												</div>
											<?php } ?>
										<?php } ?>
									<?php } ?>

									<div class="mt-3 d-flex gap-2 flex-wrap">
										<?php if ($otvoreni_kviz['moze_pokusaj']) { ?>
											<form method="post">
												<input type="hidden" name="kviz_action" value="pokreni">
												<input type="hidden" name="kviz_id" value="<?= (int)$otvoreni_kviz['id'] ?>">
												<button type="submit" class="btn btn-primary"><i class='bx bx-refresh'></i> Pokušaj ponovo</button>
											</form>
										<?php } ?>

										<a href="index.php?tab=kviz#tab-kviz" class="btn btn-outline-secondary"><i class='bx bx-arrow-back'></i> Nazad na listu kvizova</a>
									</div>

								<?php } else { ?>

									<!-- LISTA DOSTUPNIH KVIZOVA -->
									<?php if (empty($kvizovi_lista)) { ?>
										<div class="alert alert-info">Nema dostupnih kvizova.</div>
									<?php } else { ?>
										<div class="row">
											<?php foreach ($kvizovi_lista as $kv) { ?>
												<div class="col-12 col-md-6 col-lg-4 mb-3">
													<div class="card shadow-sm border-0 h-100">
														<div class="card-body d-flex flex-column">
															<strong><?= htmlspecialchars($kv['naziv']) ?></strong>

															<?php if (!empty($kv['opis'])) { ?>
																<div class="text-muted mt-1" style="font-size:13px;"><?= nl2br(htmlspecialchars($kv['opis'])) ?></div>
															<?php } ?>

															<div class="mt-2" style="font-size:13px;">
																📝 <?= (int)$kv['broj_pitanja'] ?> <?= $kv['broj_pitanja'] == 1 ? 'pitanje' : 'pitanja' ?>
															</div>

															<?php if (!empty($kv['vreme_ogranicenje_min'])) { ?>
																<div style="font-size:13px;">⏱️ Vremensko ograničenje: <?= (int)$kv['vreme_ogranicenje_min'] ?> min</div>
															<?php } ?>

															<?php if (!empty($kv['rok'])) { ?>
																<div style="font-size:13px; <?= $kv['rok_istekao'] ? 'color:#dc3545;' : '' ?>">
																	⏳ Rok: <?= date('d.m.Y H:i', strtotime($kv['rok'])) ?>
																	<?= $kv['rok_istekao'] ? ' (istekao)' : '' ?>
																</div>
															<?php } ?>

															<div style="font-size:13px;">
																🔁 Pokušaji: <?= count($kv['zavrseni']) ?> / <?= (int)$kv['broj_pokusaja'] ?>
															</div>

															<div class="mt-3">
																<?php if ($kv['u_toku']) { ?>
																	<a href="index.php?tab=kviz&kviz=<?= (int)$kv['id'] ?>#tab-kviz" class="btn btn-warning btn-sm">
																		<i class='bx bx-play-circle'></i> Nastavi kviz
																	</a>
																<?php } elseif ($kv['moze_pokusaj']) { ?>
																	<form method="post">
																		<input type="hidden" name="kviz_action" value="pokreni">
																		<input type="hidden" name="kviz_id" value="<?= (int)$kv['id'] ?>">
																		<button type="submit" class="btn btn-primary btn-sm">
																			<i class='bx bx-help-circle'></i> Pokreni kviz
																		</button>
																	</form>
																<?php } elseif (!empty($kv['zavrseni'])) { ?>
																	<a href="index.php?tab=kviz&kviz=<?= (int)$kv['id'] ?>#tab-kviz" class="btn btn-outline-secondary btn-sm">
																		<i class='bx bx-bar-chart-alt-2'></i> Pregled rezultata
																	</a>
																<?php } else { ?>
																	<span class="badge bg-secondary">Rok istekao</span>
																<?php } ?>
															</div>

														</div>
													</div>
												</div>
											<?php } ?>
										</div>
									<?php } ?>

								<?php } ?>

							</div>

						</div>

			</div>
		</div>
			</div>
		<!--end page wrapper -->
		<!--start overlay-->
		 <div class="overlay toggle-icon"></div>
		<!--end overlay-->
		<!--Start Back To Top Button-->
		  <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
		<!--End Back To Top Button-->
		<footer class="page-footer">
			
            <?php // include __DIR__ . '/app/footer.php'; ?>
		</footer>
	</div>
	<!--end wrapper-->


	<!-- search modal -->
    <div class="modal" id="SearchModal" tabindex="-1">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
		  <div class="modal-content">
			<div class="modal-header gap-2">
			  <div class="position-relative popup-search w-100">
				<input class="form-control form-control-lg ps-5 border border-3 border-primary" type="search" placeholder="Search">
				<span class="position-absolute top-50 search-show ms-3 translate-middle-y start-0 top-50 fs-4"><i class='bx bx-search'></i></span>
			  </div>
			  <button type="button" class="btn-close d-md-none" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="search-list">
				   <p class="mb-1">Html Templates</p>
				   <div class="list-group">
					  <a href="javascript:;" class="list-group-item list-group-item-action active align-items-center d-flex gap-2 py-1"><i class='bx bxl-angular fs-4'></i>Best Html Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-vuejs fs-4'></i>Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-magento fs-4'></i>Responsive Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-shopify fs-4'></i>eCommerce Html Templates</a>
				   </div>
				   <p class="mb-1 mt-3">Web Designe Company</p>
				   <div class="list-group">
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-windows fs-4'></i>Best Html Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-dropbox fs-4' ></i>Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-opera fs-4'></i>Responsive Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-wordpress fs-4'></i>eCommerce Html Templates</a>
				   </div>
				   <p class="mb-1 mt-3">Software Development</p>
				   <div class="list-group">
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-mailchimp fs-4'></i>Best Html Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-zoom fs-4'></i>Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-sass fs-4'></i>Responsive Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-vk fs-4'></i>eCommerce Html Templates</a>
				   </div>
				   <p class="mb-1 mt-3">Online Shoping Portals</p>
				   <div class="list-group">
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-slack fs-4'></i>Best Html Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-skype fs-4'></i>Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-twitter fs-4'></i>Responsive Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-vimeo fs-4'></i>eCommerce Html Templates</a>
				   </div>
				</div>
			</div>
		  </div>
		</div>
	  </div>
    <!-- end search modal -->




	<!--start switcher-->
	<div class="switcher-wrapper" style="display:none;">
		<div class="switcher-btn"> <i class='bx bx-cog bx-spin'></i>
		</div>
		<div class="switcher-body">
			<div class="d-flex align-items-center">
				<h5 class="mb-0 text-uppercase">Theme Customizer</h5>
				<button type="button" class="btn-close ms-auto close-switcher" aria-label="Close"></button>
			</div>
			<hr/>
			<h6 class="mb-0">Theme Styles</h6>
			<hr/>
			<div class="d-flex align-items-center justify-content-between">
				<div class="form-check">
					<input class="form-check-input" type="radio" name="flexRadioDefault" id="lightmode" checked>
					<label class="form-check-label" for="lightmode">Light</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="flexRadioDefault" id="darkmode">
					<label class="form-check-label" for="darkmode">Dark</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="flexRadioDefault" id="semidark">
					<label class="form-check-label" for="semidark">Semi Dark</label>
				</div>
			</div>
			<hr/>
			<div class="form-check">
				<input class="form-check-input" type="radio" id="minimaltheme" name="flexRadioDefault">
				<label class="form-check-label" for="minimaltheme">Minimal Theme</label>
			</div>
			<hr/>
			<h6 class="mb-0">Header Colors</h6>
			<hr/>
			<div class="header-colors-indigators">
				<div class="row row-cols-auto g-3">
					<div class="col">
						<div class="indigator headercolor1" id="headercolor1"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor2" id="headercolor2"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor3" id="headercolor3"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor4" id="headercolor4"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor5" id="headercolor5"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor6" id="headercolor6"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor7" id="headercolor7"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor8" id="headercolor8"></div>
					</div>
				</div>
			</div>
			<hr/>
			<h6 class="mb-0">Sidebar Colors</h6>
			<hr/>
			<div class="header-colors-indigators">
				<div class="row row-cols-auto g-3">
					<div class="col">
						<div class="indigator sidebarcolor1" id="sidebarcolor1"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor2" id="sidebarcolor2"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor3" id="sidebarcolor3"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor4" id="sidebarcolor4"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor5" id="sidebarcolor5"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor6" id="sidebarcolor6"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor7" id="sidebarcolor7"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor8" id="sidebarcolor8"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
 
	<!--end switcher-->
	<!-- Bootstrap JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>
	<!--plugins-->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
	<script src="assets/plugins/metismenu/js/metisMenu.min.js"></script>
	<script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
	<script src="assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js"></script>
	<script src="assets/plugins/chartjs/js/chart.js"></script>
	<!-- <script src="assets/js/index_prof.js"></script>  -->
	<!-- app JS -->
	<script src="assets/js/app.js"></script>
	<script>
		new PerfectScrollbar(".app-container")
	</script>
	<script>
	// Prikaz/sakrivanje iframe-a za video materijale (lazy-load src, da se ne ucitava unapred)
	$(document).on('click', '.toggle-video', function(){

		let id = $(this).data('id');
		let wrap = $('#video_wrap_' + id);
		let iframe = wrap.find('iframe');

		if (wrap.is(':visible')) {
			wrap.hide();
			iframe.attr('src', ''); // zaustavi reprodukciju kad se sakrije
			$(this).html("<i class='bx bx-play-circle'></i> Pogledaj video");
			return;
		}

		if (!iframe.attr('src')) {
			iframe.attr('src', iframe.data('src'));
		}

		wrap.show();
		$(this).html("<i class='bx bx-x-circle'></i> Sakrij video");
	});
	</script>
	<script>
	// Navigacija kroz pitanja kviza - jedno pitanje po ekranu, strelice levo/desno
	var kvizKartice = document.querySelectorAll('.kviz-pitanje-card');
	if (kvizKartice.length > 0) {
		var kvizIndeks = 0;
		var kvizUkupno = kvizKartice.length;
		var kvizPrevBtn = document.getElementById('kvizPrev');
		var kvizNextBtn = document.getElementById('kvizNext');
		var kvizPredajBtn = document.getElementById('kvizPredaj');
		var kvizBrojSpan = document.getElementById('kvizBrojTrenutni');

		var kvizPrikaziPitanje = function (i) {
			kvizKartice.forEach(function (kartica, ci) {
				kartica.style.display = (ci === i) ? '' : 'none';
			});

			kvizBrojSpan.textContent = (i + 1);
			kvizPrevBtn.disabled = (i === 0);

			if (i === kvizUkupno - 1) {
				kvizNextBtn.style.display = 'none';
				kvizPredajBtn.style.display = '';
			} else {
				kvizNextBtn.style.display = '';
				kvizPredajBtn.style.display = 'none';
			}
		};

		kvizPrevBtn.addEventListener('click', function () {
			if (kvizIndeks > 0) {
				kvizIndeks--;
				kvizPrikaziPitanje(kvizIndeks);
			}
		});

		kvizNextBtn.addEventListener('click', function () {
			if (kvizIndeks < kvizUkupno - 1) {
				kvizIndeks++;
				kvizPrikaziPitanje(kvizIndeks);
			}
		});

		kvizPrikaziPitanje(kvizIndeks);
	}
	</script>
	<script>
	// ===== DOMAĆI AUDIO: oznaci slušanje + snimanje odgovora =====
	(function () {

		var prioritetMime = [
			'audio/mp4',
			'audio/webm;codecs=opus',
			'audio/webm',
			'audio/ogg;codecs=opus',
			'audio/ogg'
		];

		function odabereMimeType() {
			if (typeof MediaRecorder === 'undefined') return '';
			for (var i = 0; i < prioritetMime.length; i++) {
				if (MediaRecorder.isTypeSupported(prioritetMime[i])) return prioritetMime[i];
			}
			return '';
		}

		function formatVreme(s) {
			var m = Math.floor(s / 60), sec = s % 60;
			return (m < 10 ? '0' : '') + m + ':' + (sec < 10 ? '0' : '') + sec;
		}

		// State po zadatku
		var stanje = {};

		function getStanje(id) {
			if (!stanje[id]) stanje[id] = { recorder: null, chunks: [], mimeType: '', timer: null, sek: 0 };
			return stanje[id];
		}

		function resetStanje(id) {
			var s = getStanje(id);
			clearInterval(s.timer);
			s.recorder = null; s.chunks = []; s.mimeType = ''; s.timer = null; s.sek = 0;
			$('.djak-playback[data-id="' + id + '"]').hide();
			$('.djak-timer[data-id="' + id + '"]').text('');
			$('.djak-upload-status[data-id="' + id + '"]').html('');
			$('.djak-snimi-btn[data-id="' + id + '"]').show().prop('disabled', false);
			$('.djak-zaustavi-btn[data-id="' + id + '"]').hide().prop('disabled', true);
		}

		// Oznaci slusano - poziva se iz onplay atributa audio elementa
		window.oznaci_slusano = function (audioEl) {
			var id = $(audioEl).data('domaci-id');
			if (!id) return;
			$.post('ajax_oznaci_slusano.php', { domaci_id: id });
		};

		// Dugme SNIMI
		$(document).on('click', '.djak-snimi-btn', function () {
			var id = parseInt($(this).data('id'));
			var s = getStanje(id);

			if (!window.isSecureContext) {
				alert('Za snimanje mikrofona potrebna je HTTPS veza.\nOva stranica je otvorena preko HTTP — obratite se administratoru ili koristite HTTPS.');
				return;
			}
			if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
				alert('Vaš browser ne podržava snimanje mikrofona. Koristite Chrome ili Safari.');
				return;
			}

			if (!window.isSecureContext) {
				alert('Za snimanje mikrofona potrebna je HTTPS veza.\nOva stranica je otvorena preko HTTP — obratite se administratoru ili koristite HTTPS.');
				return;
			}

			navigator.mediaDevices.getUserMedia({ audio: true }).then(function (stream) {
				s.mimeType = odabereMimeType();
				var opcije = s.mimeType ? { mimeType: s.mimeType } : {};
				try {
					s.recorder = new MediaRecorder(stream, opcije);
				} catch (e) {
					s.recorder = new MediaRecorder(stream);
					s.mimeType = s.recorder.mimeType;
				}
				s.chunks = [];

				s.recorder.addEventListener('dataavailable', function (e) {
					if (e.data && e.data.size > 0) s.chunks.push(e.data);
				});

				s.recorder.addEventListener('stop', function () {
					stream.getTracks().forEach(function (t) { t.stop(); });
					clearInterval(s.timer);
					var blob = new Blob(s.chunks, { type: s.mimeType || 'audio/webm' });
					var url = URL.createObjectURL(blob);
					$('.djak-audio-preview[data-id="' + id + '"]').attr('src', url);
					$('.djak-playback[data-id="' + id + '"]').show();
					$('.djak-snimi-btn[data-id="' + id + '"]').show().prop('disabled', false);
					$('.djak-zaustavi-btn[data-id="' + id + '"]').hide().prop('disabled', true);
					$('.djak-timer[data-id="' + id + '"]').text('');
				});

				s.recorder.start(1000);
				s.sek = 0;
				$('.djak-timer[data-id="' + id + '"]').text(formatVreme(0));
				s.timer = setInterval(function () {
					s.sek++;
					$('.djak-timer[data-id="' + id + '"]').text(formatVreme(s.sek));
				}, 1000);

				$('.djak-snimi-btn[data-id="' + id + '"]').hide();
				$('.djak-zaustavi-btn[data-id="' + id + '"]').show().prop('disabled', false);
				$('.djak-playback[data-id="' + id + '"]').hide();

			}).catch(function (err) {
				alert('Ne mogu da pristupim mikrofonu: ' + err.message);
			});
		});

		// Dugme ZAUSTAVI
		$(document).on('click', '.djak-zaustavi-btn', function () {
			var id = parseInt($(this).data('id'));
			var s = getStanje(id);
			if (s.recorder && s.recorder.state !== 'inactive') s.recorder.stop();
		});

		// Dugme PONOVI
		$(document).on('click', '.djak-reset-btn', function () {
			var id = parseInt($(this).data('id'));
			resetStanje(id);
		});

		// Dugme POŠALJI
		$(document).on('click', '.djak-posalji-btn', function () {
			var id = parseInt($(this).data('id'));
			var s = getStanje(id);
			var $btn = $(this);
			var $status = $('.djak-upload-status[data-id="' + id + '"]');

			if (!s.chunks || s.chunks.length === 0) {
				alert('Nema snimka za slanje.');
				return;
			}

			$btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
			$status.html('');

			var ext = 'webm';
			if (s.mimeType.indexOf('mp4') !== -1) ext = 'mp4';
			else if (s.mimeType.indexOf('ogg') !== -1) ext = 'ogg';

			var blob = new Blob(s.chunks, { type: s.mimeType || 'audio/webm' });
			var formData = new FormData();
			formData.append('audio', blob, 'odgovor.' + ext);
			formData.append('domaci_id', id);

			$.ajax({
				url: 'upload_djak_audio.php',
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				success: function (resp) {
					try {
						var data = typeof resp === 'string' ? JSON.parse(resp) : resp;
						if (data.error) {
							$status.html('<div class="alert alert-danger p-1 mb-0 small">' + data.error + '</div>');
							$btn.prop('disabled', false).html('<i class="bx bx-send"></i> Pošalji odgovor');
							return;
						}
						$status.html('<div class="alert alert-success p-1 mb-0 small"><i class="bx bx-check"></i> Odgovor je poslat!</div>');
						$btn.prop('disabled', true).html('<i class="bx bx-check-circle"></i> Poslato');
					} catch (e) {
						$status.html('<div class="alert alert-danger p-1 mb-0 small">Greška pri čitanju odgovora.</div>');
						$btn.prop('disabled', false).html('<i class="bx bx-send"></i> Pošalji odgovor');
					}
				},
				error: function () {
					$status.html('<div class="alert alert-danger p-1 mb-0 small">Greška pri komunikaciji.</div>');
					$btn.prop('disabled', false).html('<i class="bx bx-send"></i> Pošalji odgovor');
				}
			});
		});

	}());
	</script>
	<script>
	// Odbrojavanje za vremenski ogranicen kviz - automatska predaja kada vreme istekne
	var kvizOdbrojavanje = document.getElementById('kvizOdbrojavanje');
	if (kvizOdbrojavanje) {
		var kvizPreostalo = parseInt(kvizOdbrojavanje.dataset.preostalo, 10);

		var kvizPrikaziVreme = function () {
			var min = Math.floor(kvizPreostalo / 60);
			var sek = kvizPreostalo % 60;
			document.getElementById('kvizVreme').textContent = min + ':' + (sek < 10 ? '0' : '') + sek;
		};

		kvizPrikaziVreme();

		var kvizTajmer = setInterval(function () {
			kvizPreostalo--;

			if (kvizPreostalo <= 0) {
				clearInterval(kvizTajmer);
				document.getElementById('formaKviz').submit();
				return;
			}

			kvizPrikaziVreme();
		}, 1000);
	}
	</script>
</body>

</html>